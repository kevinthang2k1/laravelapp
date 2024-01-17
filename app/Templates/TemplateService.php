<?php

namespace App\Services;

use App\Services\Interfaces\{Module}ServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\{Module}RepositoryInterface as {Module}Repository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
use Illuminate\Support\Str;

/**
 * Class {Module}Service
 * @package App\Services
 */
class {Module}Service extends BaseService implements {Module}ServiceInterface
{


    protected ${module}Repository;
    protected $routerRepository;
    protected $nestedset;
    protected $language;
    protected $controllerName = '{Module}Controller';
    

    public function __construct(
        {Module}Repository ${module}Repository,
        RouterRepository $routerRepository,
    ){
        $this->{module}Repository = ${module}Repository;
        $this->routerRepository = $routerRepository;
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId]
            ]
        ];
        ${module}s = $this->{module}Repository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            ['path' => 'post.catalogue.index'],  
            ['{tableName}.lft', 'ASC'],
            [
                ['post_catalogue_language as tb2','tb2.{foreingKey}', '=' , '{tableName}.id']
            ], 
            ['languages']
        );

        return ${module}s;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            ${module} = $this->createCatalogue($request);
            if(${module}->id > 0){
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->createRouter(${module}, $request, $this->controllerName, $languageId);
                $this->nestedset = new Nestedsetbie([
                    'table' => '{tableName}',
                    'foreignkey' => '{foreingKey}',
                    'language_id' =>  $languageId ,
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{
            ${module} = $this->{module}Repository->findById($id);
            $flag = $this->updateCatalogue(${module}, $request);
            if($flag == TRUE){
                $this->updateLanguageForCatalogue(${module}, $request, $languageId);
                $this->updateRouter(
                    ${module}, $request, $this->controllerName, $languageId
                );
                $this->nestedset = new Nestedsetbie([
                    'table' => '{tableName}',
                    'foreignkey' => 'post_catalogue_id',
                    'language_id' =>  $languageId ,
                ]);
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            ${module} = $this->{module}Repository->delete($id);
            $this->routerRepository->deleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\{module}Controller'],
            ]);

            $this->nestedset = new Nestedsetbie([
                'table' => '{tableName}',
                'foreignkey' => '{foreingKey}',
                'language_id' =>  $languageId ,
            ]);
            $this->nestedset();

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
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['user_id'] = Auth::id();
        ${module} = $this->{module}Repository->create($payload);
        return ${module};
    }

    private function updateCatalogue(${module}, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->{module}Repository->update(${module}->id, $payload);
        return $flag;
    }

    private function updateLanguageForCatalogue(${module}, $request, $languageId){
        $payload = $this->formatLanguagePayload(${module}, $request, $languageId);
        ${module}->languages()->detach([$languageId, ${module}->id]);
        $language = $this->{module}Repository->createPivot(${module}, $payload, 'languages');
        return $language;
    }

    private function formatLanguagePayload(${module}, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['post_catalogue_id'] = ${module}->id;
        return $payload;
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            ${module} = $this->{module}Repository->update($post['modelId'], $payload);
            // $this->changeUserStatus($post, $payload[$post['field']]);

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
            $flag = $this->{module}Repository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeUserStatus($post, $post['value']);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
            '{tableName}.id', 
            '{tableName}.publish',
            '{tableName}.image',
            '{tableName}.level',
            '{tableName}.order',
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
