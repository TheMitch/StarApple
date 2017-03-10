$( document ).ready(function() {
	$("#new-post").ajaxForm({
		url: '/post/create', 
		type: 'post',
    	success: function(responseText){

    		var response = $.parseJSON(responseText);
    		console.log(response['notifications'] );

    		if(response.hasOwnProperty('notifications') && response['notifications'].hasOwnProperty('error'))
    		{
				$('#notification').html('<div class="alert alert-danger" role="alert">'+response['notifications']['error']+'</div>');  		
        	} else {
        		$('<tr><td><a href="/post/'+response['id']+'">'+response['title']+'</a></td><td><a href="/author/'+response['author_id']+'">'+response['name']+'</td><td>'+response['created_at']+'</td></tr>').prependTo(".table > tbody");
        	}
    	}
    })
});