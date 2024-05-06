
@include('backend.dashboard.component.breadcrumb',['title' => $config['seo'] ['create'] ['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('customer.catalogue.store') : route('customer.catalogue.update',$customerCatalogue->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>- Nhập thông tin nhóm thành viên</p>
                        <p>- Lưu ý: Những trường đánh dấu <span class="text-danger">(*) </span>là bắt buộc</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>thong tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên nhóm<span class="text-danger">*</span></label>
                                    <input
                                        type ="text"
                                        name ="name"
                                        value ="{{ old('name', ($customerCatalogue->name) ?? '') }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Ghi chú<span class="text-danger">*</span></label>
                                    <input
                                        type ="text"
                                        name ="description"
                                        value ="{{ old('description',($customerCatalogue->name) ?? '') }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="text-right">
            <button class="btn btn-primary" type="sumit" name="send" value="send">Lưu lại </button>
        </div>
    </div>
</form>




