<?php
namespace App\Http\ViewComposers;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use Illuminate\View\View;

class MenuComposer
{
    protected $language;

    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
        $language
    ){
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->language = $language;
    }

    public function compose(View $view)
    {
        $agrument = $this->agrument($this->language);
        $menuCatalogue = $this->menuCatalogueRepository->findByCondition(...$agrument);
        $menus = [];
        $htmlType = ['main-menu'];
        if(count($menuCatalogue)){
            foreach($menuCatalogue as $key => $val){
                $type = (in_array($val->keyword, $htmlType)) ? 'html' :'array';
                $menus[$val->keyword] = frontend_recursive_menu(recursive($val->menus),0 ,1 , $type);
            }
        }
        // dd($menus);
        // $menus = frontend_recursive_menu(recursive($menuCatalogue->menus));
        $view->with('menu', $menus);
    }

    private function agrument($language){
        return [
            'condition' => [
                config('apps.general.defaultPublish')
            ],
            'flag' => true,//chọn ra một phần tử do chỉ lấy mỗi keyword nếu để true thì $menuCatalogue->menu bị lỗi vì menu đang ở dạng mảng 
            'relation' => [
                'menus' => function($query) use ($language){
                    $query->orderBy('order', 'desc');
                    $query->with([
                        'languages' => function($query) use ($language){
                            $query->where('language_id', $language);
                        }
                    ]);
                }
            ],
        ];
    }
}