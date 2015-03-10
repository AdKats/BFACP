<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Exceptions\MetabansException;
use BFACP\Libraries\Metabans;
use Input;
use Lang;
use MainHelper;

class MetabansController extends BaseController
{
    protected $metabans;

    public function __construct(Metabans $metabans)
    {
        $this->metabans = $metabans;
    }

    public function getIndex()
    {
        throw new MetabansException(405, 'Invalid Resource');
    }

    public function getFeedAssessments()
    {
        $feed = $this->metabans->feed();
        $assessments = $this->metabans->assessments();

        $feed_assessments = [
            'feed' => $feed,
            'assessments' => $assessments
        ];

        return MainHelper::response(
            $feed_assessments + [
                'locales' => Lang::get('common.metabans')
            ],
            NULL, NULL, NULL, FALSE, TRUE
        );
    }

    public function getFeed()
    {
        $feed = $this->metabans->feed();

        return MainHelper::response(
            $feed,
            NULL, NULL, NULL, FALSE, TRUE
        );
    }

    public function getAssessments()
    {
        $assessments = $this->metabans->assessments();

        return MainHelper::response(
            $assessments,
            NULL, NULL, NULL, FALSE, TRUE
        );
    }
}
