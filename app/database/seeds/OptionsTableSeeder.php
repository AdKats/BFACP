<?php

use BFACP\Option;

class OptionsTableSeeder extends Seeder
{
    public function run()
    {
        Option::truncate();

        $options = [
            [
                'option_key' => 'metabans.key',
                'option_title' => 'Metabans API Key',
                'option_value' => '',
                'option_description' => 'API key for Metabans interaction.'
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
            ],
            [
                'option_key' => 'metabans.enabled',
                'option_title' => 'Use Metabans',
                'option_value' => false,
                'option_description' => 'Use metabans throughout the site or disable it completely. Default: Disabled'
            ],
            [
                'option_key' => 'site.title',
                'option_title' => 'Site Title',
                'option_value' => 'BFAdminCP',
                'option_description' => 'The name of the site in the page title.'
            ],
            [
                'option_key' => 'site.motd',
                'option_title' => 'Message of the Day',
                'option_value' => '',
                'option_description' => 'Set the Message of the Day.'
            ],
            [
                'option_key' => 'site.ssl',
                'option_title' => 'Force SSL',
                'option_value' => false,
                'option_description' => 'Force the site to run under an SSL connection. Your site must be configured for SSL connections.'
            ],
            [
                'option_key' => 'site.auth',
                'option_title' => 'Require Login',
                'option_value' => false,
                'option_description' => 'Only show the site for logged in users.'
            ],
            [
                'option_key' => 'site.registration',
                'option_title' => 'Allow User Registration',
                'option_value' => true,
                'option_description' => 'Enable user registrations.'
            ]
        ];

        Option::insert($options);
    }
}
