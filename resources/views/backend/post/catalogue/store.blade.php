
@include('backend.dashboard.component.breadcrumb',['title' => $config['seo'] [$config['method']] ['title']])
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
    $url = ($config['method'] == 'create') ? route('post.catalogue.store') : route('post.catalogue.update',$postCatalogue->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>{{ __('messages.tableHeading') }}</h5>
                    </div>
                    <div class="ibox-content">
                       @include('backend.dashboard.component.content', ['model' => ($postCatalogue) ?? null])

                       {{-- 
                            khi tách các file như content, Seo, album ra để dùng chung thì chúng nó sẽ phục vụ cho rất nhiều module ví dụ: sản phẩm, và bài viết 
                       --> vì vậy phải truyền cái model vào 

                        --> ví dụ thằng store này gọi file content thì phải truyền cái $postCatalogue vào
                    --> khi edit thì ở Controller nó gửi sang 1 biến PostCatalogue
                --> Thì bên create nó mới ko bị lỗi do việc sử dụng chung 1 view.
            --> kiểm tra tất cả các file gọi vào như content, album, seo file nào mà có cái biến $model thì phải truyền cái ['model' => ($postCatalogue) vào như là tham số thứ 2 của hàm include;
                        --}}
                    </div>
                </div>
                @include('backend.dashboard.component.album', ['model' => ($postCatalogue) ?? null])
                @include('backend.post.catalogue.component.seo', ['model' => ($postCatalogue) ?? null])
            </div>

            <div class="col-lg-3">
                @include('backend.post.catalogue.component.aside')
            </div>
        </div>

        <hr>
        <div class="text-right mb15 button-fix">
            <button class="btn btn-primary" type="sumit" name="send" value="send">{{ __('messages.save') }}</button>
        </div>
    </div>
</form>




