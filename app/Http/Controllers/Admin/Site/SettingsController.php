<?php

namespace BFACP\Http\Controllers\Admin\Site;

use BFACP\Facades\Main as MainHelper;
use BFACP\Http\Controllers\Controller;
use BFACP\Option as Option;

/**
 * Class SettingsController.
 */
class SettingsController extends Controller
{
    /**
     * @return $this
     */
    public function index()
    {
        $settings = Option::where('option_key', '!=', 'site.languages')->get();

        return view('admin.site.settings.index', compact('settings'))->with('page_title',
            trans('navigation.admin.site.items.settings.title'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $settings = Option::lists('option_value', 'option_key');

        foreach ($this->request->all() as $key => $value) {
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
                    $this->log->info(sprintf('%s updated site setting %s.', $this->user->username, $key));
                }
            }
        }

        $this->cache->forget('site.options');

        return redirect()->route('admin.site.settings.index')->with('messages', ['Settings Saved!']);
    }
}
