<?php namespace BFACP\Contracts;

interface Scoreboard
{
    /**
     * Determine if we are connected and authenticated.
     *
     * @return bool
     */
    public function check();

    /**
     * Attempt to establish connection and login.
     *
     * @param  string  $ip   Server IPv4 Address
     * @param  integer $port Server RCON Port
     * @param  string  $pass Server RCON Password
     * @return bool
     */
    public function attempt($ip, $port, $pass);
}
