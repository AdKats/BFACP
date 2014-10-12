<?php

class BaseController extends Controller {

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

		$this->whosOnline();
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
