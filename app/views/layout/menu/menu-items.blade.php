@foreach($items as $item)
    <?php if(is_null($item->url()) && !$item->hasChildren()) { continue; } ?>
    <li@lm-attrs($item) @if($item->hasChildren()) class="treeview" @endif @lm-endattrs>
        @if(is_null($item->url()))
            @if($item->hasChildren())
            <a href="javascript://">
                <i class="fa fa-folder"></i> <span>{{ $item->title }}</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            @endif
        @else
        <a href="{{ $item->url() }}" target="_self">
            @if(isset($isChild) && $isChild === true)
                <i class="fa fa-angle-double-right"></i>
            @endif

            {{ $item->title }}</span>
        </a>
        @endif

        @unless( ! $item->hasChildren())
            <ul class="treeview-menu">
                @include('layout.menu.menu-items', ['items' => $item->children(), 'isChild' => true])
            </ul>
        @endunless
    </li>
@endforeach
