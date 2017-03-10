$( document ).ready(function() {
	$("#new-post").ajaxForm({
		url: '/post/create', 
		type: 'post',
        beforeSubmit: function(){
            $('#notification').empty();

            var valid = true;
            if($("#new-post input[name=title]").val().trim().length == 0){
                printNotification("danger", "Berichten moeten een titel hebben!")
                valid = false;
            }
            if($("#new-post textarea[name=message]").val().trim().length == 0){
                printNotification("danger", "Berichten mogen niet leeg zijn!")
                valid = false;
            }
            return valid;
        },        
    	success: function(responseText){
            var error = false;
    		if(responseText.hasOwnProperty('notifications')) {
                console.log(responseText['notifications']);
                for (var i = 0; i < responseText['notifications'].length; i++) {
                    var notification = responseText['notifications'][i];
                    printNotification(notification['type'], notification['message'])
                    if(notification['type'] == "danger"){
                        error = true;
                    }
                }
        	} 
            if(!error) {
        		$('<tr><td><a href="/post/'+responseText['id']+'">'+responseText['title']+'</a></td><td><a href="/author/'+responseText['author_id']+'">'+responseText['name']+'</td><td>'+responseText['created_at']+'</td></tr>').prependTo(".table > tbody");
        	}
    	}
    })
});
function printNotification(type, message){ // success, info, warning, danger
    $('#notification').append('<div class="alert alert-'+type+'" role="alert">'+message+'</div>');
}