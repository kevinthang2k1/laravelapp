@include('backend.dashboard.component.breadcrumb',['title' => $config['seo'] ['create'] ['title']])
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@php
    $url = ($config['method'] == 'create') ? route('slide.store') : route('slide.update',$slide->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @include('backend.slide.slide.component.list')
            </div>
            <div class="col-lg-3">
                @include('backend.slide.slide.component.aside')
            </div>
        </div>
        <div class="text-right">
            <button class="btn btn-primary" type="sumit" name="send" value="send">Lưu lại </button>
        </div>
    </div>
</form>




