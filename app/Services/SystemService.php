<?php

namespace App\Services;
use App\Services\Interfaces\SystemServiceInterface;
use App\Repositories\Interfaces\SystemRepositoryInterface as SystemRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Class SystemService
 * @package App\Services
 */
class SystemService implements SystemServiceInterface
{
    protected $systemRepository;

    public function __construct(
        SystemRepository $systemRepository
    ){
        $this->systemRepository = $systemRepository;
    }
 //đang ko lưu được giới thiệu ngắn khi dùng ck-editor 
    public function save($request, $languageId){
        DB::beginTransaction();
        try{

            $config = $request->input('config');
            // dd($config);
            $payload = [];
            if(count($config)){
                foreach($config as $key => $val){
                    $payload = [
                        'keyword' => $key,
                        'content' => $val,
                        'language_id' => $languageId,
                        'user_id'=> Auth::id(),
                    ];                    
                    $condition = ['keyword' => $key, 'language_id' => $languageId];
                    $this->systemRepository->updateOrInsert($payload, $condition);
                }
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

}
