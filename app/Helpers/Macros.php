<?php namespace BFACP\Helpers;

class Macros extends Main
{
    /**
     * @param null       $timestamp
     * @param null       $duration
     * @param string     $durationFormat
     * @param bool|false $fromNow
     *
     * @return null|string
     */
    public function moment($timestamp = null, $duration = null, $durationFormat = 'seconds', $fromNow = false)
    {
        if (!is_null($timestamp) && is_null($duration) && !$fromNow) {
            return sprintf('{{ moment(\'%s\').format(\'lll\') }}', $timestamp);
        } elseif (!is_null($timestamp) && is_null($duration) && $fromNow) {
            return sprintf('{{ moment(\'%s\').fromNow() }}', $timestamp);
        } elseif (is_null($timestamp) && !is_null($duration) && !$fromNow) {
            return sprintf('{{ momentDuration(%u, \'%s\') }}', (int)$duration, $durationFormat);
        }

        return null;
    }

    /**
     * @param            $icon
     * @param bool|false $openSpan
     *
     * @return string
     */
    public function faicon($icon, $openSpan = false)
    {
        if ($openSpan) {
            return sprintf('<i class="fa %s"></i><span>', $icon);
        }

        return sprintf('<i class="fa %s"></i>', $icon);
    }

    /**
     * @param            $icon
     * @param bool|false $openSpan
     *
     * @return string
     */
    public function ionicon($icon, $openSpan = false)
    {
        if ($openSpan) {
            return sprintf('<i class="ion %s"></i><span>', $icon);
        }

        return sprintf('<i class="ion %s"></i>', $icon);
    }
}
