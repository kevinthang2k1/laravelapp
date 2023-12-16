<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PostServiceInterface as PostService; 
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Requests\DeletePostRequest;
use App\Classes\Nestedsetbie;

class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $language;

    public function __construct(
        PostService $postService,
        PostRepository $postRepository
        ){
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => 1,
        ]);
        $this->language = $this->currentLanguage();
    }

    public function index(Request $request)
    {
        $posts = $this->postService->paginate($request);

        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            ],
            'model' => 'Post'
        ];

        $config['seo'] = config('apps.post');
        $template = 'backend.post.post.index';
        $dropdown = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'posts',
            'dropdown'
        ));
    }

    public function create()
    {
        $config = $this->confiData();
        $config['seo'] = config('apps.post');
        $config['method'] = 'create';
        $config['model'] = 'Post';
        $dropdown = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
        ));
    }

    public function store(StorePostRequest $request){
        if($this->postService->create($request)){
            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công'); 
        }
        return redirect()->route('post.admin')->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $post = $this->postRepository->getPostById($id, $this->language);
        $config = $this->confiData();
        $config['seo'] = config('apps.post');
        $config['method'] = 'edit';
        $dropdown = $this->nestedset->Dropdown();
        $album = json_decode($post->album);
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'post',
            'album',
        ));
    }

    public function update($id, UpdatePostRequest $request){
        if($this->postService->update($id, $request)){
            return redirect()->route('post.index')->with('success', 'Cập nhật bản ghi thành công'); 
        }
        return redirect()->route('post.admin')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $config['seo'] = config('apps.post');
        $post = $this->postRepository->getPostById($id, $this->language);
        $template = 'backend.post.post.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'post',
            'config',
        ));
    }

    public function destroy($id){
        
        if($this->postService->destroy($id)){
            return redirect()->route('post.index')->with('success', 'Xóa bản ghi thành công'); 
        }
        return redirect()->route('post.admin')->with('error', 'Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function confiData(){
        return [
            'js' => [
                'backend/plugins/ckeditor/ckeditor.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/seo.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',

            ],
            'css'=> [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',

            ]
        ];
    }

}

