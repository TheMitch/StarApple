<?php

namespace App\Provider\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AuthorControllerProvider implements ControllerProviderInterface 
{
	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];

		$controllers->match('/', 'App\Controller\AuthorController::indexAction');		
		$controllers->match('/{id}', 'App\Controller\AuthorController::readAction');
		
        return $controllers;
	}
}