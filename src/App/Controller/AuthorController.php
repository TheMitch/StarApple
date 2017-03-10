<?php

namespace App\Controller;

use Silex\Application;
use App\Model\Author;

class AuthorController 
{
  public function indexAction(Application $app)
  {
    $authors = Author::findAll($app);
    return $app['twig']->render('author\index.html.twig', array( 'authors' => $authors,));
  }
  
  public function readAction(Application $app, $id)
  {
    $author = Author::find($app, $id);
    $posts = $author->getPosts($app);
    return $app['twig']->render('author\read.html.twig', array( 'author' => $author,  'posts' => $posts));
  }

}