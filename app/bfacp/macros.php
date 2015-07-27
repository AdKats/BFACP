<?php

use Illuminate\Support\Facades\HTML;

HTML::macro('moment', function ($timestamp = null, $duration = null, $durationFormat = 'seconds', $fromNow = false) {
    if (!is_null($timestamp) && is_null($duration) && !$fromNow) {
        return sprintf('{{ moment(\'%s\').format(\'lll\') }}', $timestamp);
    } elseif (!is_null($timestamp) && is_null($duration) && $fromNow) {
        return sprintf('{{ moment(\'%s\').fromNow() }}', $timestamp);
    } elseif (is_null($timestamp) && !is_null($duration) && !$fromNow) {
        return sprintf('{{ momentDuration(%u, \'%s\') }}', (int)$duration, $durationFormat);
    }
});

HTML::macro('faicon', function ($icon, $openSpan = false) {
    if ($openSpan) {
        return sprintf('<i class="fa %s"></i><span>', $icon);
    }

    return sprintf('<i class="fa %s"></i>', $icon);
});

HTML::macro('ionicon', function ($icon, $openSpan = false) {
    if ($openSpan) {
        return sprintf('<i class="ion %s"></i><span>', $icon);
    }

    return sprintf('<i class="ion %s"></i>', $icon);
});
