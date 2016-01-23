<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

/**
 * Class MaintenanceController.
 */
class MaintenanceController extends Controller
{
    public function index()
    {
        return view('admin.site.maintenance.index')->with('page_title',
            trans('navigation.main.items.maintenance.title'));
    }

    public function update()
    {
        if (Input::has('maintenance_mode')) {
            switch (Input::get('maintenance_mode')) {
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

        if (Input::has('cache_flush') && Input::get('cache_flush') == 1) {
            $this->messages[] = 'Cache Cleared';
            Artisan::call('cache:clear');
        }

        return redirect()->route('admin.site.maintenance.index')->withMessages($this->messages);
    }
}
