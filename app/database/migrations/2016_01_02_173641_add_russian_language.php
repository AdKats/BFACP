<?php

use BFACP\Option;
use Illuminate\Database\Migrations\Migration;

class AddRussianLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Option::count() == 0) {
            if (! defined('FIRST_RUN')) {
                define('FIRST_RUN', true);
            }

            return;
        }

        $setting = Option::setting('site.languages')->first();

        $keys = explode(',', $setting->option_value);

        $keys[] = 'ru';

        $setting->option_value = implode(',', $keys);
        $setting->save();
    }
}
