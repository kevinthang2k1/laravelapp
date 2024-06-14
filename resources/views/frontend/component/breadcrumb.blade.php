@php
    $modename = $productCatalogue->languages->first()->pivot->name;
@endphp
<div class="page-breadcrumb background">
    <h1 class="heading-2"><span>{{ $modename }}</span></h1>
    <ul class="uk-list uk-clearfix">
        <li><a href="/"><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
        @if(!is_null($breadcrumb))
            @foreach ($breadcrumb as $key => $val)
            @php
                $name = $val->languages->first()->pivot->name;
                $canonical = write_url($val->languages->first()->pivot->canonical, true, true);    
            @endphp
                <li><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></li>
            @endforeach
        @endif
    </ul>
</div> 
 {{-- phần breadcrumb này liên quan đến cấu trúc danh mục cha con --}}