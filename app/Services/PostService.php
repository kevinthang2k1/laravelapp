<?php

namespace App\Services;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


/**
 * Class PostService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    protected $language;


    public function __construct(
        PostRepository $postRepository,
    ){
        $this->language = $this->currentLanguage();
        $this->postRepository = $postRepository;
    }

    public function paginate($request)
    {
        $condition['keyword']= addslashes($request->input('keyword'));
        $condition['publish']= $request->integer('publish');
        $condition['post_catalogue_id'] = $request->input('post_catalogue_id');
        $condition['where']= [
            ['tb2.language_id', '=', $this->language],
        ];

        $perPage = $request->integer('perpage');
        $posts = $this->postRepository->pagination
        (
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['posts.id', 'DESC'],
            ['path' => 'post.index', 'grouBy' => $this->paginateSelect()],
            [
                ['post_language as tb2','tb2.post_id', '=' ,'posts.id'],
                // ['post_catalogue_post as tb3','posts.id', '=' ,'tb3.post_id'],

            ],            
        );
        return $posts;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload =$request->only($this->payload());
            $payload['user_id'] = Auth::id();
            $payload['album'] = (isset($payload['album']) && !empty($payload['album'])) ? json_encode($payload['album']) : '';
            $post = $this->postRepository->create($payload);
            if($post->id >0){
                $payloadLanguage = $request->only($this->payloadLanguage());
                $payloadLanguage['canonical'] = Str::slug($payloadLanguage['canonical']);
                $payloadLanguage['language_id'] = $this->currentLanguage();
                $payloadLanguage['post_id'] = $post->id;
                $language = $this->postRepository->createPivot($post, $payloadLanguage, 'languages');
                $catalogue = $this->catalogue($request); 
                $post->post_catalogues()->sync($catalogue);
            }
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
            $post = $this->postRepository->findById($id);
             
            if($this->uploadPost($post, $request)){
                $this->uploadLanguageForPost($post, $request);

                $post->languages()->detach([$payloadLanguage['language_id'], $id]);
                $response =
                $catalogue = $this->catalogue($request); 
                $post->post_catalogues()->sync($catalogue);
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
            $post = $this->postRepository->delete($id);
 
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // echo $e->getMessage();die();
            return false;
        }
    }

    private function uploadPost($post, $request){
        $payload =$request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return $this->postRepository->update($post->id, $payload);
    }

    private function updateForPost($post, $request){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $request);
        $post->languages()->detach([$this->language, $post->id]);
        return $this->postRepository->createPivot($post, $payloadLanguage,'languages');
    }

    private function formatLanguagePayload($payload, $postId){
        $payload['canonical'] = Str::slug($payloadLanguage['canonical']);
        $payload['language_id'] = $this->currentLanguage();
        $payload['post_id'] = $postId;
        return $payload;
    }

    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'), [$request->post_catalogue_id]));
        }
        return [$request->post_catalogue_id];
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $post = $this->postRepository->update($post['modelId'], $payload);

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
            $flag = $this->postRepository->updateByWhereIn('id', $post['id'], $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('post_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.post_catalogue_id IN (
                        SELECT id
                        FROM post_catalogues
                        WHERE lft >= (SELECT lft FROM post_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM post_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('post_catalogue_id'), $request->integer('post_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function paginateSelect(){
        return [
            'posts.id',
            'posts.publish',
            'posts.image',
            'posts.order',
            'tb2.name',
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'follow', 
            'publish', 
            'image',
            'album',
            'post_catalogue_id',
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
