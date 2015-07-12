<?php namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\BaseController;
use BFACP\Option as Option;
use Illuminate\Support\Facades\Cache as Cache;
use Illuminate\Support\Facades\Input as Input;
use Illuminate\Support\Facades\Lang as Lang;
use Illuminate\Support\Facades\Redirect as Redirect;
use Illuminate\Support\Facades\View as View;

class SettingsController extends BaseController
{
    public function index()
    {
        $settings = Option::where('option_key', '!=', 'site.languages')->get();

        return View::make('admin.site.settings.index', compact('settings'))->with('page_title',
            Lang::get('navigation.admin.site.items.settings.title'));
    }

    public function update()
    {
        $settings = Option::lists('option_value', 'option_key');

        foreach (Input::all() as $key => $value) {
            if (starts_with($key, '_') === false) {
                $key = str_replace('-', '.', $key);
                $value = trim($value);

                if (!is_null(MainHelper::stringToBool($value))) {
                    $value = MainHelper::stringToBool($value);
                } else {
                    if (empty($value)) {
                        $value = null;
                    }
                }

                if ($value != $settings[ $key ]) {
                    Option::where('option_key', $key)->update(['option_value' => $value]);
                }
            }
        }

        Cache::forget('site.options');

        return Redirect::route('admin.site.settings.index')->with('messages', ['Settings Saved!']);
    }
}
