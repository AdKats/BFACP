<?php

use BFACP\Option;

class OptionsTableSeeder extends Seeder
{
    public function run()
    {
        $options = [
            [
                'option_key' => 'metabans.key',
                'option_title' => 'Metabans API Key',
                'option_value' => '',
                'option_description' => ''
            ],
            [
                'option_key' => 'metabans.user',
                'option_title' => 'Metabans Username',
                'option_value' => '',
                'option_description' => 'The username of the API key account holder.'
            ],
            [
                'option_key' => 'metabans.account',
                'option_title' => 'Metabans Account',
                'option_value' => '',
                'option_description' => 'The account name you want to pull the metabans feed from.'
            ]
        ];

        Option::insert($options);
    }
}
