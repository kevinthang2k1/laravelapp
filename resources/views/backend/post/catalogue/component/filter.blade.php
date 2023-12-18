<form action="{{ route('post.catalogue.index') }}">
    <div class="filter">
        <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
            <div class="perpage">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
                    <select name="perpage" class="form-control input-sm perpage filter mr10  ">
                        @for($i = 10; $i<= 100; $i+=10)
                            <option {{ ($perpage == $i) ? 'selected' : '' }} value="{{ $i }}">{{ $i }}{{ __('messages.perpage') }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="Uk-flex Uk-flex-middle">
                    @php
                        $publish = request('publish') ?: old('publish');
                    @endphp
                    <select name="publish" class="form-control mr10 setupSelect2">
                        @foreach(__('messages.publish')  as $key => $val)
                            <option {{ ($publish == $key) ? 'selected' : '' }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                    <div class="Uk-search Uk-flex Uk-flex-middle mr10 ">
                        <div class="input-group">
                            <input 
                            type="text" 
                            name="keyword" 
                            value="{{ request('keyword') ?: old('keyword') }}" 
                            placeholder="{{ __('messages.searchInput') }}"class="form-control"
                            >
                            <span class="input-group-btn">
                                <button 
                                type ="submit" 
                                name="search" 
                                value="search" 
                                class="btn btn-primary mb0 btn-sm">{{ __('messages.search') }}</button>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('post.catalogue.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>{{ __('messages.postCatalogue.create.title') }}</a>
                </div>
            </div>
        </div>
    </div>
</form>