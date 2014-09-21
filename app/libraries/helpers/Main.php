<?php namespace ADKGamers\Webadmin\Libs\Helpers;

/**
 * Copyright 2013 A Different Kind LLC. All rights reserved.
 *
 * Development by Prophet
 *
 * Version 1.5.0
 * 8-APR-2014
 */

use ADKGamers\Webadmin\Models\Battlefield\Ban;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use DateTimeZone, Auth, WebadminException, Exception;

class Main
{
    /**
     * [response description]
     * @param  integer $status  HTTP Status Code
     * @param  string  $message Response message
     * @param  array   $data
     * @return array
     */
    static public function response($status, $message, $data = array(), $statusCode = 200)
    {
        $response = array(
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        );

        return Response::json($response, $statusCode)
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT')
            ->setCallback(Input::get('callback'));
    }

    /**
     * Querys the database for the games identifer
     * @param  string $ident Game Name Short Code
     * @return integer
     */
    static public function getGameId($ident)
    {
        if(Schema::hasTable('tbl_games'))
        {
            return DB::table('tbl_games')->where('Name', strtoupper($ident))->pluck('GameID');
        }
        else return FALSE;
    }

    /**
     * Querys the database for the games identifer
     * @param  string $ident Game Name Short Code
     * @return integer
     */
    static public function getGameName($ident)
    {
        if(Schema::hasTable('tbl_games'))
        {
            return DB::table('tbl_games')->where('GameID', $ident)->pluck('Name');
        }
        else return FALSE;
    }

    /**
     * Parses the IP address and returns the port number
     * @param  string $ip IPv4 Address
     * @return integer
     */
    static public function getPort($ip = NULL)
    {
        if($c = preg_match_all("/.*?(:)(\\d+)/is", $ip, $matches))
        {
            if(strlen((string) $matches[2][0]) != 5)
            {
                throw new WebadminException("Invalid Port Number");
            }
            else return $matches[2][0];
        }
        else throw new WebadminException("Unable to parse port number");
    }

    /**
     * Parses IP or Hostname and returns the numeric IPv4 Address
     * @param  string $host IP or Hostname
     * @return string
     */
    static public function getIpAddr($host = NULL)
    {
        if($c = preg_match_all("/((?:[a-z][a-z\\.\\d\\-]+)\\.(?:[a-z][a-z\\-]+))(?![\\w\\.])/is", $host, $matches))
        {
            $filtered = gethostbyname($matches[1][0]);

            if(self::validateIPv4($filtered))
            {
                return $filtered;
            }
            else throw new WebadminException("Invalid IPv4 Address");
        }
        else if($c = preg_match_all("/((?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?))(?![\\d])/is", $host, $matches))
        {
            if(self::validateIPv4($matches[1][0]))
            {
                return $matches[1][0];
            }
            else throw new WebadminException("Invalid IPv4 Address");
        }
        else throw new WebadminException("Unable to parse IP/Hostname");
    }

    /**
     * Checks if IP is valid
     * @param  string $ip IPv4 Address
     * @return boolean
     */
    static public function validateIPv4($ip)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
        {
            return TRUE;
        }
        else return FALSE;
    }

    /**
     * Convert seconds to a human-readable string
     * @param  integer $secs
     * @param  boolean $shothand Short version time/date string
     * @return string
     */
    static public function convertSecToStr($secs = NULL, $shorthand = FALSE)
    {
        $output = '';

        // If $secs is null throw an error
        if(is_null($secs)) throw new WebadminException("Empty value not accepted");

        // If $secs is not a number throw an error
        if(!is_numeric($secs)) throw new WebadminException("Input not numeric");

        // If $secs is less than zero default to zero
        if($secs < 0) $secs = 0;

        // Week
        if($secs >= 604800)
        {
            $week = floor($secs/604800);
            $secs = $secs%604800;
            $output = $week . ' week';
            if($week != 1 && !$shorthand) $output .= 's';
            if($secs > 0) $output .= ', ';
        }

        // Day
        if($secs >= 86400)
        {
            $days   = floor($secs/86400);
            $secs   = $secs%86400;
            $output .= $days . ' day';
            if($days != 1 && !$shorthand) $output .= 's';
            if($secs > 0) $output .= ', ';
        }

        // Hour
        if($secs >= 3600)
        {
            $hours  = floor($secs/3600);
            $secs   = $secs%3600;
            $output .= $hours . ' hour';
            if($hours != 1 && !$shorthand) $output .= 's';
            if($secs > 0) $output .= ', ';
        }

        // Minute
        if($secs >= 60)
        {
            $minutes = floor($secs/60);
            $secs    = $secs%60;
            $output  .= $minutes . ' minute';
            if($minutes != 1 && !$shorthand) $output .= 's';
            if($secs > 0) $output .= ', ';
        }

        if($secs > 0)
        {
            $output .= $secs.' second';

            if($secs != 1 && !$shorthand) $output .= 's';
        }

        // If short version is request replace all
        // long values with the abbreviation
        if($shorthand)
        {
            $output = str_replace(
                array(" day", " hour", " minute", " second", " week"),
                array("d", "h", "m", "s", "w"),
                $output
            );
        }

        return $output;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     *
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    static public function getGravatar( $email, $s = 80, $d = 'mm', $r = 'x', $img = false, $atts = array() )
    {
        $url = '//www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";

        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }

        return $url;
    }

    /**
     * Calculate the percentage between two numbers
     * @param  integer  $int1      First number
     * @param  integer  $int2      Second Number
     * @param  integer  $precision  Show how many numbers after the dot
     * @return integer/floatval
     */
    static public function calculPercentage($int1 = 0, $int2 = 0, $precision = 2)
    {
        try
        {
            return round( ($int1 / $int2) * 100, $precision );
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    /**
     * Attempt to divide between two numbers
     * Returns zero if Division by zero exception occurs
     * @param  integer  $int1      First number
     * @param  integer  $int2      Second Number
     * @param  integer  $precision  Show how many numbers after the dot
     * @return integer/floatval
     */
    static public function divide($int1 = 0, $int2 = 0, $precision = 2)
    {
        try
        {
            return round( ($int1 / $int2), $precision );
        }
        catch(Exception $e)
        {
            return 0;
        }
    }

    /**
     * Converts UTC to user local time
     * @param mixed $stamp String or Integer
     * @return object Carbon class
     */
    static public function UTCToLocal($stamp, $tz = FALSE)
    {
        try
        {
            if($stamp instanceof Carbon\Carbon)
            {
                // Do nothing
            }
            elseif(is_integer($stamp))
            {
                $stamp = date('Y-m-d H:i:s', $stamp);
            }
            else $stamp = date('Y-m-d H:i:s', strtotime($stamp));

            $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $stamp, new DateTimeZone('UTC'));

            if(Auth::check() && $tz === FALSE)
            {
                $userTZ = Auth::user()->preferences->timezone;
            }
            else $userTZ = ( $tz ?: 'UTC');

            if($userTZ != 'UTC') $datetime->setTimezone(new DateTimeZone($userTZ));

            return $datetime;
        }
        catch(Exception $e)
        {
            return Carbon::parse($stamp);
        }
    }

    /**
     * Converts user local time to UTC
     * @param mixed $stamp String or Integer
     * @return object Carbon class
     */
    static public function LocalToUTC($stamp, $tz = FALSE)
    {
        if(is_integer($stamp))
        {
            $stamp = date('Y-m-d H:i:s', $stamp);
        }
        else $stamp = date('Y-m-d H:i:s', strtotime($stamp));

        $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $stamp, new DateTimeZone( ($tz !== FALSE ? $tz : Auth::user()->preferences->timezone) ));

        $datetime->setTimezone(new DateTimeZone('UTC'));

        return $datetime;
    }

    /**
     * Cleans up the HTML provided by the user
     * and removes harmful code
     * @param  string $string Raw HTML
     * @return string Cleaned up HTML
     */
    static public function cleanupHTML($string)
    {
        // Remove any <script></script> tags and content inside them
        $string = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $string);

        // Replace new lines
        $string = preg_replace("/\r\n|\r|\n/", '', rtrim($string));

        // Return back the cleaned up html
        return trim($string);
    }

    /**
     * Generate a timezone list
     * @source http://stackoverflow.com/a/17355238
     * @return array
     */
    static public function generateTimezoneList()
    {
        static $regions = array(
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
            DateTimeZone::UTC
        );

        $timezones = array();
        foreach( $regions as $region )
        {
            $timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
        }

        $timezone_offsets = array();
        foreach( $timezones as $timezone )
        {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new Carbon);
        }

        // sort timezone by offset
        asort($timezone_offsets);

        $timezone_list = array();
        foreach( $timezone_offsets as $timezone => $offset )
        {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate( 'H:i', abs($offset) );

            $pretty_offset = "UTC ${offset_prefix}${offset_formatted}";

            $timezone_list[$timezone] = "(${pretty_offset}) $timezone";
        }

        return $timezone_list;
    }

    /**
     * Show a status bar in the console
     * @param  integer  $done      how many items are completed
     * @param  integer  $total     how many items are to be done total
     * @param  integer  $size      optional size of the status bar
     * @param  integer  $lineWidth
     * @return void
     * @source http://stackoverflow.com/a/24947217/1318205
     */
    static public function showCLIStatus($done, $total, $size = 30, $lineWidth = -1)
    {
        if($lineWidth <= 0)
        {
            $lineWidth = $_ENV['COLUMNS'];
        }

        static $start_time;

        // to take account for [ and ]
        $size -= 3;

        // if we go over our bound, just ignore it
        if($done > $total) return;

        if(empty($start_time)) $start_time = time();

        $now  = time();

        $perc = self::divide( $done, $total );

        $bar  = floor($perc * $size);

        // jump to the begining
        echo "\r";

        // jump a line up
        echo "\x1b[A";

        $status_bar = "[";

        $status_bar .= str_repeat("=", $bar);

        if($bar < $size)
        {
            $status_bar .= ">";
            $status_bar .= str_repeat(" ", $size-$bar);
        }
        else
        {
            $status_bar .= "=";
        }

        $disp = number_format($perc * 100, 0);

        $status_bar.="]";
        $details = "$disp%  $done/$total";

        $rate = self::divide( ($now - $start_time), $done );
        $left = $total - $done;
        $eta  = round($rate * $left, 2);

        $elapsed = $now - $start_time;


        $details .= " " . self::convertSecToStr($eta) . " " . self::convertSecToStr($elapsed);

        $lineWidth--;

        if(strlen($details) >= $lineWidth)
        {
            $details = substr($details, 0, $lineWidth-1);
        }

        echo "$details\n$status_bar";

        flush();

        // when done, send a newline
        if($done == $total)
        {
            echo "\n";
        }
    }

    static public function timeRemainingDifference($startDate, $endDate)
    {
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $startDate);
        $end   = Carbon::createFromFormat('Y-m-d H:i:s', $endDate);
        $diff  = $start->diff($end);
        $temp  = '';

        $years   = $diff->y;
        $months  = $diff->m;
        $days    = $diff->d;
        $days2   = $diff->days;
        $hours   = $diff->h;
        $minutes = $diff->i;
        $seconds = $diff->s;

        // Only show day if greater than 1 day
        if($days2 > 0)
        {
            if($years > 0)
            {
                $temp .= $years . "y ";
            }

            if($months > 0)
            {
                $temp .= $months . "m ";
            }

            if($years > 0 || $months > 0)
            {
                if($days > 0)
                {
                    $temp .= $days . "d ";
                }
            }
            else
            {
                $temp .= $days2 . "d ";
            }
        }

        // Only show more information if less than 35 days
        if($days2 < 35)
        {
            // Only show hours if days exist, or hours > 0
            if($hours > 0 || $days2 > 0)
            {
                $temp .= $hours . "h ";
            }

            // Only show more infomation if less than 1 day
            if($days2 < 1)
            {
                // Only show minutes if Hours exist, or minutes > 0
                if($minutes > 0 || $hours > 0)
                {
                    $temp .= $minutes . "m ";
                }

                // Only show more infomation if less than 1 hour
                if($hours < 1)
                {
                    // Only show seconds if minutes exist, or seconds > 0
                    if($seconds > 0 || $minutes > 0)
                    {
                        $temp .= $seconds . "s";
                    }
                }
            }
        }

        if($temp === '')
        {
            return '::';
        }
        else return trim($temp);
    }

    /**
     * List of countries
     * @param  string $code Return specific country
     * @return array/string/bool
     */
    static public function countries($code = NULL)
    {
        $countries = array(
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
        );

        if(!is_null($code))
        {
            $code = strtoupper($code);

            if(array_key_exists($code, $countries))
            {
                return $countries[$code];
            }
            else
            {
                return FALSE;
            }
        }

        return $countries;
    }

    static public function _empty($val)
    {
        return empty($val);
    }
}
