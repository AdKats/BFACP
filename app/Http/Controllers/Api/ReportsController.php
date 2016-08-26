<?php

namespace BFACP\Http\Controllers\Api;

use BFACP\Adkats\Setting;
use BFACP\Facades\Main as MainHelper;
use BFACP\Repositories\ReportRepository;
use Carbon\Carbon;
use Dingo\Api\Exception\ResourceException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as Validator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException as AccessDeniedHttpException;

/**
 * Class ReportsController.
 */
class ReportsController extends Controller
{
    /**
     * @var ReportRepository
     */
    private $repository;

    /**
     * Returns the latest 30 reports.
     */
    public function getIndex()
    {
        if (! Auth::check() || ! Auth::user()->ability(null, 'admin.adkats.reports.view')) {
            throw new AccessDeniedHttpException('Not Authorized for viewing reports.');
        }

        $this->repository = app(ReportRepository::class);

        return MainHelper::response($this->repository->getReports(false), null, null, null, false, true);
    }

    /**
     * Returns the allowed commands to be issued.
     *
     * @return array
     */
    public function getActions()
    {
        $this->repository = app(ReportRepository::class);

        return $this->repository->getActions();
    }

    /**
     * Updates the selected report.
     */
    public function putIndex()
    {
        if (! Auth::check() || ! Auth::user()->ability(null, 'admin.adkats.reports.edit')) {
            throw new AccessDeniedHttpException('Not Authorized for editing reports.');
        }

        $this->repository = app(ReportRepository::class);

        $v = Validator::make($this->request->all(), [
            'id'                   => 'required|numeric|exists:adkats_records_main,record_id',
            'action'               => 'required|numeric|in:'.implode(',', $this->repository->getAllowedCommands()),
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
            $record = $this->repository->getReportById($this->request->get('id'));

            if (! in_array($record->command_action, [18, 20])) {
                throw new UpdateResourceFailedException('Unable to complete action. Report has already been acted on.');
            }

            // If the action is {Accept, Deny, Ignore} Round Report then we just need to update the existing record.
            if (in_array($this->request->get('action'), [40, 42, 62])) {
                $record->command_action = $this->request->get('action');
                $record->save();
            } else {
                $newRecord = $record->replicate();
                $newRecord->command_type = $this->request->get('action');
                $newRecord->command_action = $this->request->get('action');

                if ($this->request->get('action') == 7) {
                    $query = Setting::where('setting_name', 'Maximum Temp-Ban Duration Minutes')->where('server_id',
                        $newRecord->server_id)->first();

                    $maxDuration = $query->setting_value;

                    $duration = $this->request->get('extras.tban.duration', $maxDuration);

                    $commandNumeric = (int) $duration > (int) $maxDuration ? $maxDuration : $duration;
                } else {
                    $commandNumeric = 0;
                }

                $newRecord->command_numeric = $commandNumeric;

                $newMessage = trim($this->request->get('reason', $newRecord->record_message));
                $oldMessage = trim($newRecord->record_message);

                if ($newMessage != $oldMessage && ! empty($newMessage)) {
                    $newRecord->record_message = $newMessage;
                }

                $source = MainHelper::getAdminPlayer(Auth::user(), $newRecord->server->game->GameID);

                if (! is_null($source)) {
                    $newRecord->source_id = $source->PlayerID;
                    $newRecord->source_name = $source->SoldierName;
                } else {
                    $newRecord->source_id = null;
                    $newRecord->source_name = Auth::user()->username;
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
