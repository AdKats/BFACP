<?php

use BFACP\Option;

$options = [];

$format = function(&$options, $keys, $value) use(&$format) {
    $keys ? $format($options[array_shift($keys)], $keys, $value) : $options = $value;
};

foreach(Option::all() as $option) {
    if($option->option_value === 1 || $option->option_value === '1' || $option->option_value === 'true') {
        $format($options, explode('.', $option->option_key), true);
    } elseif($option->option_value === 0 || $option->option_value === '0' || $option->option_value === 'false') {
        $format($options, explode('.', $option->option_key), false);
    } else {
        $format($options, explode('.', $option->option_key), $option->option_value);
    }
}

return $options;
