<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;

/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuRepository;
    protected $nestedset;
    protected $menuCatalogueRepository;

    
    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository,

    ){
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }

    public function initialize($languageId){
        $this->nestedset = new Nestedsetbie([
                    'table' => 'menus',
                    'foreignkey' => 'menu_id',
                    'isMenu' => TRUE,
                    'language_id' => $languageId,
                ]);
    }

    public function paginate($request, $languageId){
        
        return [];
    }

    public function save($request, $languageId){
        DB::beginTransaction();
        try{
            $payload = $request->only('menu', 'menu_catalogue_id', 'type');
            // dd($payload);
            if(count($payload['menu']['name'])){
                foreach($payload['menu']['name'] as $key => $val){
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id'=> $payload['menu_catalogue_id'],
                        // 'type' => $payload['type'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id(),
                    ];
                    // dd($menuArray);
                    if($menuId == 0){
                        $menuSave = $this->menuRepository->create($menuArray);
                    }else{
                        $menuSave = $this->menuRepository->update($menuId ,$menuArray);
                        if($menuSave->rgt - $menuSave->lft>1){
                            $this->menuRepository->updateByWhere(
                                [
                                    ['lft', '>', $menuSave->lft],
                                    ['rgt', '<', $menuSave->rgt],
                                ],['menu_catalogue_id' => $payload['menu_catalogue_id']]
                            );
                        }
                    }
                    // dd($menuSave->toArray());
                    // $menu = $this->menuRepository->create($menuArray);
                    if($menuSave->id > 0){
                        $menuSave->languages()->detach([$this->language, $menuSave->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }        
                $this->initialize($languageId);
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

    public function saveChildren($request, $languageId, $menu){
        DB::beginTransaction();
        try{
            $payload = $request->only('menu');
            // dd($payload);
            if(count($payload['menu']['name'])){
                foreach($payload['menu']['name'] as $key => $val){
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id'=> $menu->menu_catalogue_id,
                        'parent_id' => $menu->id,
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id(),
                    ];
                    if($menuId == 0){
                        $menuSave = $this->menuRepository->create($menuArray);
                    }else{
                        $menuSave = $this->menuRepository->update($menuId ,$menuArray);
                    }
                    // dd($menuSave);
                    if($menuSave->id > 0){
                        $menuSave->languages()->detach([$this->language, $menuSave->id]);
                        $payloadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menuSave, $payloadLanguage, 'languages');
                    }
                }
                $this->initialize($languageId);
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

    public function dargUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 1, $parentId = 0){
        if(count($json)){
            foreach($json as $key => $val){
                $update = [
                    'order' => count($json) - $key,
                    'parent_id' => $parentId, 
                ];
                // dd($update);
                $menu = $this->menuRepository->update($val['id'], $update);
                if(isset($val['children']) && count($val['children'])){
                    $this->dargUpdate($val['children'], $menuCatalogueId, $languageId,$val['id']);
                }
            }
        }
        $this->initialize($languageId);
        $this->nestedset(); 
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $this->menuRepository->forceDeleteByCondition([
                ['menu_catalogue_id', '=', $id],
            ]);
            $this->menuCatalogueRepository->forceDelete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function getAndConvertMenu($menu = null, $language = 1):array{
        $menuList = $this->menuRepository->findByCondition([
            ['parent_id', '=', $menu->id]
        ], TRUE, [
            'languages' => function($query) use ($language){
                $query->where('language_id', $language);
            }
        ]);
        // dd($menuList);
        return $this->convertMenu($menuList);  
    }

    public function convertMenu($menuList = null){
        $temp = [];
        $fields = ['name', 'canonical', 'order', 'id'];
        if(count($menuList)){
            foreach($menuList as $key => $val){
                foreach($fields as $field){
                    if($field == 'name' || $field == 'canonical'){
                        $temp[$field][] = $val->languages->first()->pivot->{$field};
                    }else{
                        $temp[$field][] = $val->{$field};
                    }
                }
            }                 
        }
        return $temp;
    }

}
