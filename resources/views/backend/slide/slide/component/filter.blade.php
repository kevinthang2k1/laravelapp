<form action="{{ route('slide.index') }}">
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
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('slide.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>Thêm mới Slide</a>
                </div>
            </div>
        </div>
    </div>
</form>