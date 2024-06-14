<div class="panel-foot mt30">
    <div class="cart-summary">
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Giảm giá</span>
                <div class="summary-value discount-value">-{{ convert_price($cartPromotion['discount'], true) }}đ</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title">Phí giao hàng</span>
                <div class="summary-value">Miễn phí giao hàng</div>
            </div>
        </div>
        <div class="cart-summary-item">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                <span class="summay-title bold">Phí giao hàng</span>
                <div class="summary-value cart-total">{{ (count($carts) && !is_null($carts)) ? convert_price($cartCaculate['cartTotal'] - $cartPromotion['discount'], true) : 0 }}đ</div>
            </div>
        </div>
    </div>
</div>