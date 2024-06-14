<?php

namespace App\Http\Controllers\Backend\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Services\Interfaces\SourceServiceInterface  as SourceService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface  as ProvinceRepository;
use App\Repositories\Interfaces\SourceRepositoryInterface as SourceRepository;
use App\Http\Requests\StoreSourceRequest;
use App\Http\Requests\UpdateSourceRequest;

class SourceController extends Controller
{
    protected $sourceService;
    protected $language;
    protected $sourceRepository;

    public function __construct(
        SourceService $sourceService,
        SourceRepository $sourceRepository,
    ){
        $this->sourceService = $sourceService;
        $this->sourceRepository = $sourceRepository;
    }

    public function index(Request $request){
        //$this->authorize('modules', 'widget.index');
        $sources = $this->sourceService->paginate($request);
      
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Source'
        ];
        $config['seo'] =__('messages.source');
        $template = 'backend.source.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'sources'
        ));
    }

    public function create(){
        //$this->authorize('modules', 'widget.create');
        $config = $this->config();
        $config['seo'] =__('messages.source');
        $config['method'] = 'create';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSourceRequest $request){
        if($this->sourceService->create($request, $this->language)){
            return redirect()->route('source.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
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
        $source = $this->sourceRepository->findById($id);
        $config = $this->config();
        $config['seo'] =__('messages.source');
        $config['method'] = 'edit';
        $template = 'backend.source.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'source',
        ));
    }

    public function update($id, UpdateSourceRequest $request){
        if($this->sourceService->update($id, $request, $this->language)){
            return redirect()->route('source.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        //$this->authorize('modules', 'widget.destroy');
        $config['seo'] =__('messages.source');
        $source = $this->sourceRepository->findById($id);
        $template = 'backend.source.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'source',
            'config',
        ));
    }

    public function destroy($id){
        if($this->sourceService->destroy($id)){
            return redirect()->route('source.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('source.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/library/source.js',
                'backend/plugins/ckeditor/ckeditor.js',

            ]
        ];
    }

}
