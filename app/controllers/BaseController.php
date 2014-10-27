<?php

class BaseController extends Controller {

	public $user_tz = 'UTC';

	public function __construct()
	{
		if(Auth::check()) $this->user_tz = Auth::user()->preferences->timezone;

		View::share('user_timezone', $this->user_tz);

		if(Helper::_empty(Config::get('webadmin.CLANNAME')) == FALSE)
		{
			View::share('clan_name', Config::get('webadmin.CLANNAME') . ' |');
		}
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	/**
	 * Get's recent users online
	 *
	 * @return void
	 */
	protected function whosOnline()
	{
		$query = DB::select(File::get(storage_path() . '/sql/whos_online.sql'));

		Config::set('webadmin.WHOSONLINE', $query);
	}

}
