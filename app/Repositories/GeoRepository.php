<?php

namespace BFACP\Repositories;

/**
 * Class GeoRepository.
 */
class GeoRepository extends BaseRepository
{
    /**
     * GeoIP2\Database\Reader.
     *
     * @var object
     */
    protected $geo;

    /**
     * IP Address.
     *
     * @var null
     */
    protected $ip = null;

    /**
     * GeoIP Reader.
     *
     * @var object
     */
    protected $reader;

    public function __construct()
    {
        parent::__construct();

        $this->geo = app('GeoIP');
    }

    /**
     * Set the IP Address to be used.
     *
     * @param string $ip IPv4 Address
     *
     * @return $this
     */
    public function set($ip)
    {
        $this->ip = $ip;
        $this->reader = $this->geo->city($this->ip);

        return $this;
    }

    /**
     * Simply returns all values.
     *
     * @return array
     */
    public function all()
    {
        return [
            'cc'          => $this->cc(),
            'country'     => $this->country(),
            'city'        => $this->city(),
            'lat'         => $this->lat(),
            'lon'         => $this->lon(),
            'postal'      => $this->postal(),
            'flag'        => $this->flag(),
            'subdivision' => [
                'name' => $this->subDivisionName(),
                'iso'  => $this->subDivisionIso(),
            ],
        ];
    }

    /**
     * Returns the country ISO Code.
     *
     * @return string
     */
    public function cc()
    {
        return $this->reader->country->isoCode;
    }

    /**
     * Returns the country name.
     *
     * @return string
     */
    public function country()
    {
        return $this->reader->country->name;
    }

    /**
     * Returns the name of the city the IP is located in.
     *
     * @return string
     */
    public function city()
    {
        return $this->reader->city->name;
    }

    /**
     * Returns the latitude.
     *
     * @return float
     */
    public function lat()
    {
        return $this->reader->location->latitude;
    }

    /**
     * Returns the longitude.
     *
     * @return float
     */
    public function lon()
    {
        return $this->reader->location->longitude;
    }

    /**
     * Returns the postal code of the city.
     *
     * @return string
     */
    public function postal()
    {
        return $this->reader->postal->code;
    }

    /**
     * Returns the file path to the country flag.
     *
     * @return string
     */
    public function flag()
    {
        return sprintf('images/flags/24/%s.png', strtoupper($this->cc()));
    }

    /**
     * Returns the name of the subdivision of the country.
     *
     * @return mixed
     */
    public function subDivisionName()
    {
        return $this->reader->mostSpecificSubdivision->name;
    }

    /**
     * Returns the iso code of the subdivision of the country.
     *
     * @return mixed
     */
    public function subDivisionIso()
    {
        return $this->reader->mostSpecificSubdivision->isoCode;
    }
}
