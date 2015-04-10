@if ($breadcrumbs)
    <ul class="breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$breadcrumb->last && !is_null($breadcrumb->url))
                <li>
                    <a href="{{{ $breadcrumb->url }}}" target="_self">
                        {{ $breadcrumb->icon or null }}
                        {{{ $breadcrumb->title }}}
                    </a>
                </li>
            @else
                <li class="active">
                    {{ $breadcrumb->icon or null }}
                    {{{ $breadcrumb->title }}}
                </li>
            @endif
        @endforeach
    </ul>
@endif
