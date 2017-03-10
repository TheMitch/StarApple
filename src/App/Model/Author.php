<?php
namespace App\Model;

class Author {
	static public function findAll($app) {
    	$query = $app['db']->createQueryBuilder();
    	$query
      		->select('a.id', 'a.name', 'a.password', 'a.created_at', 'COUNT(p.author_id) as posts_count')
      		->from('authors', 'a')
      		->innerJoin('a', 'posts', 'p', 'p.author_id = a.id')
      		->groupBy('p.author_id');
      	$authorsData = $query->execute(); 

   		$authors = Array();
   		foreach($authorsData as $authorData) {
   			$author = new Author();
   			$author->id = $authorData['id'];
   			$author->name = $authorData['name'];
   			$author->password = $authorData['password'];
   			$author->postCount = $authorData['posts_count'];
   			$author->created_at = $authorData['created_at'];
   			$authors[] = $author;
   		}
   		return $authors;
    }
	static public function find($app, $id) {
    	$query = $app['db']->createQueryBuilder();
    	$query
      		->select('a.id', 'a.name', 'a.password', 'a.created_at', 'COUNT(p.author_id) as posts_count')
      		->from('authors', 'a')
        	->where('a.id = :id')
        	->setParameter(':id', $id)
      		->innerJoin('a', 'posts', 'p', 'p.author_id = a.id')
      		->groupBy('p.author_id');
      	
      	$authorsData = $query->execute();     
      	$authorData = $authorsData->fetch();

   		$author = new Author();
   		$author->id = $authorData['id'];
   		$author->name = $authorData['name'];
   		$author->password = $authorData['password'];
   		$author->postCount = $authorData['posts_count'];
   		$author->created_at = $authorData['created_at'];

   		return $author; 			
   	}

	static public function findByName($app, $name) {
    	$query = $app['db']->createQueryBuilder();
    	$query
      		->select('a.id', 'a.name', 'a.password', 'a.created_at', 'COUNT(p.author_id) as posts_count')
      		->from('authors', 'a')
      		->where('name = :name')
        	->setParameter(':name', $name)
      		->innerJoin('a', 'posts', 'p', 'p.author_id = a.id')
      		->groupBy('p.author_id');
      	
      	$authorsData = $query->execute();  

   		if($authorsData){   
      		$authorData = $authorsData->fetch();

   			$author = new Author();
   			$author->id = $authorData['id'];
   			$author->name = $authorData['name'];
   			$author->password = $authorData['password'];
   			$author->postCount = $authorData['posts_count'];
   			$author->created_at = $authorData['created_at'];
   		}

   		return $author; 			
   	}    	 
	
	public function getPosts($app) {
		return Post::findAllByAuthor($app, $this->id);
	}  

	public function authenticate($app) {
		$dbAuthor = Author::findByName($app, $this->name);
		if($dbAuthor != null) {
			if(password_verify ( $this->password , $dbAuthor->password)) {
				$this->id = $dbAuthor->id;
   				$this->password = $dbAuthor->password;			
				return true;
			}
			return false;
		}
	}

	public function save($app) {
		if(!isset($this->id) && Author::findByName($app, $this->name)->id == null){
        	$query = $app['db']->createQueryBuilder();
    		$this->password = password_hash($this->password, PASSWORD_DEFAULT);

        	$query->values(
          		array(
	            	"name" => "'".$this->name."'",
            		"password" => "'".$this->password."'",
            		"created_at" => "'".$this->created_at."'",
          			)
        	   	)
        		->insert('authors');
        	$query->execute();
        	$this->id = $app['db']->lastInsertId();
        	return true;
        }
        return false;
	}
}