<?php

class page_not_exists extends base_controller
{
	public function __construct($_vars)
	{
		parent::__construct();
		var_dump($_vars);
		var_dump($this->get_app_path());
		var_dump($this->get_base_url());
	}
}

?>