<?php namespace BFACP;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class Elegant extends Model
{
    /**
     * Validation rules
     *
     * @var array
     */
    protected static $rules = [];

    /**
     * Custom messages
     *
     * @var array
     */
    protected static $messages = [];

    /**
     * Validation errors
     *
     * @var MessageBag
     */
    protected $errors = [];

    /**
     * Validator instance
     *
     * @var \Illuminate\Support\Facades\Validator
     */
    protected $validator;

    /**
     * BFAdminCP instance
     */
    protected $bfacp;

    public function __construct(array $attributes = [], Validator $validator = null)
    {
        parent::__construct($attributes);

        $this->validator = $validator ?: app('validator');
        $this->bfacp = app('bfadmincp');
    }

    /**
     * Listen for save event
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            return $model->validate();
        });
    }

    /**
     * Validates current attributes against rules
     */
    public function validate()
    {
        $v = $this->validator->make($this->attributes, static::$rules, static::$messages);

        if ($v->fails()) {
            $this->setErrors($v->messages());
            return false;
        }

        return true;
    }

    /**
     * Retrieve error message bag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set error message bag
     *
     * @var MessageBag
     */
    protected function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Inverse of wasSaved
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Retrieve the validation rules
     */
    public function getRules()
    {
        return static::$rules;
    }
}
