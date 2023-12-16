<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th style="width:90px">Ảnh</th>
            <th>Tên nhóm thành viên</th>
            <th class="text-center">Số thành viên</th>
            <th>Mô tả</th>
            <th class="text-center">Tình trạng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($userCatalogues) && is_object($userCatalogues))
            @foreach($userCatalogues as $userCatalogue)
                <tr>
                    <td>
                        <input type="checkbox" value="{{ $userCatalogue->id }}" class="input-checkbox checkBoxItem">
                    </td>

                    <td>
                        <span class="image img-cover"><img src="https://files.cults3d.com/uploaders/25822624/illustration-file/bc3e3dc9-3f59-4012-b99f-0a3d17737c15/kda_ahr.webp" alt=""></span>
                    </td>

                    <td>{{ $userCatalogue->name }}</td>

                    <td class="text-center">{{ $userCatalogue->users_count }} người</td>

                    <td>{{ $userCatalogue->description }}</td>

                    <td class="text-center js-switch-{{ $userCatalogue->id }}">
                        <input type="checkbox" value="{{ $userCatalogue->publish }}" class="js-switch status" data-field="publish" data-model="{{ $config['model'] }}" {{ ($userCatalogue->publish == 2) ? 'checked':'' }} data-modelId="{{ $userCatalogue->id }}" />
                    </td>
                    
                    <td class="text-center">
                        <a href="{{ route('user.Catalogue.edit',$userCatalogue->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('user.Catalogue.delete', $userCatalogue->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{ $userCatalogues->links('pagination::bootstrap-4') }}
