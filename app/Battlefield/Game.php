<?php namespace BFACP\Battlefield;

use BFACP\Elegant;

class Game extends Elegant
{
    /**
     * Should model handle timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'tbl_games';

    /**
     * Table primary key
     *
     * @var string
     */
    protected $primaryKey = 'GameID';

    /**
     * Fields not allowed to be mass assigned
     *
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * Date fields to convert to carbon instances
     *
     * @var array
     */
    protected $dates = [];

    /**
     * Append custom attributes to output
     *
     * @var array
     */
    protected $appends = ['class_css'];

    /**
     * Models to be loaded automaticly
     *
     * @var array
     */
    protected $with = [];

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function servers()
    {
        return $this->hasMany('BFACP\Battlefield\Server\Server', 'GameID');
    }

    public function getClassCssAttribute()
    {
        switch ($this->Name) {
            case 'BF3':
                $class = 'label bg-purple';
                break;

            case 'BF4':
                $class = 'label bg-blue';
                break;

            case 'BFH':
            case 'BFHL':
                $class = 'label bg-green';
                break;

            default:
                $class = 'label bg-yellow';
        }

        return $class;
    }
}
