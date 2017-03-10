<?php

namespace App\Controller;

use Silex\Application;
use App\Model\Post;
use App\Model\Author;

class PostController 
{

	public function indexAction(Application $app)
	{
    $posts = Post::findAll($app);
		return $app['twig']->render('post\index.html.twig', array( 'posts' => $posts,));
	}

	public function readAction(Application $app, $id)
	{
    $post = Post::find($app, $id);
		return $app['twig']->render('post\read.html.twig', array( 'post' => $post,	));
	}

	public function createAJAXAction(Application $app){	
    $request = $app['request_stack']->getCurrentRequest()->request;
    $dateTime = date("Y-m-d H:i:s");    

    $post = new Post();
    $post->title = $request->get("title");
    $post->text = $request->get("message");
    $post->created_at = $dateTime;

    $author = new Author();
    $author->name = $request->get("name");
    $author->password = $request->get("password");
    $author->created_at = $dateTime;

    if(!$author->authenticate($app)){
      if($author->save($app)){
        $response["notifications"][] =  array(
             "type" => "info",
             "message" => "Nieuw account aangemaakt!",
        );      
      } else { 
        $response["notifications"][] =  array(
          "type" => "danger",
          "message" => "Gegevens incorrect",
          );
        return $app->json($response, 200);
      }
    }
    $post->author = $author;
    $post->save($app);

    $response["id"] = $post->id;
    $response["author_id"] = $post->author->id;
    $response["name"] = $post->author->name;
    $response["title"] = $post->title;
    $response["text"] = $post->text;
    $response["created_at"] = $post->created_at;
    $response["notifications"][] =  array(
      "type" => "success",
      "message" => "Nieuwe post geplaatst!",
    );

      return $app->json($response, 201);
	}
}