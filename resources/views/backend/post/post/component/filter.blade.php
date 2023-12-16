<form action="{{ route('post.index') }}">
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
                    @php
                        $publish = request('publish') ?: old('publish');
                        $postCatalogueId = request('post_catalogue_id') ?: old('post_catalogue_id');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2">
                        @foreach(config('apps.general.publish') as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>

                    <select name="post_catalogue_id" class="form-control mr10 setupSelect2">
                        @foreach($dropdown as $key => $val)
                            <option {{ ($postCatalogueId == $key) ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
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
                    <a href="{{ route('post.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>{{ config('apps.post.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>