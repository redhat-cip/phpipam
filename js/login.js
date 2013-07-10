/**
 *
 * Javascript / jQuery login functions
 *
 *
 */


$(document).ready(function() {

/* hide error div if jquery loads ok
*********************************************/
$('div.jqueryError').hide();
$('div.loading').hide();


/*	loading spinner functions
*******************************/
function showSpinner() {
    $('div.loading').show();
}
function hideSpinner() {
    $('div.loading').fadeOut('fast');
}

/*	Login redirect function if success
****************************************/
function loginRedirect() { window.location="dashboard/"; }

/*	submit login 
*********************/
$('form#login').submit(function() {
    //stop all active animations
    $('div#loginCheck').stop(true,true);
    //get login data
    var logindata = $(this).serialize();
    
    $('div#loginCheck').hide();
    //post to check form
    $.post('site/login/loginCheck.php', logindata, function(data) {
        $('div#loginCheck').html(data).fadeIn('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            showSpinner();
            //search for redirect
            if($('form#login input#phpipamredirect').length > 0) { setTimeout(function (){window.location=$('form#login input#phpipamredirect').val();}, 1500); }
            else 												 { setTimeout(loginRedirect, 1000);	}
        }
    });
    return false;
});

/*	auto-suggest first available IP in selected subnet
********************************************************/
$(document).on("change", "select#subnetId", function() {
	showSpinner();
	var subnetId = $('select#subnetId option:selected').attr('value');
	//post it via json to requestIPfirstFree.php
	$.post('site/login/requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
		$('input.ip_addr').val(data);
		hideSpinner();
	});
});

/*	submit IP request
*****************************************/
$(document).on("submit", "#requestIP", function() {
	var subnet = $('#requestIPsubnet').serialize();
	var IPdata = $(this).serialize();
	var postData = subnet + "&" + IPdata;
	
	showSpinner();
	
    //post to check form
    $.post('site/login/requestIPresult.php', postData, function(data) {
        $('div#requestIPresult').html(data).slideDown('fast');
        hideSpinner();
        //reset sender to prevent duplicates
        $('input[name=requester]').val('');
    });
	return false;
});

});


