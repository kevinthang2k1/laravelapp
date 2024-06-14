<div class="panel-voucher uk-hidden">
    <div class="voucher-list">
        @for ($i = 0; $i < 2; $i++)
            <div class="voucher-item {{ $i == 0 ? 'active' : ''}}">
                <div class="voucher-left"></div>
                <div class="voucher-right">
                    <div class="voucher-title">dfgdsfgfds <span>(còn 20)</span></div>
                    <div class="voucher-description">
                        <p>khuyến mại</p>
                    </div>
                </div>
            </div>
        @endfor
    </div>
    <div class="voucher-form">
        <input type="text" placeholder="Chọn mã giảm giá" name="voucher" value="" readonly>
        <a href="" class="apply-voucher">Áp dụng</a>
    </div>
</div>