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
        $setting = Option::setting('site.languages')->first();

        $keys = explode(',', $setting->option_value);

        $keys[] = 'ru';

        $setting->option_value = implode(',', $keys);
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
