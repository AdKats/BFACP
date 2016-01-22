<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Exceptions\MetabansException;
use BFACP\Facades\Main as MainHelper;
use BFACP\Libraries\Metabans;

/**
 * Class MetabansController.
 */
class MetabansController extends Controller
{
    protected $metabans;

    /**
     * @param Metabans $metabans
     */
    public function __construct(Metabans $metabans)
    {
        parent::__construct();
        $this->metabans = $metabans;
    }

    public function getIndex()
    {
        throw new MetabansException(405, 'Invalid Resource');
    }

    /**
     * @return mixed
     */
    public function getFeedAssessments()
    {
        $feed = $this->metabans->feed();
        $assessments = $this->metabans->assessments();

        $feed_assessments = [
            'feed'        => $feed,
            'assessments' => $assessments,
        ];

        return MainHelper::response($feed_assessments + [
                'locales' => trans('common.metabans'),
            ], null, null, null, false, true);
    }

    /**
     * @return mixed
     */
    public function getFeed()
    {
        $feed = $this->metabans->feed();

        return MainHelper::response($feed, null, null, null, false, true);
    }

    /**
     * @return mixed
     */
    public function getAssessments()
    {
        $assessments = $this->metabans->assessments();

        return MainHelper::response($assessments, null, null, null, false, true);
    }
}
