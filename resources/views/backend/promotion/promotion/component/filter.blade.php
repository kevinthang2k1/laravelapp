<form action="{{ route('promotion.index') }}">
    <div class="filter">
        <div class="Uk-flex Uk-flex-middle Uk-flex-space-betwween">
            @include('backend.dashboard.component.perpage')

            <div class="action">
                <div class="Uk-flex Uk-flex-middle">
                    @include('backend.dashboard.component.filterPublish')
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('promotion.create') }}" class="btn btn-danger"><i class="fa fa-plus"></i>Thêm mới khuyến mại</a>
                </div>
            </div>
        </div>
    </div>
</form>