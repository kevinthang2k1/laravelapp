@include('backend.dashboard.component.breadcrumb', ['title' => $config ['seo'] ['index'] ['title']]) 
{{-- <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-8">
        <h2>{{ __('messages.postCatalogue.index.title') }}</h2>
        <ol class="breadcrumb" style="margin-bottom: 10px;">
            <li>
                <a href="{{ route('dashboard.index') }}">Dashboard</a>
            </li>
            <li class="active"><strong>{{ __('messages.postCatalogue.create.title') }}</strong></li>
        </ol>
    </div>
</div> --}}

<div class="row mb20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo'] ['index'] ['table'] }}</h5>
                @include('backend.dashboard.component.toolbox', ['model'=>'UserCatalogue'])

            </div>
            <div class="ibox-content">
                @include('backend.user.user.component.filter')
                @include('backend.user.user.component.table')
            </div>
        </div>
    </div>
</div>
