<?php

namespace App\Http\Controllers\Ajax;
use App\Repositories\Interfaces\ProductRepositoryInterface  as ProductRepository;

use App\Http\Controllers\FrontendController;
use App\Services\CartService;
use Illuminate\Http\Request;
use Cart;


class CartController extends FrontendController
{
    protected $cartService;
    protected $productRepository;
    protected $language;

    public function __construct(
        CartService $cartService,
        ProductRepository $productRepository,

    ){
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;

        parent::__construct();
    }

    public function create(Request $request){
        $flag = $this->cartService->create($request, $this->language);
        // dd($flag);
        $cart = Cart::instance('shopping')->content();  
        // dd($cart);
        return response()->json([
            'cart' => $cart, 
            'messages' => 'Thêm sản phẩm giỏ hàng thành công',
            'code' => ($flag) ? 10 : 11,
        ]);
    }

    public function update(Request $request){
        $response = $this->cartService->update($request);
        // dd($response);
        return response()->json([
            'response' => $response, 
            'messages' => 'Cập nhật số lượng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);
    }

    public function delete(Request $request){
        $response = $this->cartService->delete($request);
        // dd($response);
        return response()->json([
            'response' => $response, 
            'messages' => 'Xóa sản phẩm khỏi giở hàng thành công',
            'code' => (!$response) ? 11 : 10,
        ]);
    }
}
