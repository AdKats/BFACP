<?php

use Carbon\Carbon;

HTML::macro('moment', function($timestamp = null, $duration = null, $durationFormat = 'seconds')
{
    if(!is_null($timestamp) && is_null($duration)) {
        return sprintf('{{ moment(\'%s\').format(\'lll\') }}', $timestamp);
    } elseif(is_null($timestamp) && !is_null($duration)) {
        return sprintf('{{ momentDuration(%u, \'%s\') }}', (int) $duration, $durationFormat);
    }
});

HTML::macro('faicon', function($icon, $openSpan = false)
{
    if($openSpan) {
        return sprintf('<i class="fa %s"></i><span>', $icon);
    }

    return sprintf('<i class="fa %s"></i>', $icon);
});

HTML::macro('ionicon', function($icon, $openSpan = false)
{
    if($openSpan) {
        return sprintf('<i class="ion %s"></i><span>', $icon);
    }

    return sprintf('<i class="ion %s"></i>', $icon);
});
