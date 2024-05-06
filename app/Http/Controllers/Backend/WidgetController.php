<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Services\Interfaces\WidgetServiceInterface  as WidgetService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\WidgetRepositoryInterface as WidgetRepository;
use App\Http\Requests\StoreWidgetRequest;
use App\Http\Requests\UpdateWidgetRequest;

class WidgetController extends Controller
{
    protected $widgetService;
    protected $language;
    protected $widgetRepository;

    public function __construct(
        WidgetService $widgetService,
        WidgetRepository $widgetRepository,
    ){
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request){
        //$this->authorize('modules', 'widget.index');
        $widgets = $this->widgetService->paginate($request);
      
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Widget'
        ];
        $config['seo'] =__('messages.widget');
        $template = 'backend.widget.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widgets'
        ));
    }

    public function create(){
        //$this->authorize('modules', 'widget.create');
        $config = $this->config();
        $config['seo'] =__('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreWidgetRequest $request){
        if($this->widgetService->create($request, $this->language)){
            return redirect()->route('widget.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    private function menuItemAgrumaent(array $whereIn = []){
        $language = $this->language;
        return [
            'condition' => [],
            'flag' => true,
            'relation' => [
                'languages' => function($query) use ($language){
                    $query->where('language_id', $language);
                }
            ],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $whereIn,
                'whereInField' => 'id',
            ]
        ];
    }

    public function edit($id){
        //$this->authorize('modules', 'widget.update');
        $widget = $this->widgetRepository->findById($id);
        $modelClass = loadClass($widget->model);
        // dd($modelClass);
        $widgetItem = convertArrayByKey( $modelClass->findByCondition(
            ...array_values($this->menuItemAgrumaent($widget->model_id))
        ), ['id', 'name.languages', 'image']);
        $config = $this->config();
        $widget->description = $widget->description[$this->language];
        $config['seo'] =__('messages.widget');
        $config['method'] = 'edit';
        $template = 'backend.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'widgetItem',
        ));
    }

    public function update($id, UpdateWidgetRequest $request){
        if($this->widgetService->update($id, $request, $this->language)){
            return redirect()->route('widget.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        //$this->authorize('modules', 'widget.destroy');
        $config['seo'] =__('messages.widget');
        $widget = $this->widgetRepository->findById($id);
        $template = 'backend.widget.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'widget',
            'config',
        ));
    }

    public function destroy($id){
        if($this->widgetService->destroy($id)){
            return redirect()->route('widget.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',

            ]
        ];
    }

}
