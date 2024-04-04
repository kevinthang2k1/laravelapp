<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Services\Interfaces\MenuServiceInterface as MenuService; 
use App\Services\Interfaces\MenuCatalogueServiceInterface  as MenuCatalogueService;
use App\Models\Language;
use App\Http\Requests\StoreMenuCatalogueRequest;


class MenuController extends Controller
{
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;
    protected $language;
    protected $menuService;


    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuCatalogueService $menuCatalogueService,
        MenuService $menuService,


    ){
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->menuService = $menuService;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });

    }

    public function createCatalogue(StoreMenuCatalogueRequest $request){
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if($menuCatalogue !== FALSE){
            return response()->json([
                'code' => 0,
                'message' => 'Tạo nhóm thành công!',
                'data' => $menuCatalogue,
            ]);
        }
        return response()->json([
            'message' => 'Có vấn đề xảy ra. Hãy thử lại',
            'code' => 1,
        ]);
    }

    public function drag(Request $request){
        $json = json_decode($request->string('json'), TRUE);
        $menuCatalogueId = $request->integer('menu_catalogue_id');
        $flag = $this->menuService->dargUpdate($json, $menuCatalogueId, $this->language);
    }
}
