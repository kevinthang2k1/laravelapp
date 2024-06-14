<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Services\Interfaces\ProductServiceInterface as ProductService;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;


class ProductController extends FrontendController
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

    public function index($id, $request){
        // echo $id;die();4
        $language = $this->language;
        // dd($language);
        $product = $this->productRepository->getProductById($id, $this->language);
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);

        // dd($product);

        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        /*--------------------------------------------------------*/ 
        $product = $this->productService->getAttribute($product, $this->language);
        $category = recursive(
            $this->productCatalogueRepository->all([
                'languages' => function($query) use ($language){
                    $query->where('language_id', $language);
                }
            ], categorySelectRaw('product'))
        );

        // dd($category);



        $config = $this->config();
        $system = $this->system;
        $seo = seo($product);
        return view('frontend.product.product.index', compact(
            'config',
            'seo',
            'system',
            'productCatalogue',
            'breadcrumb',
            'product',
            'category',
        ));   
    }

    private function config(){
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js'

            ],
        ];
    }

}
