var validateDbTo = null;
var site = false;
var db = false;
var admin = false;

$(document).ready(function(){

	/* Initiate tooltips */
	$("[data-toggle=tooltip]").tooltip();

    $('#rootwizard').bootstrapWizard({
        onTabClick: function(tab, navigation, index) {
            return false;
        },
        onNext: function(tab, navigation, index) {
            switch (index) {
                case 2:
                    return validateSite();
                    break;
                case 3:
                    if (!db) {
                        validateDb();
                    }
                    return db;
                    break;
            }
        },
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-danger'
    });

    $('#lang').change(function(){
        var url = window.location.href.split('?')[0] + '?lang=' + $('#lang').val();
        document.location.href = url;
    });

    $('#db_drive').change(function(){
        db = false;
    });


	/** Comment for production! **/
	// prefill();
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
		}else{
			$("#admin"+ucwords(key)).tooltip('hide');
		}
	});

    if (valid) {
        $('#install-form').submit();
    }

	return valid;

}

function validateDb(){

        var valid = true;
		var obj = {};

		obj['host'] = $("#dbHost").val();
		obj['user'] = $("#dbUser").val();
		obj['pass'] = $("#dbPass").val();
		obj['name'] = $("#dbName").val();
		obj['db_drive'] = $("#db_drive").val();

		$.each(obj,function(key,value){
			if ( key != "pass" && ( value == "" || value == null ) ){
				$("#db"+ucwords(key)).tooltip('show');
                valid = false;
                db = false;
			}else{
				$("#db"+ucwords(key)).tooltip('hide');
			}
		});

		if ( valid ){
			$("#db-ajax").css('visibility','visible');
			$.ajax({
				async:false,
				type:"POST",
				url:"index.php?task=postinstall&m=validateDb",
				data:obj,
				dataType:'JSON',
				success:function(data){
					$("#db-ajax").css('visibility','hidden');
					if ( data.valid ){
						$("#database-validation-message").empty().append("<div class='alert alert-success'>Everything checks out!</div>");
                        db = true;
                        $('a.btn-next').click();
					}else{
						$("#database-validation-message").empty().append("<div class='alert alert-danger'>There appears to be a problem! <b>"+data.error+"</b></div>");
                        db = false;
					}
				}
			})
		}
}

function ucwords (str) {
    // Uppercase the first character of every word in a string
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}
