<?php
namespace App\Http\Controllers;

use App\Providers\Application;

class Controller extends Application{

	public function __construct()
	{
		/**
		 * in an elaborate framework, 
		 * framework requisite injections can be done here
		 * 
		*/
		parent::__construct(__DIR__.'/../../../');
	}
}