<?php

use BFACP\Option;

$options = [];

$format = function(&$options, $keys, $value) use(&$format) {
    $keys ? $format($options[array_shift($keys)], $keys, $value) : $options = $value;
};

foreach(Option::all() as $option) {
    $format($options, explode('.', $option->option_key), $option->option_value);
}

return $options;
