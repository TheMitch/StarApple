<?php

use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\Schema\Table;

//Request::setTrustedProxies(array('127.0.0.1'));
$app->get('/', 'App\Controller\PostController::indexAction');
$app->mount('/author', new App\Provider\Controller\AuthorControllerProvider);
$app->mount('/post', new App\Provider\Controller\PostControllerProvider);

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});

// Ugly way of creating tables if needed
$schema = $app['db']->getSchemaManager();
if (!$schema->tablesExist('authors')) {
    $posts = new Table('authors');
    $posts->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
    $posts->setPrimaryKey(array('id'));
    $posts->addColumn('name', 'string', array('length' => 255));
    $posts->addColumn('password', 'string', array('length' => 255));
    $posts->addColumn('created_at', 'datetime');

    $schema->createTable($posts);}
if (!$schema->tablesExist('posts')) {
    $posts = new Table('posts');
    $posts->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
    $posts->setPrimaryKey(array('id'));
    $posts->addColumn('author_id', 'integer', array('unsigned' => true));
    $posts->addColumn('title', 'string', array('length' => 255));
    $posts->addColumn('text', 'text');
    $posts->addColumn('created_at', 'datetime');
    $schema->createTable($posts);
}
