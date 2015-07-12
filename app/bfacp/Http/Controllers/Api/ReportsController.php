<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Facades\Main as MainHelper;
use Dingo\Api\Exception\ResourceException as ResourceException;
use Dingo\Api\Exception\UpdateResourceFailedException as UpdateResourceFailedException;
use Illuminate\Support\Facades\App as App;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Validator as Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException as AccessDeniedHttpException;

class ReportsController extends BaseController
{
    public function getIndex()
    {
        if (!$this->isLoggedIn || !$this->user->ability(null, 'admin.adkats.reports.view')) {
            throw new AccessDeniedHttpException('Authorization Denied!');
        }

        $r = App::make('BFACP\Repositories\ReportRepository');

        return MainHelper::response($r->getReports(false), null, null, null, false, true);
    }

    public function getActions()
    {
        $r = App::make('BFACP\Repositories\ReportRepository');

        return $r->getActions();
    }

    public function putIndex()
    {
        if (!$this->isLoggedIn || !$this->user->ability(null, 'admin.adkats.reports.edit')) {
            throw new AccessDeniedHttpException('Authorization Denied!');
        }

        $r = App::make('BFACP\Repositories\ReportRepository');

        $v = Validator::make(Input::all(), [
            'id' => 'required|numeric|exists:adkats_records_main,record_id',
            'action' => 'required|numeric|in:' . implode(',', $r::$allowedCommands),
            'reason' => 'max:500'
        ]);

        if ($v->fails()) {
            throw new ResourceException(null, $v->errors());
        }

        $record = $r->getRecordById(Input::get('id'));

        if (!in_array($record->command_action, [18, 20])) {
            throw new UpdateResourceFailedException('Unable to complete action. Report has already been acted on.');
        }

        $newMessage = trim(Input::get('reason', $record->record_message));
        $oldMessage = trim($record->record_message);

        if ($newMessage != $oldMessage && !empty($newMessage)) {
            $record->record_message = $newMessage;
        }

        $record->command_action = Input::get('action');
        $record->adkats_read = 'N';
        $record->save();

        return MainHelper::response(null, 'Report updated', null, null, false, true);
    }
}
