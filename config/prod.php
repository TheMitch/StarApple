<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates'); 

// disable twig cache
// $app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'	=> array(
        'driver'	=> 'pdo_mysql',
        'dbname'	=> 'starapple',
        'host' 		=> '127.0.0.1', 
        'password' 	=> ''

    ),
));