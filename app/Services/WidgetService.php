<?php

namespace App\Services;

use App\Services\Interfaces\WidgetServiceInterface;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;


/**
 * Class WidgetService
 * @package App\Services
 */
class WidgetService extends BaseService implements WidgetServiceInterface
{
    protected $widgetRepository;

    public function __construct(WidgetRepository $widgetRepository)
    {
        $this->widgetRepository = $widgetRepository;
    }
    public function paginate($request)
    {
        $condition['keyword']= addslashes($request->input('keyword'));
        $condition['publish']= $request->integer('publish');
        $perPage = $request->integer('perpage');
        $widgets = $this->widgetRepository->pagination
        (
            $this->paginateSelect(), 
            $condition, 
            $perPage, 
            ['path'=>'widget/index'], 
        );
        return $widgets;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $payload =$request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            $widget = $this->widgetRepository->create($payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{

            $payload =$request->only('name', 'keyword', 'short_code', 'description', 'album', 'model');
            $payload['model_id'] = $request->input('modelItem.id');
            $payload['description'] = [
                $languageId => $payload['description']
            ];
            // dd($payload);
            $widget = $this->widgetRepository->update($id, $payload);
            
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
            $widget = $this->widgetRepository->delete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            echo $e->getMessage();die();
            return false;
        }
    }

    private function paginateSelect(){
        return [
            'id',
            'name',
            'keyword',
            'short_code',
            'description',
            'publish',
        ];
    }

}
