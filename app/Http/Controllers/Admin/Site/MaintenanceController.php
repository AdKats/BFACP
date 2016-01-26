<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

/**
 * Class MaintenanceController.
 */
class MaintenanceController extends Controller
{
    /**
     * @return $this
     */
    public function index()
    {
        return view('admin.site.maintenance.index')->with('page_title',
            trans('navigation.main.items.maintenance.title'));
    }

    /**
     * @return mixed
     */
    public function update()
    {
        if ($this->request->has('maintenance_mode')) {
            switch ($this->request->get('maintenance_mode')) {
                case 1:
                    if (! File::exists(storage_path().'/meta/down')) {
                        $this->messages[] = 'Maintenance mode enabled';
                        Artisan::call('down');
                    }
                    break;
                case 0:
                    if (File::exists(storage_path().'/meta/down')) {
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
