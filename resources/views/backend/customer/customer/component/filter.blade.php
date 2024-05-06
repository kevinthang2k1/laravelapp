<form action="{{ route('customer.index') }}">
    <div class="filter">
        <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
            <div class="perpage">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
                    <select name="perpage" class="form-control input-sm perpage filter mr10  ">
                        @for($i = 10; $i<= 100; $i+=10)
                            <option {{ ($perpage == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="Uk-flex Uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')

                    <select name="customer_catalogue_id" class="form-control mr10 setupSelect2">
                        <option value="0" selected="selected">Chọn nhóm thành viên</option>
                        @foreach($customerCatalogues as $key => $val)
                        <option value="{{ $val->id }}" >{{ $val->name }}</option>
                        @endforeach
                    </select>
                    <div class="Uk-search Uk-flex Uk-flex-middle mr10 ">
                        <div class="input-group">
                            <input 
                            type="text" 
                            name="keyword" 
                            value="{{ request('keyword') ?: old('keyword') }}" 
                            placeholder="Nhập từ khóa muốn tìm kiếm ...."class="form-control"
                            >
                            <span class="input-group-btn">
                                <button 
                                type ="submit" 
                                name="search" 
                                value="search" 
                                class="btn btn-primary mb0 btn-sm">Tìm kiếm</button>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('customer.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>Thêm mới thành viên</a>
                </div>
            </div>
        </div>
    </div>
</form>