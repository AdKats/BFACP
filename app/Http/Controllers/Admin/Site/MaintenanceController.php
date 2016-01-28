<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

/**
 * Class MaintenanceController.
 */
class MaintenanceController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $page_title = trans('navigation.main.items.maintenance.title');
        return view('admin.site.maintenance.index', compact('page_title'));
    }

    /**
     * @return mixed
     */
    public function update()
    {
        if ($this->request->has('maintenance_mode')) {
            switch ($this->request->get('maintenance_mode')) {
                case 1:
                    if (! env('APP_DOWN')) {
                        $this->messages[] = 'Maintenance mode enabled';
                        Artisan::call('down');
                    }
                    break;
                case 0:
                    if (env('APP_DOWN')) {
                        $this->messages[] = 'Maintenance mode disabled';
                        Artisan::call('up');
                    }
                    break;
            }
        }

        if ($this->request->has('cache_flush') && $this->request->get('cache_flush') == 1) {
            $this->messages[] = 'Cache Cleared';
            Artisan::call('cache:clear');
        }

        return redirect()->route('admin.site.maintenance.index')->withMessages($this->messages);
    }
}
