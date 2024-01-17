<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;


/**
 * Class PostCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $routerRepository;
    protected $nestedset;
    protected $language;


    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        RouterRepository $routerRepository,

    ){
        $this->language = $this->currentLanguage();
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->language,
        ]);

    }

    public function paginate($request)
    {
        $condition['keyword']= addslashes($request->input('keyword'));
        $condition['publish']= $request->integer('publish');
        $condition['where']= [
            ['tb2.language_id', '=', $this->language]
        ];

        $perPage = $request->integer('perpage');
        $postCatalogues = $this->postCatalogueRepository->pagination
        (
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['post_catalogues.lft', 'ASC'],
            ['path' => 'post.catalogue.index'],
            [
                ['post_catalogue_language as tb2','tb2.post_catalogue_id', '=' ,'post_catalogues.id']
            ],            
        );
        return $postCatalogues;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $postCatalogue = $this->createCatalogue($request);

            if($postCatalogue->id >0){
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->currentLanguage();
                $payloadLanguage['post_catalogue_id'] = $postCatalogue->id;
                $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payloadLanguage, 'languages');
                
                $router = [
                    'canonical' => $payloadLanguage['canonical'],
                    'module_id' => $postCatalogue->id,
                    'controllers' => 'App\Http\Controllers\Frontend\PostCatalogue',
                ];
                // dd($router);
                $condition = [
                    ['module_id', '=', $id],
                    ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogue'],

                ];
                $this->routerRepository->create($router);
            }
            
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $postCatalogue = $this->postCatalogueRepository->findById($id);
            
            $payload =$request->only($this->payload());
            if(isset($payload['album'])){
                $payload['album'] = json_encode($payload['album']);
            }            
            $flag = $this->postCatalogueRepository->update($id, $payload); 
            if($flag == TRUE){
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['language_id'] = $this->currentLanguage();
                $payloadLanguage['post_catalogue_id'] = $id;
                $postCatalogue->languages()->detach([$payloadLanguage['language_id'], $id]);
                $response = $this->postCatalogueRepository->createPivot($postCatalogue, $payloadLanguage, 'languages');
                $payloadRouter = [
                    'canonical' => $payloadLanguage['canonical'],
                    'module_id' => $postCatalogue->id,
                    'controllers' => 'App\Http\Controllers\Frontend\PostCatalogue',
                ];

                $condition = [
                    ['module_id', '=', $id],
                    ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogue'],

                ];
                $router = $this->routerRepository->findByCondition($condition);
                $this->routerRepository->update($router->id, $payloadRouter);
                
                $this->nestedset->Get('level ASC, order ASC');
                $this->nestedset->Recursive(0, $this->nestedset->Set());
                $this->nestedset->Action();
            }

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
       
        DB::beginTransaction();
        try{ 
            $postCatalogue = $this->postCatalogueRepository->delete($id);
            $this->nestedset->Get('level ASC, order ASC');
            $this->nestedset->Recursive(0, $this->nestedset->Set());
            $this->nestedset->Action();
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $postCatalogue = $this->postCatalogueRepository->update($post['modelId'], $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = $post['value'];
            $flag = $this->postCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function createCatalogue($request){
        $payload =$request->only($this->payload());
            $payload['user_id'] = Auth::id();
            if(isset($payload['album'])){
                $payload['album'] = json_encode($payload['album']);
            }
            $postCatalogue = $this->postCatalogueRepository->create($payload);
    }

    private function formatAlbum($request){
        return ($request->input('album') && !empty($request->input('album'))) ? json_encode($request->input('album')) : '';
    }

    private function paginateSelect(){
        return [
            'post_catalogues.id',
            'post_catalogues.publish',
            'post_catalogues.image',
            'post_catalogues.level',
            'post_catalogues.order',
            'tb2.name',
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'parent_id', 
            'follow', 
            'publish', 
            'image',
            'album',
        ];
    }

    private function payloadLanguage(){
        return [
            'name', 
            'description',
            'content', 
            'meta_title', 
            'meta_keyword', 
            'meta_description',
            'canonical'
        ];
    }
}
