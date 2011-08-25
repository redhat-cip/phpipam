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
function loginRedirect() {
    window.location="../";
}

/*	Hide success div after 2 sec
********************************/
$('div.success').animate({opacity: 1.0}, 2000, function() {
    $(this).fadeOut('fast');
});

/*	close error div on click
*******************************/
$('div.error').live('click', function() {
	$(this).fadeOut();
});


/***************************************************************
		load hash-requested site
***************************************************************/
var hash = document.location.toString();

showSpinner();

if (hash.match('#')) 
{
	var page = hash.split('#')[1];
		
	//do nothig if login
	if (page == 'requestIP') {
		loadPage (page);
	}
	else {
		loadPage ('login');
	}
}
else 
    loadPage('login');


/*	load subpage function
**************************/
function loadPage (page) {
	if (page == 'login') {
		$('div#login').hide().removeClass('requestIP').load('loginForm.php', function() {
  			hideSpinner();
  			$('div#login').fadeIn('slow');
		});
	}
	else {
		$('div#login').hide().addClass('requestIP').load('requestIPform.php', function() {
  			hideSpinner();
  			$('div#login').fadeIn('slow');
		});
	}
	hideSpinner();
}


/*	submit login 
*********************/
$('form#login').live('submit', function() {

    //stop all active animations
    $('div#loginCheck').stop(true,true);
    
    //get login data
    logindata = $(this).serialize();
    
    $('div#loginCheck').hide();
    //post to check form
    $.post('loginCheck.php', logindata, function(data) {
        $('div#loginCheck').html(data).fadeIn('fast').animate({opacity: 1}, 3000).fadeOut('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            showSpinner();
            setTimeout(loginRedirect, 1000);
        }
    });
    return false;
});

/*	hide login response after 3 seconds 
*****************************************/
$('div#loginCheck').live('click', function() {
    //stop all active animations
    $('div#loginCheck').stop(true,true).fadeOut('fast');
});


/*	open request IP site
*****************************************/
$('a.requestIP').live('click', function() {

	showSpinner();
	
	$('div#login').slideUp('fast', function() {
	
		$('div#login').addClass('requestIP').load('requestIPform.php', function() {
  			hideSpinner();
  			$('div#login').slideDown('fast');
		});
	});
});


/*	auto-suggest first available IP in selected subnet
********************************************************/
$('select#subnetId').live('change', function() {
	showSpinner();
	var subnetId = $('select#subnetId option:selected').attr('value');
	//post it via json to requestIPfirstFree.php
	$.post('requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
		$('input.ip_addr').val(data);
		hideSpinner();
	});
});


/*	back to login
*****************************************/
$('a.backToLogin').live('click', function() {

	showSpinner();
	
	$('div#login').slideUp('fast', function() {
		$('div#login').removeClass('requestIP').load('loginForm.php', function() {
  			hideSpinner();
  			$('div#login').slideDown('fast');
		});
	});
});

/*	submit IP request
*****************************************/
$('#requestIP').live('submit', function() {

	var subnet = $('#requestIPsubnet').serialize();
	var IPdata = $(this).serialize();
	var postData = subnet + "&" + IPdata;
	
	showSpinner();
	
    //post to check form
    $.post('requestIPresult.php', postData, function(data) {
        $('div#requestIPresult').html(data).slideDown('fast');
        hideSpinner();
    });


	return false;
});



});


