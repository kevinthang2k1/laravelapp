
@include('backend.dashboard.component.breadcrumb',['title' => $config['seo'] ['create'] ['title']])
<form action="{{ route('system.store') }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        @foreach($systemConfig as $key => $val)
        <div class="row">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title">{{ $val['label'] }}</div>
                    <div class="panel-description">
                        {{ $val['description'] }}
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ibox">
                    @if(count($val['value']))
                    <div class="ibox-content">
                        @foreach($val['value'] as $keyVal => $item)
                        
                        @php
                            {{ $name = $key .'_'.$keyVal; }}
                        @endphp
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="uk-flex-space-between uk-flex"><span>{{ $item['label'] }}</span><span>{!! renderSystemLink($item) !!}</span></label>
                                    @switch($item['type'])
                                        @case('text')
                                        {!! renderSystemInput($name, $systems) !!}
                                            @break
                                        @case('images')
                                        {!! renderSystemimages($name, $systems) !!}
                                            @break
                                        @case('textarea')
                                        {!! renderSystemTextarea($name, $systems) !!}
                                            @break
                                        @case('select')
                                        {!! renderSystemSelect($item, $name, $systems) !!}
                                            @break
                                        @case('editor')
                                        {!! renderSystemEditor($name, $systems) !!}
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <hr>

        @endforeach
        <div class="text-right">
            <button class="btn btn-primary" type="sumit" name="send" value="send">Lưu lại </button>
        </div>
    </div>
</form>



