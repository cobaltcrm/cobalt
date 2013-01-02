var validateDbTo = null;

$(document).ready(function(){

	/* Initiate tooltips */
	$("[rel=tooltip]").tooltip();

	/** Validate database credentials **/
	$("#dbHost,#dbUser,#dbPass,#dbName").bind("change",function(){
		if ( validateDbTo != null )
			clearTimeout(validateDbTo);
		validateDbTo = setTimeout(function(){validateDb();},500);
	});

	/** Hide past shown tooltips **/
	$("[data-toggle='tab']").on('click',function(){
		$("[rel=tooltip]").tooltip('hide');
	})
	
});

function validateSite(){

	var valid = true;

	if ( $("#siteName").val() == "" ){
		$('#myTab a[href="#site"]').tab('show');
		if ( !$("#siteName").is(":visible") ){
			setTimeout(function(){
				$("#siteName").tooltip('show');
			},500);
		}else{
			$("#siteName").tooltip('show');
		}
		valid = false;
	}

	return valid;
}

function validateAdmin(){

	console.log('here');

	var valid = true;

	var obj = {};

	obj['firstname'] = $("#adminFirstname").val();
	obj['lastname'] = $("#adminLastname").val();
	obj['username'] = $("#adminUsername").val();
	obj['password'] = $("#adminPassword").val();
	obj['email'] = $("#adminEmail").val();

	var valid = true;

	$.each(obj,function(key,value){
		if ( value == "" || value == null ){
			valid = false;
			$("#admin"+ucwords(key)).tooltip('show');
			$('#myTab a[href="#admin"]').tab('show')
		}else{
			$("#admin"+ucwords(key)).tooltip('hide');
		}
	});

	return valid;

}

function validateDb(){

	if ( !$("#database").is(":visible") ) {

		$('#myTab a[href="#database"]').tab('show');
		setTimeout(function(){validateDb();},500);

		var valid = false;

	} else {

		var obj = {};

		obj['host'] = $("#dbHost").val();
		obj['user'] = $("#dbUser").val();
		obj['pass'] = $("#dbPass").val();
		obj['name'] = $("#dbName").val();

		var valid = true;

		$.each(obj,function(key,value){
			if ( value == "" || value == null ){
				valid = false;
				$("#db"+ucwords(key)).tooltip('show');
				$('#myTab a[href="#database"]').tab('show')
			}else{
				$("#db"+ucwords(key)).tooltip('hide');
			}
		});

		if ( valid ){
			$("#db-ajax").css('visibility','visible');
			$.ajax({
				async:false,
				type:"POST",
				url:"index.php?c=install&m=validateDb",
				data:obj,
				dataType:'JSON',
				success:function(data){
					$("#db-ajax").css('visibility','hidden');
					if ( data.valid ){
						$("#database-validation-message").empty().append("<span class='alert alert-success'>Everything checks out!</span>");
					}else{
						$("#database-validation-message").empty().append("<span class='alert alert-danger'>There appears to be a problem! <b>"+data.error+"</b></span>");
					}
				}
			})
		}

	}

	return valid;

}

function showTab(tab){
	$("[rel=tooltip]").tooltip('hide');
	$('#myTab a[href="#'+tab+'"]').tab('show');
}

function ucwords (str) {
    // Uppercase the first character of every word in a string  
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function install(){
	if ( validateSite() && validateDb() && validateAdmin() )
		$('#install-form').submit();
}