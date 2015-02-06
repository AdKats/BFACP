<?php namespace BFACP\Helpers;

use Exception;
use Illuminate\Support\Collection;

class Main extends BaseHelper
{
    /**
     * Return a JSON response
     * @param  array   $input
     * @param  string  $message
     * @param  string  $status
     * @param  integer $httpcode
     * @param  boolean $cached
     * @param  boolean $collectionOnly
     * @return \Illuminate\Support\Facades\Response
     */
    public function response($input = [], $message = 'OK', $status = 'success', $httpcode = 200, $cached = FALSE, $collectionOnly = FALSE)
    {
        if(is_null($message)) $message = 'OK';
        if(is_null($status)) $status = 'success';
        if(is_null($httpcode)) $httpcode = 200;

        $collection = new Collection([
            'status'         => $status,
            'message'        => $message,
            'execution_time' => $this->executionTime(),
            'cached'         => $cached,
            'data'           => $input
        ]);

        if($collectionOnly)
            return $collection;

        return $this->response->json($collection, $httpcode)
            ->header('X-Robots-Tag', 'noindex')
            ->header('Cache-Control', 'no-cache, must-revalidate')
            ->header('Expires', $this->carbon->now()->subYears(10)->format("D, d M Y H:i:s \G\M\T"));
    }

    /**
     * Returns how long the application took to complete
     *
     * @return string
     */
    public function executionTime($isPage = FALSE)
    {
        $time = round( (microtime(true) - LARAVEL_START), 2);

        if($isPage) $string = "Page generated in ";
        else $string = "Data crunched in ";

        return $string . $this->secToStr($time);
    }

    /**
     * Function to divide two numbers together and catch
     * divide by zero exception
     *
     * @param  integer $num1
     * @param  integer $num2
     * @param  integer $precision
     * @return floatval
     */
    public function divide($num1 = 0, $num2 = 0, $precision = 2)
    {
        try {
            return round( ($num1 / $num2), $precision );
        } catch(Exception $e) {
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
     * @return floatval
     */
    public function percent($num1 = 0, $num2 = 0, $precision = 2)
    {
        try {
            return round( ($num1 / $num2) * 100, $precision );
        } catch(Exception $e) {
            return 0;
        }
    }

    /**
     * Allows the ability to call empty on a static class method
     * @param  mixed  $var
     * @return boolean
     */
    public function isEmpty($var)
    {
        return empty($var);
    }

    /**
     * Convert seconds to a human-readable string
     * @param  integer $secs
     * @param  boolean $shothand Short version time/date string
     * @return string
     */
    public function secToStr($secs = NULL, $shorthand = FALSE)
    {
        $output = '';

        // If $secs is null throw an error
        if(is_null($secs)) throw new Exception("Empty value not accepted");

        // If $secs is not a number throw an error
        if(!is_numeric($secs)) throw new Exception("Input not numeric");

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

        // Second
        if($secs > 0)
        {
            $output .= $secs.' second';

            if($secs != 1 && !$shorthand) $output .= 's';
        }

        // If short version is requested replace all
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
     * Generates the site title
     * @param  string  $page  Page Title
     * @param  stirng  $clan  Use clan name if set
     * @param  boolean $short If true it will just return without the page title
     * @return string
     */
    public function getTitle($page, $clan = NULL, $short = FALSE)
    {
    	$title = '';

    	if(!$short)
    	{
    		$title .= $page . ' | ';
    	}

    	if(!is_null($clan))
    	{
    		$title .= $clan;
    	}
    	else
    	{
    		$title .= 'BFAdminCP';
    	}

    	return $title;
    }
}
