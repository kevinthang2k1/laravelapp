<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Services\Interfaces\PromotionServiceInterface  as PromotionService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;

use App\Http\Requests\Promotion\StorePromotionRequest;
use App\Http\Requests\Promotion\UpdatePromotionRequest;

class PromotionController extends Controller
{
    protected $promotionService;
    protected $language;
    protected $promotionRepository;
    protected $sourceRepository;

    public function __construct(
        PromotionService $promotionService,
        PromotionRepository $promotionRepository,
        SourceRepository $sourceRepository,

    ){
        $this->promotionService = $promotionService;
        $this->promotionRepository = $promotionRepository;
        $this->sourceRepository = $sourceRepository;

        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request){
        //$this->authorize('modules', 'promotion.index');
        $promotions = $this->promotionService->paginate($request);
      
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Promotion'
        ];
        $config['seo'] =__('messages.promotion');
        $template = 'backend.promotion.promotion.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotions'
        ));
    }

    public function create(){
        //$this->authorize('modules', 'promotion.create');
        $config = $this->config();
        $sources =$this->sourceRepository->all();
        $config['seo'] =__('messages.promotion');
        $config['method'] = 'create';
        $template = 'backend.promotion.promotion.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources',
        ));
    }

    public function store(StorePromotionRequest $request){
        if($this->promotionService->create($request, $this->language)){
            return redirect()->route('promotion.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        // dd($id);
        //$this->authorize('modules', 'promotion.update');
        $promotion = $this->promotionRepository->findById($id);
        $sources =$this->sourceRepository->all();
        $config = $this->config();
        $config['seo'] =__('messages.promotion');
        $config['method'] = 'edit';
        $template = 'backend.promotion.promotion.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'promotion',
            'sources',
        ));
    }

    public function update($id, UpdatePromotionRequest $request){
        if($this->promotionService->update($id, $request, $this->language)){
            return redirect()->route('promotion.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        //$this->authorize('modules', 'promotion.destroy');
        $config['seo'] =__('messages.promotion');
        $promotion = $this->promotionRepository->findById($id);
        $template = 'backend.promotion.promotion.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'promotion',
            'config',
        ));
    }

    public function destroy($id){
        if($this->promotionService->destroy($id)){
            return redirect()->route('promotion.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('promotion.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js',
                'backend/library/promotion.js',
                'backend/plugins/ckeditor/ckeditor.js',

            ]
        ];
    }

}
