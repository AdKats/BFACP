<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use BFACP\Option as Option;
use Illuminate\Support\Facades\Input as Input;

/**
 * Class SettingsController.
 */
class SettingsController extends Controller
{
    public function index()
    {
        $settings = Option::where('option_key', '!=', 'site.languages')->get();

        return view('admin.site.settings.index', compact('settings'))->with('page_title',
            trans('navigation.admin.site.items.settings.title'));
    }

    public function update()
    {
        $settings = Option::lists('option_value', 'option_key');

        foreach (Input::all() as $key => $value) {
            if (starts_with($key, '_') === false) {
                $key = str_replace('-', '.', $key);
                $value = trim($value);

                if (! is_null(MainHelper::stringToBool($value))) {
                    $value = MainHelper::stringToBool($value);
                } else {
                    if (empty($value)) {
                        $value = null;
                    }
                }

                if ($value != $settings[$key]) {
                    Option::where('option_key', $key)->update(['option_value' => $value]);
                }
            }
        }

        $this->cache->forget('site.options');

        return redirect()->route('admin.site.settings.index')->with('messages', ['Settings Saved!']);
    }
}
