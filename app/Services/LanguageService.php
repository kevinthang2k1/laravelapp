<?php

namespace App\Services;

use App\Services\Interfaces\LanguageServiceInterface;

use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;



/**
 * Class LanguageService
 * @package App\Services
 */
class LanguageService implements LanguageServiceInterface
{
    protected $languageRepository;


    public function __construct(
        LanguageRepository $languageRepository,

    ){
        $this->LanguageRepository = $languageRepository;

    }

    public function paginate($request)
    {
        $condition['keyword']= addslashes($request->input('keyword'));
        $condition['publish']= $request->integer('publish');

        $perPage = $request->integer('perpage');
        $languages = $this->LanguageRepository->pagination
        (
            $this->paginateSelect(),
            $condition,
            $perPage, 
            ['path'=>'language/index'],
        );
        // dd($languages);
        return $languages;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload =$request->except('_token','send');
            $payload['user_id'] = Auth::id();
            // dd($payload);
            $language = $this->LanguageRepository->create($payload);
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

            $payload =$request->except('_token','send');
            $language = $this->LanguageRepository->update($id, $payload);
            
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
            $language = $this->LanguageRepository->delete($id);
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
            $language = $this->LanguageRepository->update($post['modelId'], $payload);
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
            $flag = $this->LanguageRepository->updateByWhereIn('id', $post['id'], $payload);
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

    // private function changeUserStatus($post, $value){
    //     DB::beginTransaction();
    //     try{
    //         $array = [];
    //         if(isset($post['modelId'])){
    //             $array[] = $post['modelId'];
    //         }else{
    //             $array = $post['id'];
    //         }
    //         $payload[$post['field']] = $value;
    //         // dd($payload);
    //         $this->userRepository->updateByWhereIn('user_catalogue_id', $array, $payload);
    //         // echo 123;die();
    //         DB::commit();
    //         return true;
    //     }catch(\Exception $e ){
    //         DB::rollBack();
    //         // Log::error($e->getMessage());
    //         echo $e->getMessage();die();
    //         return false;
    //     }
    // }

    private function paginateSelect(){
        return [
            'id',
            'name',
            'canonical',
            'publish',
            'image',
        ];
    }

}
