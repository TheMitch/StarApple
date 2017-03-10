<?php
namespace App\Model;

class Post {

	static public function findAll($app) {
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('p.id', 'p.author_id', 'p.title', 'p.text', 'a.name', 'p.created_at')
    	->from('posts', 'p')
    	->innerJoin('p', 'authors', 'a', 'p.author_id = a.id')
      	->orderBy('p.created_at', 'DESC');
   		
   		$postsData = $query->execute();
   		$posts = Array();
   		foreach($postsData as $postData) {
   			$post = new Post();
   			$post->id = $postData['id'];
   			$post->author_id = $postData['author_id'];
   			$post->title = $postData['title'];
   			$post->name = $postData['name'];
   			$post->text = $postData['text'];
   			$post->created_at = $postData['created_at'];
   			$posts[] = $post;

   		}
   		return $posts;
	}
	static public function find($app, $id) {
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('p.id', 'p.author_id', 'p.title', 'p.text', 'a.name', 'p.created_at')
    	->from('posts', 'p')
      	->where('p.id = :id')
      	->setParameter(':id', $id)
    	->innerJoin('p', 'authors', 'a', 'p.author_id = a.id');

   		$postsData = $query->execute();    	
   		$postData = $postsData->fetch();

   		$post = new Post();
   		$post->id = $postData['id'];
   		$post->author_id = $postData['author_id'];
   		$post->title = $postData['title'];
   		$post->text = $postData['text'];
   		$post->created_at = $postData['created_at'];  
   		return $post; 			
   	}

	static public function findAllByAuthor($app, $id) {
		$query = $app['db']->createQueryBuilder();
		$query
    	->select('p.id', 'p.author_id', 'p.title', 'p.text', 'a.name', 'p.created_at')
    	->from('posts', 'p')
    	->where('p.author_id = :id')   
      	->setParameter(':id', $id)    	
    	->innerJoin('p', 'authors', 'a', 'p.author_id = a.id')
      	->orderBy('p.created_at', 'DESC');
   		
   		$postsData = $query->execute();
   		$posts = Array();
   		foreach($postsData as $postData) {
   			$post = new Post();
   			$post->id = $postData['id'];
   			$post->author_id = $postData['author_id'];
   			$post->title = $postData['title'];
   			$post->name = $postData['name'];
   			$post->text = $postData['text'];
   			$post->created_at = $postData['created_at'];
   			$posts[] = $post;

   		}
   		return $posts;		
   	}

	public function save($app) {
		if(!isset($this->id)){
        	$query = $app['db']->createQueryBuilder();
        	$query->values(
        	  	array(
	        	    "author_id" => "'".$this->author->id."'",
            		"title" => "'".$this->title."'",
            		"text" => "'".$this->text."'",
            		"created_at" => "'".$this->created_at."'",
          		)
        	)
        	->insert('posts');
        	$query->execute();
        	$this->id = $app['db']->lastInsertId();
        	return true;
        }
        return false;
	}   	
}
?>