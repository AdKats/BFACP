<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    "accepted"             => ":attribute должен быть принят.",
    "active_url"           => ":attribute не является URL.",
    "after"                => ":attribute должен быть датой после :date.",
    "alpha"                => ":attribute может содержать только буквы.",
    "alpha_dash"           => ":attribute может содержать только буквы, цифры и дефис.",
    "alpha_num"            => ":attribute может содержать только буквы и цифры.",
    "array"                => ":attribute должен быть массивом.",
    "before"               => ":attribute должен быть датой до :date.",
    "between"              => [
        "numeric" => ":attribute должен быть между :min и :max.",
        "file"    => ":attribute должен быть между :min и :max КБ.",
        "string"  => ":attribute должен быть между :min и :max символов.",
        "array"   => ":attribute должен иметь от :min до :max значений.",
    ],
    "boolean"              => ":attribute поле должно быть либо положительным, либо отрицательным.",
    "confirmed"            => ":attribute подтверждение не совпадает.",
    "date"                 => ":attribute не является корректной датой.",
    "date_format"          => ":attribute не подходит под формат :format.",
    "different"            => ":attribute и :other должны быть разными.",
    "digits"               => ":attribute должен быть :digits цифрами.",
    "digits_between"       => ":attribute должен быть между :min и :max цифр.",
    "email"                => ":attribute должен быть настоящим Email.",
    "exists"               => "Выбранный :attribute не правильный.",
    "image"                => ":attribute должен быть изображением.",
    "in"                   => "Выбранный :attribute не правильный.",
    "integer"              => ":attribute должен быть числом.",
    "ip"                   => ":attribute не является корректным IP.",
    "max"                  => [
        "numeric" => ":attribute не должен быть больше :max.",
        "file"    => ":attribute не должен быть больше :max КБ.",
        "string"  => ":attribute не должен быть больше :max символов.",
        "array"   => ":attribute не должен быть больше :max значений.",
    ],
    "mimes"                => ":attribute must be a file of type: :values.",
    "min"                  => [
        "numeric" => ":attribute должен быть как минимум :min.",
        "file"    => ":attribute должен быть как минимум :min КБ.",
        "string"  => ":attribute должен быть как минимум :min символов.",
        "array"   => ":attribute должен быть как минимум :min значений.",
    ],
    "not_in"               => "Выбранный :attribute не правильный.",
    "numeric"              => ":attribute должен быть числом.",
    "regex"                => ":attribute формат не правильный.",
    "required"             => ":attribute поле требуется.",
    "required_if"          => ":attribute поле требуется когда :other - :value.",
    "required_with"        => ":attribute поле требуется когда :values будущее.",
    "required_with_all"    => ":attribute поле требуется когда :values будущее.",
    "required_without"     => ":attribute поле требуется когда :values прошлое.",
    "required_without_all" => ":attribute поле требуется когда ничто из :values является будущим.",
    "same"                 => ":attribute и :other должны различаться.",
    "size"                 => [
        "numeric" => ":attribute должен быть :size.",
        "file"    => ":attribute должен быть :size КБ.",
        "string"  => ":attribute должен быть :size символов.",
        "array"   => ":attribute должен содержать :size значений.",
    ],
    "unique"               => ":attribute уже взят.",
    "url"                  => ":attribute формат не правильный.",
    "timezone"             => ":attribute должен быть правильной зоной.",
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom'               => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes'           => [],

];
