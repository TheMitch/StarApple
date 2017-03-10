<?php

namespace App\Provider\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class PostControllerProvider implements ControllerProviderInterface 
{
	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];

		$controllers->post('/create', 'App\Controller\PostController::createAction');	
		$controllers->match('/', 'App\Controller\PostController::indexAction');		
		$controllers->match('/{id}', 'App\Controller\PostController::readAction');	
		
        return $controllers;
	}
}