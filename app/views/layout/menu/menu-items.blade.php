@foreach($items as $item)
    <li@lm-attrs($item) @if($item->hasChildren()) class="treeview" @endif @lm-endattrs>
        @if(is_null($item->url()))
            {{ $item->title }}</span>
        @else
        <a href="{{ $item->url() }}" target="_self">
            @if(isset($isChild) && $isChild === TRUE)
                <i class="fa fa-angle-double-right"></i>
            @endif

            {{ $item->title }}</span>
        </a>
        @endif

        @unless( ! $item->hasChildren())
            <ul class="treeview-menu">
                @include('layout.menu.menu-items', ['items' => $item->children(), 'isChild' => TRUE])
            </ul>
        @endunless
    </li>
@endforeach
