<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class MaintenanceController extends BaseController
{
    public function index()
    {
        return View::make('admin.site.maintenance.index')->with('page_title',
            Lang::get('navigation.main.items.maintenance.title'));
    }

    public function update()
    {
        if (Input::has('maintenance_mode')) {
            switch (Input::get('maintenance_mode')) {
                case 1:
                    if (!File::exists(storage_path() . '/meta/down')) {
                        $this->messages[] = 'Maintenance mode enabled';
                        Artisan::call('down');
                    }
                    break;
                case 0:
                    if (File::exists(storage_path() . '/meta/down')) {
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

        return Redirect::route('admin.site.maintenance.index')->withMessages($this->messages);
    }
}
