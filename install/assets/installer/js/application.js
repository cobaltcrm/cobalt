var validateDbTo = null;
var site = false;
var db = false;
var admin = false;

$(document).ready(function(){

	/* Initiate tooltips */
	$("[data-toggle=tooltip]").tooltip();

	/** Validate database credentials **/
	$("#dbHost,#dbUser,#dbPass,#dbName").bind("change",function(){
		if ( validateDbTo != null )
			clearTimeout(validateDbTo);
		validateDbTo = setTimeout(function(){validateDb();},500);
	});

	/** Hide past shown tooltips **/
	$("[data-toggle='tab']").on('click',function(){
		$("[rel=tooltip]").tooltip('hide');
	});

	/** Install button action **/
	$('#install-cobalt').click(function(e) {
		e.preventDefault();
		install();
	});

	$('a[data-toggle="tab"]').click(function(e) {
		e.preventDefault();
		var tab = $(this).attr('data-showtab');
		showTab(tab);
	});

	/** Comment for production! **/
	// prefill();
    $('#myTab a:first').tab('show')
});

/*
* Prefills fields for faster testing
*/
function prefill() {
	var $ = jQuery;
	$('input#siteName').val('Cobalt');
	$('input#dbHost').val('localhost');
	$('input#dbUser').val('root');
	$('input#dbPass').val('root');
	$('input#dbName').val('cobalt2');
	$('input#dbPrefix').val('cob_');
	$('input#adminFirstname').val('John');
	$('input#adminLastname').val('Doe');
	$('input#adminEmail').val('admin@escope.cz');
	$('input#adminUsername').val('admin');
	$('input#adminPassword').val('admin');
}

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

	site = valid;

    if ( !valid && !$('#site').hasClass('active') ) {
        showTab('site');
    }

	return valid;
}

function validateAdmin(){

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

	admin = valid;

    if ( !valid && !$('#admin').hasClass('active') ) {
        showTab('admin');
    }

	return valid;

}

function validateDb(){

	var valid = false;

	if ( !$("#database").is(":visible") ) {
		setTimeout(function(){validateDb();},500);

		valid = false;

	} else {

		var obj = {};

		obj['host'] = $("#dbHost").val();
		obj['user'] = $("#dbUser").val();
		obj['pass'] = $("#dbPass").val();
		obj['name'] = $("#dbName").val();

		valid = true;

		$.each(obj,function(key,value){
			if ( key != "pass" && ( value == "" || value == null ) ){
				valid = false;
				$("#db"+ucwords(key)).tooltip('show');
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
						$("#database-validation-message").empty().append("<div class='alert alert-success'>Everything checks out!</div>");
					}else{
						$("#database-validation-message").empty().append("<div class='alert alert-danger'>There appears to be a problem! <b>"+data.error+"</b></div>");
					}
				}
			})
		}

	}

	db = valid;

    if ( !valid && !$('#database').hasClass('active') ) {
        showTab('database');
    }

	return valid;

}

function showTab(tab){
	$("[rel=tooltip]").tooltip('hide');
	$('a[href="#'+tab+'"]').tab('show');
}

function ucwords (str) {
    // Uppercase the first character of every word in a string  
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function install() {
    if ( !site ){
        validateSite();
    }else if ( !db ){
        validateDb();
    } else if ( !admin ) {
        validateAdmin();
    } else {
        $("#install-form").submit();
    }

    /*

	if ( !site ){
		validateSite();
	}else if ( !db ){
		validateDb();
	}else{
		validateAdmin();
	}

	if ( admin ){
        alert('submit');
		//$("#install-form").submit();
	}
	*/
}