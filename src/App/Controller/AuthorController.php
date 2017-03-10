<?php

namespace App\Controller;

use Silex\Application;
use Sumpfony\Component\HttpFoundation\Request;
use Sumpfony\Component\HttpFoundation\Response;

class AuthorController 
{
	public function indexAction(Application $app)
	{
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('a.id', 'a.name', 'a.created_at', 'a.created_at', 'COUNT(p.author_id) as count')
    	->from('authors', 'a')
    	->innerJoin('a', 'posts', 'p', 'p.author_id = a.id')
    	->groupBy('p.author_id');

   		$authors = $query->execute();	
		return $app['twig']->render('author\index.html.twig', array( 'authors' => $authors,));
	}
	public function readAction(Application $app, $id)
	{
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('a.id', 'a.name', 'a.created_at', 'a.created_at', 'COUNT(p.author_id) as count')
    	->from('authors', 'a')
      	->where('a.id = :id')
      	->setParameter(':id', $id)
    	->innerJoin('a', 'posts', 'p', 'p.author_id = a.id')
    	->groupBy('p.author_id');

   		$authors = $query->execute();    	
   		$author = $authors->fetch();
		return $app['twig']->render('author\read.html.twig', array( 'author' => $author,	));
	}
}