<?php namespace BFACP\Http\Controllers\Api;

use BFACP\Adkats\Setting;
use BFACP\Facades\Main as MainHelper;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException as ResourceException;
use Dingo\Api\Exception\UpdateResourceFailedException as UpdateResourceFailedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
            'id'                   => 'required|numeric|exists:adkats_records_main,record_id',
            'action'               => 'required|numeric|in:' . implode(',', $r::$allowedCommands),
            'reason'               => 'required|string|between:3,500',
            'extras.tban.duration' => 'required_if:action,7|numeric|between:1,525960',
        ], [
            'extras.tban.duration.required_if' => 'The duration is required for temp bans.',
            'extras.tban.duration.between'     => 'The duration must be between :min minute and :max minutes.',
        ]);

        if ($v->fails()) {
            throw new ResourceException(null, $v->errors());
        }

        try {
            $record = $r->getReportById(Input::get('id'));

            if (!in_array($record->command_action, [18, 20])) {
                throw new UpdateResourceFailedException('Unable to complete action. Report has already been acted on.');
            }

            // If the action is {Accept, Deny, Ignore} Round Report then we just need to update the existing record.
            if (in_array(Input::get('action'), [40, 41, 61])) {
                $record->command_action = Input::get('action');
                $record->save();
            } else {
                $newRecord = $record->replicate();
                $newRecord->command_type = Input::get('action');
                $newRecord->command_action = Input::get('action');

                if (Input::get('action') == 7) {
                    $maxDuration = Setting::where('setting_name',
                        'Maximum Temp-Ban Duration Minutes')->where('server_id',
                        $newRecord->server_id)->pluck('setting_value');

                    $duration = Input::get('extras.tban.duration', $maxDuration);

                    $commandNumeric = (int)$duration > (int)$maxDuration ? $maxDuration : $duration;
                } else {
                    $commandNumeric = 0;
                }

                $newRecord->command_numeric = $commandNumeric;

                $newMessage = trim(Input::get('reason', $newRecord->record_message));
                $oldMessage = trim($newRecord->record_message);

                if ($newMessage != $oldMessage && !empty($newMessage)) {
                    $newRecord->record_message = $newMessage;
                }

                $source = MainHelper::getAdminPlayer($this->user, $newRecord->server->game->GameID);

                if (!is_null($source)) {
                    $newRecord->source_id = $source->PlayerID;
                    $newRecord->source_name = $source->SoldierName;
                } else {
                    $newRecord->source_id = null;
                    $newRecord->source_name = $this->user->username;
                }

                $newRecord->record_time = Carbon::now();
                $newRecord->adkats_read = 'N';
                $newRecord->save();

                $record->command_action = 40;
                $record->save();
            }

            return MainHelper::response([
                'old' => $record,
                'new' => isset($newRecord) ? $newRecord : null,
            ], 'Report updated', null, null, false, true);
        } catch (ModelNotFoundException $e) {
            return MainHelper::response(null, 'Report was not found. Aborting!', 'error', null, false, true);
        }
    }
}
