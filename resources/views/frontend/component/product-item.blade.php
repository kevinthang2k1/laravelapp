<?php 
    $name = $product->languages->first()->pivot->name;
    $canonical = write_url($product->languages->first()->pivot->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $catName = $product->product_catalogues->first()->languages->first()->pivot->name;
    $review = getReview($product);
?>
<div class="product-item product">
    <div class="badge badge-bg<?php echo rand(1,3) ?>">-<?php echo rand(10, 35) ?>%</div>
    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
    <div class="info">
        <div class="category-title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $catName }}</a></div>
        <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
        <div class="rating">
            <div class="uk-flex uk-flex-middle">
                <div class="star">
                    @for ($j = 1; $j <= $review['star']; $j++)
                        <i class="fa fa-star"></i>
                    @endfor
                </div>
                <span class="rate-number">({{ $review['count'] }})</span>
            </div>
        </div>
        <div class="product-group">
            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                {!! $price['html'] !!}
                <div class="addcart">
                    {!! renderQuickBuy($product, $name, $canonical) !!}
                </div>
            </div>
        </div>

    </div>
    <div class="tools">
        <a href="{{ $canonical }}" title="{{ $name }}"><img src="frontend/resources/img/trend.svg" alt="{{ $name }}"></a>
        <a href="{{ $canonical }}" title="{{ $name }}"><img src="frontend/resources/img/wishlist.svg" alt="{{ $name }}"></a>
        <a href="{{ $canonical }}" title="{{ $name }}"><img src="frontend/resources/img/compare.svg" alt="{{ $name }}"></a>
        <a href="{{ $canonical }}#popup" data-uk-modal title="{{ $name }}"><img src="frontend/resources/img/view.svg" alt="{{ $name }}"></a>
    </div>
</div>