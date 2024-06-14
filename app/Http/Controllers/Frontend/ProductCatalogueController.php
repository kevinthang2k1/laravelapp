<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;



class ProductCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productRepository;
    protected $productService;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductRepository $productRepository,
        ProductService $productService,
    ){
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productRepository = $productRepository;
        $this->productService = $productService;

        parent::__construct(
            // $systemRepository,
        );
    }

    public function index($id, $request, $page = 1){
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        
        $products = $this->productService->paginate($request, $this->language, $productCatalogue, $page, ['path' => $productCatalogue->canonical]);
        $productId = $products->pluck('id')->toArray();
        if(count($productId) && !is_null($productId)){
            $products = $this->productService->combineProductAndPromotion($productId, $products);
        }
        // dd($products);
        $config = $this->config();
        $system = $this->system;
        $seo = seo($productCatalogue, $page);
        return view('frontend.product.catalogue.index', compact(
            'config',
            'seo',
            'system',
            'productCatalogue',
            'breadcrumb',
            'products',
        ));   
    }

    private function config(){
        return [
            'language' => $this->language
        ];
    }

}
