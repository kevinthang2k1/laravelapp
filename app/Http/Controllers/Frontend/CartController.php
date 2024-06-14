<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface as OrderRepository;

use App\Services\Interfaces\CartServiceInterface  as CartService;
use App\Http\Requests\StoreCartRequest;
use App\Mail\OrderMail;
use Cart;


class CartController extends FrontendController
{   
    protected $provinceRepository;  
    protected $promotionRepository;  
    protected $cartService;
    protected $orderRepository;


    public function __construct(
        ProvinceRepository $provinceRepository,
        CartService $cartService,
        PromotionRepository $promotionRepository,
        OrderRepository $orderRepository,

    ){
        $this->provinceRepository = $provinceRepository;
        $this->promotionRepository = $promotionRepository;
        $this->cartService = $cartService;
        $this->orderRepository = $orderRepository;

        parent::__construct();
    }

    public function checkout( ){

        $provinces = $this->provinceRepository->all();
        $carts = Cart::instance('shopping')->content();
        $carts = $this->cartService->remakeCart($carts);
        // $cartConfig = $this->cartConfig();
        $cartCaculate = $this->cartService->reCaculateCart();
        $cartPromotion = $this->cartService->cartPromotion($cartCaculate['cartTotal']);
        $seo = [
            'meta_title' => 'Trang thanh toán đơn hàng',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonncal' => write_url('thanh-toan', TRUE, TRUE),
        ];
        $system = $this->system;
        $config = $this->config();
        return view('frontend.cart.index', compact(
            'config',
            'seo',
            'system',
            'provinces',
            'carts',
            'carts',
            'cartPromotion',
            'cartCaculate',
        ));    
    }

    public function store(StoreCartRequest $request){
        $system = $this->system;
        $order = $this->cartService->order($request, $system);
        if($order['flag']){
            return redirect()->route('cart.success', ['code' => $order['order']->code])->with('success','Đặt hàng thành công');
        }
        return redirect()->route('cart.checkout')->with('error','Đặt hàng không thành công. Hãy thử lại');
    }

    public function success($code){

        $order =$this->orderRepository->findByCondition([
            ['code', '=', $code],

        ], false, ['products']);

        $seo = [
            'meta_title' => 'Thanh toán đơn hàng thành công',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonncal' => write_url('cart/success', TRUE, TRUE),
        ];
        $system = $this->system;
        $config = $this->config();
        return view('frontend.cart.success', compact(
            'config',
            'seo',
            'system',
            'order',
        ));  
    }

    private function config(){
        return [
            'language' => $this->language,
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
                'frontend/core/library/cart.js',
            ]
        ];
    }
}
