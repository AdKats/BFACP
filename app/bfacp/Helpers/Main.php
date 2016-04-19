<?php namespace BFACP\Helpers;

use BFACP\Account\User;
use BFACP\Adkats\Setting as AdKatsSetting;
use BFACP\Battlefield\Player;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class Main extends BaseHelper
{
    /**
     * Return a JSON response
     *
     * @param  array   $input
     * @param  string  $message
     * @param  string  $status
     * @param  integer $httpcode
     * @param  boolean $cached
     * @param  boolean $collectionOnly
     *
     * @return Response
     */
    public function response(
        $input = [],
        $message = 'OK',
        $status = 'success',
        $httpcode = 200,
        $cached = false,
        $collectionOnly = false
    ) {
        if (is_null($message)) {
            $message = 'OK';
        }

        if (is_null($status)) {
            $status = 'success';
        }

        if (is_null($httpcode)) {
            $httpcode = 200;
        }

        if (is_null($input)) {
            $input = [];
        }

        $collection = new Collection([
            'status'         => $status,
            'message'        => $message,
            'execution_time' => $this->executionTime(),
            'cached'         => $cached,
            'data'           => $input,
        ]);

        if ($collectionOnly) {
            return $collection;
        }

        return $this->response->json($collection, $httpcode, [], JSON_NUMERIC_CHECK)->header('X-Robots-Tag',
            'noindex')->header('Cache-Control',
            'no-cache, must-revalidate');
    }

    /**
     * Returns how long the application took to complete
     *
     * @param bool $isPage
     *
     * @return string
     * @throws Exception
     */
    public function executionTime($isPage = false)
    {
        $time = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 2);

        if ($isPage) {
            $string = 'Page generated in ';
        } else {
            return [
                'time' => $time,
                'text' => 'Data crunched in ' . $this->secToStr($time),
            ];
        }

        return $string . $this->secToStr($time);
    }

    /**
     * Convert seconds to a human-readable string
     *
     * @param  integer $secs
     * @param bool     $shorthand
     *
     * @return string
     * @throws Exception
     * @internal param bool $shorthand Short version time/date string
     */
    public function secToStr($secs = null, $shorthand = false)
    {
        $output = '';

        // If $secs is null throw an error
        if (is_null($secs)) {
            throw new Exception('Empty value not accepted');
        }

        // If $secs is not a number throw an error
        if (!is_numeric($secs)) {
            throw new Exception('Input not numeric');
        }

        // If $secs is less than zero default to zero
        if ($secs < 0) {
            $secs = 0;
        }

        // Week
        if ($secs >= 604800) {
            $week = floor($secs / 604800);
            $secs = $secs % 604800;
            $output = $week . ' week';
            if ($week != 1 && !$shorthand) {
                $output .= 's';
            }

            if ($secs > 0) {
                $output .= ', ';
            }

        }

        // Day
        if ($secs >= 86400) {
            $days = floor($secs / 86400);
            $secs = $secs % 86400;
            $output .= $days . ' day';
            if ($days != 1 && !$shorthand) {
                $output .= 's';
            }

            if ($secs > 0) {
                $output .= ', ';
            }

        }

        // Hour
        if ($secs >= 3600) {
            $hours = floor($secs / 3600);
            $secs = $secs % 3600;
            $output .= $hours . ' hour';
            if ($hours != 1 && !$shorthand) {
                $output .= 's';
            }

            if ($secs > 0) {
                $output .= ', ';
            }

        }

        // Minute
        if ($secs >= 60) {
            $minutes = floor($secs / 60);
            $secs = $secs % 60;
            $output .= $minutes . ' minute';
            if ($minutes != 1 && !$shorthand) {
                $output .= 's';
            }

            if ($secs > 0) {
                $output .= ', ';
            }

        }

        // Second
        if ($secs > 0) {
            $output .= $secs . ' second';

            if ($secs != 1 && !$shorthand) {
                $output .= 's';
            }

        }

        // If short version is requested replace all
        // long values with the abbreviation
        if ($shorthand) {
            $output = str_replace([' day', ' hour', ' minute', ' second', ' week'], ['d', 'h', 'm', 's', 'w'], $output);
        }

        return $output;
    }

    /**
     * Function to divide two numbers together and catch
     * divide by zero exception
     *
     * @param  integer $num1
     * @param  integer $num2
     * @param  integer $precision
     *
     * @return float
     */
    public function divide($num1 = 0, $num2 = 0, $precision = 2)
    {
        try {
            return round(($num1 / $num2), $precision);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Function to get percentage of two numbers together and
     * catch divide by zero exception
     *
     * @param  integer $num1
     * @param  integer $num2
     * @param  integer $precision
     *
     * @return float
     */
    public function percent($num1 = 0, $num2 = 0, $precision = 2)
    {
        try {
            return round(($num1 / $num2) * 100, $precision);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Allows the ability to call empty on a static class method
     *
     * @param  mixed $var
     *
     * @return boolean
     */
    public function isEmpty($var)
    {
        return empty($var);
    }

    /**
     * Generates the site title
     *
     * @param  string  $page  Page Title
     * @param  string  $clan  Use clan name if set
     * @param  boolean $short If true it will just return without the page title
     *
     * @return string
     */
    public function getTitle($page, $clan = null, $short = false)
    {
        $title = '';

        if (!$short && $page !== false) {
            $title .= $page . ' | ';
        }

        if (!is_null($clan)) {
            $title .= $clan;
        } else {
            $title .= 'BFAdminCP';
        }

        return $title;
    }

    /**
     * Return country name by code
     *
     * @param  string $code Two digit country code
     * @param bool    $list
     *
     * @return string
     */
    public function countries($code = null, $list = false)
    {
        $countries = [
            'AF' => 'Afghanistan',
            'AX' => 'Aland Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AQ' => 'Antarctica',
            'AG' => 'Antigua And Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia And Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Republic',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo',
            'CD' => 'Congo, Democratic Republic',
            'CK' => 'Cook Islands',
            'CR' => 'Costa Rica',
            'CI' => 'Cote D\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands (Malvinas)',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GG' => 'Guernsey',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island & Mcdonald Islands',
            'VA' => 'Holy See (Vatican City State)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran, Islamic Republic Of',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IM' => 'Isle Of Man',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JE' => 'Jersey',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KR' => 'Korea',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libyan Arab Jamahiriya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States Of',
            'MD' => 'Moldova',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestinian Territory, Occupied',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russian Federation',
            'RW' => 'Rwanda',
            'BL' => 'Saint Barthelemy',
            'SH' => 'Saint Helena',
            'KN' => 'Saint Kitts And Nevis',
            'LC' => 'Saint Lucia',
            'MF' => 'Saint Martin',
            'PM' => 'Saint Pierre And Miquelon',
            'VC' => 'Saint Vincent And Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome And Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia And Sandwich Isl.',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard And Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad And Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks And Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UM' => 'United States Outlying Islands',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands, British',
            'VI' => 'Virgin Islands, U.S.',
            'WF' => 'Wallis And Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];

        if (is_null($code) && $list) {
            return $countries;
        }

        $code = strtoupper($code);

        if (array_key_exists($code, $countries)) {
            return $countries[ $code ];
        }

        return;
    }

    /**
     * Returns the language name if code is specified
     *
     * @param  string $lang     Language Code
     * @param  bool   $onlyKeys Only return comma delimited list
     *
     * @return mixed        String or Array
     */
    public function languages($lang = '', $onlyKeys = false)
    {
        $languages = [
            'aa' => 'Afar',
            'ab' => 'Abkhaz',
            'ae' => 'Avestan',
            'af' => 'Afrikaans',
            'ak' => 'Akan',
            'am' => 'Amharic',
            'an' => 'Aragonese',
            'ar' => 'Arabic',
            'as' => 'Assamese',
            'av' => 'Avaric',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'ba' => 'Bashkir',
            'be' => 'Belarusian',
            'bg' => 'Bulgarian',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bm' => 'Bambara',
            'bn' => 'Bengali',
            'bo' => 'Tibetan Standard, Tibetan, Central',
            'br' => 'Breton',
            'bs' => 'Bosnian',
            'ca' => 'Catalan; Valencian',
            'ce' => 'Chechen',
            'ch' => 'Chamorro',
            'co' => 'Corsican',
            'cr' => 'Cree',
            'cs' => 'Czech',
            'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
            'cv' => 'Chuvash',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'dv' => 'Divehi; Dhivehi; Maldivian;',
            'dz' => 'Dzongkha',
            'ee' => 'Ewe',
            'el' => 'Greek, Modern',
            'en' => 'English',
            'eo' => 'Esperanto',
            'es' => 'Spanish; Castilian',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Persian',
            'ff' => 'Fula; Fulah; Pulaar; Pular',
            'fi' => 'Finnish',
            'fj' => 'Fijian',
            'fo' => 'Faroese',
            'fr' => 'French',
            'fy' => 'Western Frisian',
            'ga' => 'Irish',
            'gd' => 'Scottish Gaelic; Gaelic',
            'gl' => 'Galician',
            'gn' => 'GuaranÃ­',
            'gu' => 'Gujarati',
            'gv' => 'Manx',
            'ha' => 'Hausa',
            'he' => 'Hebrew (modern)',
            'hi' => 'Hindi',
            'ho' => 'Hiri Motu',
            'hr' => 'Croatian',
            'ht' => 'Haitian; Haitian Creole',
            'hu' => 'Hungarian',
            'hy' => 'Armenian',
            'hz' => 'Herero',
            'ia' => 'Interlingua',
            'id' => 'Indonesian',
            'ie' => 'Interlingue',
            'ig' => 'Igbo',
            'ii' => 'Nuosu',
            'ik' => 'Inupiaq',
            'io' => 'Ido',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'iu' => 'Inuktitut',
            'ja' => 'Japanese (ja)',
            'jv' => 'Javanese (jv)',
            'ka' => 'Georgian',
            'kg' => 'Kongo',
            'ki' => 'Kikuyu, Gikuyu',
            'kj' => 'Kwanyama, Kuanyama',
            'kk' => 'Kazakh',
            'kl' => 'Kalaallisut, Greenlandic',
            'km' => 'Khmer',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'kr' => 'Kanuri',
            'ks' => 'Kashmiri',
            'ku' => 'Kurdish',
            'kv' => 'Komi',
            'kw' => 'Cornish',
            'ky' => 'Kirghiz, Kyrgyz',
            'la' => 'Latin',
            'lb' => 'Luxembourgish, Letzeburgesch',
            'lg' => 'Luganda',
            'li' => 'Limburgish, Limburgan, Limburger',
            'ln' => 'Lingala',
            'lo' => 'Lao',
            'lt' => 'Lithuanian',
            'lu' => 'Luba-Katanga',
            'lv' => 'Latvian',
            'mg' => 'Malagasy',
            'mh' => 'Marshallese',
            'mi' => 'Maori',
            'mk' => 'Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'mr' => 'Marathi (Mara?hi)',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'my' => 'Burmese',
            'na' => 'Nauru',
            'nb' => 'Norwegian BokmÃ¥l',
            'nd' => 'North Ndebele',
            'ne' => 'Nepali',
            'ng' => 'Ndonga',
            'nl' => 'Dutch',
            'nn' => 'Norwegian Nynorsk',
            'no' => 'Norwegian',
            'nr' => 'South Ndebele',
            'nv' => 'Navajo, Navaho',
            'ny' => 'Chichewa; Chewa; Nyanja',
            'oc' => 'Occitan',
            'oj' => 'Ojibwe, Ojibwa',
            'om' => 'Oromo',
            'or' => 'Oriya',
            'os' => 'Ossetian, Ossetic',
            'pa' => 'Panjabi, Punjabi',
            'pi' => 'Pali',
            'pl' => 'Polish',
            'ps' => 'Pashto, Pushto',
            'pt' => 'Portuguese',
            'qu' => 'Quechua',
            'rm' => 'Romansh',
            'rn' => 'Kirundi',
            'ro' => 'Romanian, Moldavian, Moldovan',
            'ru' => 'Russian',
            'rw' => 'Kinyarwanda',
            'sa' => 'Sanskrit (Sa?sk?ta)',
            'sc' => 'Sardinian',
            'sd' => 'Sindhi',
            'se' => 'Northern Sami',
            'sg' => 'Sango',
            'si' => 'Sinhala, Sinhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovene',
            'sm' => 'Samoan',
            'sn' => 'Shona',
            'so' => 'Somali',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'ss' => 'Swati',
            'st' => 'Southern Sotho',
            'su' => 'Sundanese',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'tg' => 'Tajik',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'tk' => 'Turkmen',
            'tl' => 'Tagalog',
            'tn' => 'Tswana',
            'to' => 'Tonga (Tonga Islands)',
            'tr' => 'Turkish',
            'ts' => 'Tsonga',
            'tt' => 'Tatar',
            'tw' => 'Twi',
            'ty' => 'Tahitian',
            'ug' => 'Uighur, Uyghur',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            've' => 'Venda',
            'vi' => 'Vietnamese',
            'vo' => 'VolapÃ¼k',
            'wa' => 'Walloon',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yi' => 'Yiddish',
            'yo' => 'Yoruba',
            'za' => 'Zhuang, Chuang',
            'zh' => 'Chinese',
            'zu' => 'Zulu',
        ];

        if (!empty($lang) && array_key_exists($lang, $languages)) {
            return $languages[ $lang ];
        }

        if ($onlyKeys) {
            return implode(',', array_keys($languages));
        }

        return $languages;
    }

    /**
     * Returns the correct soldier assigned to user for the correct game.
     *
     * @param  \BFACP\Account\User $user
     * @param  integer             $gameID
     *
     * @return Player
     */
    public function getAdminPlayer(User $user, $gameID)
    {
        $soldiers = $user->soldiers->filter(function ($soldier) use ($gameID) {
            // Only return true if the user has a matching soldier with the game
            if ($soldier->player->game->GameID == $gameID) {
                return true;
            }
        });

        // Check if we have a soldier.
        if (!empty($soldiers) && count($soldiers) > 0) {
            $soldier = head(array_flatten($soldiers));

            return $soldier->player;
        }

        // Return null if no match was able too be met.
        return;
    }

    /**
     * Generates a strong password of N length containing at least one lower case letter,
     * one uppercase letter, one digit, and one special character. The remaining characters
     * in the password are chosen at random from those four sets.
     * The available characters in each set are user friendly - there are no ambiguous
     * characters such as i, l, 1, o, 0, etc. This, coupled with the $add_dashes option,
     * makes it much easier for users to manually type or speak their passwords.
     * Note: the $add_dashes option will increase the length of the password by
     * floor(sqrt(N)) characters.
     *
     * @param  integer $length
     * @param  boolean $add_dashes
     * @param  string  $available_sets
     *
     * @return string
     * @source https://gist.github.com/tylerhall/521810
     */
    public function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = [];
        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }

        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }

        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }

        if (strpos($available_sets, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }

        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[ array_rand(str_split($set)) ];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[ array_rand($all) ];
        }

        $password = str_shuffle($password);

        if (!$add_dashes) {
            return $password;
        }

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;

        return $dash_str;
    }

    /**
     * Converts a string to a boolean
     *
     * @param  string $string
     *
     * @return boolean
     */
    public function stringToBool($string)
    {
        if ($string === 1 || $string === '1' || $string === 'true' || $string === true) {
            return true;
        } else {
            if ($string === 0 || $string === '0' || $string === 'false' || $string === false) {
                return false;
            }
        }

        return;
    }

    /**
     * Converts log error codes to correct css styles
     *
     * @param  string $name Exception Level
     *
     * @return string
     */
    public function alertToBoxClass($name = null)
    {
        switch ($name) {
            case 'emergency':
            case 'alert':
            case 'critical':
            case 'error':
                $style = 'danger';
                break;

            case 'notice':
                $style = 'warning';
                break;

            case 'debug':
                $style = 'info';
                break;

            default:
                $style = $name;
        }

        return $style;
    }

    /**
     * Returns files in a directory
     *
     * @param  string  $dir       Directory Path
     * @param  boolean $onlyNames Only return the filename
     * @param  string  $prepend   Prepend custom path to use in front of filename
     *
     * @return array
     */
    public function files($dir, $onlyNames = false, $prepend = null)
    {
        $files = [];

        foreach (File::files($dir) as $path) {
            if ($onlyNames) {
                $file = pathinfo($path);

                $extension = '.' . $file['extension'];

                $files[] = $prepend === null ? $file['filename'] . $extension : $prepend . $file['filename'] . $extension;
            } else {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Returns a list of accounts that match $player
     *
     * @param  object $player \BFACP\Battlefield\Player
     *
     * @return array
     */
    public function linkedAccounts($player)
    {
        $players = Player::where('PlayerID', '!=', $player->PlayerID)->where(function ($query) use (&$player) {
            if (!empty($player->EAGUID)) {
                $query->orWhere('EAGUID', $player->EAGUID);
            }

            if (!empty($player->PBGUID)) {
                $query->orWhere('PBGUID', $player->PBGUID);
            }

            if (!empty($player->SoldierName)) {
                $query->orWhere('SoldierName', $player->SoldierName);
            }

            if (!empty($player->IP_Address)) {
                $query->orWhere('IP_Address', $player->IP_Address);
            }
        });

        return $players->get();
    }

    /**
     * Returns the valid AdKats Special groups
     *
     * @param null $keys   Only return the requested group(s)
     * @param null $objkey The object property to use if $keys contains array of objects
     *
     * @return mixed
     */
    public function specialGroups($keys = null, $objkey = null)
    {
        $groups = $this->cache->remember('admin.adkats.special.groups', 60 * 24, function () {
            try {
                $request = $this->guzzle->get('https://raw.githubusercontent.com/AdKats/AdKats/master/adkatsspecialgroups.json');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            } catch (RequestException $e) {
                $request = $this->guzzle->get('http://api.gamerethos.net/adkats/fetch/specialgroups');
                $response = $request->json();
                $data = $response['SpecialGroups'];
            }

            return new Collection($data);
        });

        if (!is_null($keys)) {
            return $groups->filter(function ($group) use (&$keys, &$objkey) {
                if (is_array($keys)) {
                    foreach ($keys as $k) {
                        if (is_object($k)) {
                            if ($k->{$objkey} == $group['group_key']) {
                                return true;
                            }
                        } else {
                            if ($k == $group['group_key']) {
                                return true;
                            }
                        }
                    }
                } else {
                    if ($keys == $group['group_key']) {
                        return true;
                    }
                }
            })->map(function ($group) use (&$keys, &$objkey) {
                $special = [];

                if (is_array($keys)) {
                    foreach ($keys as $k) {
                        if (is_object($k)) {
                            if ($k->{$objkey} == $group['group_key']) {
                                $special = $k;
                                break;
                            }
                        } else {
                            if ($k == $group['group_key']) {
                                $special = $k;
                                break;
                            }
                        }
                    }
                }

                return array_merge($group, (array)$special);
            });
        }

        return $groups;
    }

    /**
     * Checks if the $column has fulltext support.
     *
     * @param $table
     * @param $column
     *
     * @return bool
     */
    public function hasFulltextSupport($table, $column)
    {
        $sql = File::get(storage_path() . '/sql/fulltextCheck.sql');

        $results = DB::select($sql, [Config::get('database.connections.mysql.database'), $table]);

        if (count($results) > 0) {
            foreach ($results as $result) {
                if ($result->column_name == $column) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Gets users gravatar image
     *
     * @param string $email
     * @param string $hash
     * @param int    $size
     *
     * @return string|void
     */
    public function gravatar($email = '', $hash = '', $size = 80)
    {
        if (!empty($email)) {
            return sprintf('https://www.gravatar.com/avatar/%s?s=%u&d=mm&r=x', md5(strtolower(trim($email))), $size);
        }

        if (!empty($hash)) {
            return sprintf('https://www.gravatar.com/avatar/%s?s=%u&d=mm&r=x', $hash, $size);
        }

        return sprintf('https://www.gravatar.com/avatar/?s=%u&d=mm&r=x', $size);
    }

    /**
     * Gets the next punishment name
     *
     * @param string|null  $key
     * @param integer|null $index
     * @param integer|null $serverid
     *
     * @return string|null
     */
    public function getNextPunishment($key, $index = null, $serverid = null)
    {
        try {
            // If the index is less than 1 just return false
            if ($index < 1) {
                return false;
            }

            if (!is_null($index) && !is_null($serverid)) {
                $settings = AdKatsSetting::servers($serverid)->settings('Punishment Hierarchy')->first();

                $key = $settings->setting_value[ $index ];
            }

            $hierarchy = [
                'warn'       => 'Warn',
                'kill'       => 'Kill',
                'kick'       => 'Kick',
                'tban60'     => 'Temp-Ban 1 Hour',
                'tban120'    => 'Temp-Ban 2 Hours',
                'tbanday'    => 'Temp-Ban 1 Day',
                'tbanweek'   => 'Temp-Ban 1 Week',
                'tban2weeks' => 'Temp-Ban 2 Weeks',
                'tbanmonth'  => 'Temp-Ban 1 Month',
                'ban'        => 'Perma-Ban',
            ];

            if (array_key_exists($key, $hierarchy)) {
                return $hierarchy[ $key ];
            }
        } catch (Exception $e) {
        }

        return null;
    }
}
