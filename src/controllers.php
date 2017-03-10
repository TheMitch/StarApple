<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

$schema = $app['db']->getSchemaManager();
if (!$schema->tablesExist('authors')) {
    $posts = new Table('authors');
    $posts->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
    $posts->setPrimaryKey(array('id'));
    $posts->addColumn('name', 'string', array('length' => 255));
    $posts->addColumn('password', 'string', array('length' => 255));
    $posts->addColumn('created_at', 'datetime');

    $schema->createTable($posts);

    $app['db']->insert('authors', array(
      'name' => 'Pietje Puk',
      'password' => '',
    ));
    $app['db']->insert('authors', array(
      'name' => 'Olivier B Bommel',
      'password' => '',
    ));
}
if (!$schema->tablesExist('posts')) {
    $posts = new Table('posts');
    $posts->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
    $posts->setPrimaryKey(array('id'));
    $posts->addColumn('author_id', 'integer', array('unsigned' => true));
    $posts->addColumn('name', 'string', array('length' => 255));
    $posts->addColumn('text', 'text');
    $posts->addColumn('created_at', 'datetime');

    $schema->createTable($posts);

    $app['db']->insert('posts', array(
      'author_id' => '1',
      'name' => 'test',
      'text' => 'heel verhaal',
    ));
    $app['db']->insert('posts', array(
      'author_id' => '1',
      'name' => 'test2',
      'text' => 'heel verhaal2',
    ));
    $app['db']->insert('posts', array(
      'author_id' => '2',
      'name' => 'test2',
      'text' => 'heel verhaal2',
    ));
}
