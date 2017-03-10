<?php

namespace App\Controller;

use Silex\Application;
use Doctrine\DBAL\DriverManager;
use Sumpfony\Component\HttpFoundation\Request;
use Sumpfony\Component\HttpFoundation\Response;

class PostController 
{

	public function indexAction(Application $app)
	{
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('p.id', 'p.author_id', 'p.title', 'p.text', 'a.name', 'p.created_at')
    	->from('posts', 'p')
    	->innerJoin('p', 'authors', 'a', 'p.author_id = a.id')
      ->orderBy('p.created_at', 'DESC');

   	$posts = $query->execute();
		return $app['twig']->render('post\index.html.twig', array( 'posts' => $posts,));
	}

	public function readAction(Application $app, $id)
	{
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('p.id', 'p.author_id', 'p.title', 'p.text', 'a.name', 'p.created_at')
    	->from('posts', 'p')
      	->where('p.id = :id')
      	->setParameter(':id', $id)
    	->innerJoin('p', 'authors', 'a', 'p.author_id = a.id');

   		$posts = $query->execute();    	
   		$post = $posts->fetch();
		return $app['twig']->render('post\read.html.twig', array( 'post' => $post,	));
	}

	public function createAction(Application $app){	
    // Lacking an orm system so we'll store the data here	
		$request = $app['request_stack']->getCurrentRequest()->request;
    $name = $request->get("name");
    $password = password_hash($request->get("password"), PASSWORD_DEFAULT);
    $title = $request->get("title");
    $message = $request->get("message");
    $created_at = date("Y-m-d H:i:s");

    // Checking if there is an author with the specified name
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('a.id', 'a.password')
    	->from('authors', 'a')
      	->where('name = :name')
      	->setParameters(array('name'=> $name));
   		$results = $query->execute();   
   		$result = $results->fetch();
      $author_id = $result['id'];

    // Does the password match up with the specified author
   		if($result){
        if(!password_verify ( $request->get("password") , $result['password'] )){
          $response = array(
            "notifications" => array(
              "error" => "Gegevens incorrect",
              )
          );
          die(json_encode($response));  // need to update this; echo and return gives a 500 error
        }
      } 
      // Create author if the name is not known in the db
      if(!$author_id){
        $query = $app['db']->createQueryBuilder();
        $query->values(
          array(
            "name" => "'".$name."'",
            "password" => "'".$password."'",
            "created_at" => "'".$created_at."'",
          )
        )
        ->insert('authors');
        $query->execute();
        $author_id = $app['db']->lastInsertId();
      }

      // Create post
        $query = $app['db']->createQueryBuilder();
        $query->values(
          array(
            "author_id" => "'".$author_id."'",
            "title" => "'".$title."'",
            "text" => "'".$message."'",
            "created_at" => "'".$created_at."'",
          )
        )
        ->insert('posts');
        $query->execute();
        $response = array(
          "id" => $app['db']->lastInsertId(),
          "author_id" => $result['id'],
          "name" => $name,
          "title" => $title,
          "text" => $message,
          "created_at" => $created_at,
          );
      die(json_encode($response));  // need to update this; echo and return gives a 500 error
	}
}