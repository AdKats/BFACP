<?php namespace BFACP\Libraries;

/**
 * Base 62 Encoder / Decoder Class
 * (c) Andy Huang, 2009; All rights reserved
 * This code is not distributed under any specific license,
 * as I do not believe in them, but it is distributed under
 * these terms outlined below:
 * - You may use these code as part of your application, even if it is a commercial product
 * - You may modify these code to suite your application, even if it is a commercial product
 * - You may sell your commercial product derived from these code
 * - You may donate to me if you are some how able to get a hold of me, but that's not required
 * - You may link back to the original article for reference, but do not hotlink the source file
 * - This line is intentionally added to differentiate from LGPL, or other similar licensing terms
 * - You must at all time retain this copyright message and terms in your code
 */
class Base62
{
    static $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    static $base = 62;

    public function encode($var)
    {
        $stack = [];

        while (bccomp($var, 0) != 0) {
            $remainder = bcmod($var, self::$base);
            $var = bcdiv(bcsub($var, $remainder), self::$base);
            array_push($stack, self::$characters[ $remainder ]);
        }

        return implode('', array_reverse($stack));
    }

    public function decode($var)
    {
        $length = strlen($var);
        $result = 0;

        for ($i = 0; $i < $length; $i++) {
            $result = bcadd($result, bcmul(self::getDigit($var[ $i ]), bcpow(self::$base, ($length - ($i + 1)))));
        }

        return $result;
    }

    private function getDigit($var)
    {
        if (preg_match('/[0-9]/', $var)) {
            return (int)(ord($var) - ord('0'));
        } else {
            if (preg_match('/[A-Z]/', $var)) {
                return (int)(ord($var) - ord('A') + 10);
            } else {
                if (preg_match('/[a-z]/', $var)) {
                    return (int)(ord($var) - ord('a') + 36);
                } else {
                    return $var;
                }
            }
        }
    }
}
