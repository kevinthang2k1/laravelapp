
@include('backend.dashboard.component.breadcrumb',['title' => $config['seo'] ['create'] ['title']])

<form action="{{ route('user.Catalogue.destroy', $userCatalogue->id) }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">Thông tin chung</div>
                    <div class="panel-description">
                        <p>Bạn chắc chắn muốn xóa nhóm thành viên có tên là:<span class="text-danger">{{ $userCatalogue->name }}</span></p>
                        <p>- Lưu ý: Không thể khôi phục thành viên sau khi xóa. Hãy chắc chắn bạn muốn thực hiện chức năng này.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin chung</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên nhóm<span class="text-danger">*</span></label>
                                    <input
                                        type ="text"
                                        name ="name"
                                        value ="{{ old('name', ($userCatalogue->name) ?? '') }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right">
            <button class="btn btn-danger" type="sumit" name="send" value="send">Xóa dữ liệu</button>
        </div>
    </div>
</form>



