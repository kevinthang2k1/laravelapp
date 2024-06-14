<div class="panel-body mb30">
    <div class="cart-information">
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input 
                        name="fullname"
                        type="text"
                        value="{{ old('fullname') }}"
                        placeholder="Nhập vào Họ Tên"
                        class="input-text"
                    >
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input 
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                        placeholder="Nhập vào số điện thoại"
                        class="input-text"
                    >
                </div>
            </div>
        </div>
        <div class="form-row mb20">
            <input 
                name="email"
                type="text"
                value="{{ old('emali') }}"
                placeholder="Nhập vào Email"
                class="input-text"
            >
        </div>
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-3">
                <select name="province_id" id="" class="setupSelect2 province location" data-target="districts">
                    <option value="">[Chọn thành phố]</option>
                    @foreach ($provinces as $key => $val)
                        <option value="{{ $val->code }}">{{ $val->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="district_id" id="" class="setupSelect2 districts location" data-target="wards">
                    <option value="">Chọn quận huyện</option>
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select name="ward_id" id="" class="setupSelect2 wards">
                    <option value="">Chọn phường xã</option>
                </select>
            </div>
        </div>
        <div class="form-row mb20">
            <input 
                name="address"
                type="text"
                value="{{ old('address') }}"
                placeholder="Nhập địa chỉ chi tiết"
                class="input-text"
            >
        </div>
        <div class="form-row">
            <input 
                name="description"
                type="text"
                value="{{ old('description') }}"
                placeholder="Ghi chú thêm (Ví dụ:Giao hàng vào lúc 3 giờ chiều)"
                class="input-text"
            >
        </div>
    </div>
</div>