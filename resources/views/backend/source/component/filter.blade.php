<form action="{{ route('source.index') }}">
    <div class="filter">
        <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
            @include('backend.dashboard.component.perpage')

            <div class="action">
                <div class="Uk-flex Uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('source.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>Thêm mới nguồn khách</a>
                </div>
            </div>
        </div>
    </div>
</form>