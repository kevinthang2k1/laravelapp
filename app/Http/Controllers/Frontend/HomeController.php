<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\SlideRepositoryInterface  as SlideRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Services\Interfaces\WidgetServiceInterface  as WidgetService;
use App\Services\Interfaces\SlideServiceInterface  as SlideService;
use App\Enums\SlideEnum;
use App\Events\TestEvent;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class HomeController extends FrontendController
{
    protected $language;
    protected $slideRepository;
    protected $widgetService;
    protected $slideService;
    protected $systemRepository;


    public function __construct(
        SlideRepository $slideRepository,
        WidgetService $widgetService,
        SlideService $slideService,
        SystemRepository $systemRepository,


    ){
        $this->slideRepository = $slideRepository;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;


        parent::__construct(
            $systemRepository,
        );
    }

    public function index( ){
        $config = $this->config();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'category', 'countObject' => true],
            ['keyword' => 'news','children' => true],
            ['keyword' => 'category-highlight'],
            ['keyword' => 'category-home','children' => true, 'promotion' => true, 'object' => true],
            ['keyword' => 'bestseller'],
        ], $this->language);

        // dd($widgets['bestseller']);
        $slides = $this->slideService->getSlide([SlideEnum::BANNER, SlideEnum::MAIN], $this->language);
        // dd($slides);
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonncal' => config('app.url'),
        ];
        // dd($seo);
        return view('frontend.homepage.home.index', compact(
            'config',
            'slides',
            'widgets',
            'seo',
            'system',
        ));    
    }

    private function config(){
        return [
            'language' => $this->language
        ];
    }

}
