<form action="{{ route('generate.index') }}">
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
                    <a href="{{ route('generate.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>Tạo module mới</a>
                </div>
            </div>
        </div>
    </div>
</form>