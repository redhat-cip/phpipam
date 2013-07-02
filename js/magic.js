/**
 *
 * Javascript / jQuery functions
 *
 *
 */

$(document).ready(function () {



/* @general functions */

/*loading spinner functions */
function showSpinner() { $('div.loading').show(); }
function hideSpinner() { $('div.loading').fadeOut('fast'); }

/* hide error div if jquery loads ok
*********************************************/
$('div.jqueryError').hide();

/* Show / hide JS error */
function showError(errorText) {
	$('div.jqueryError').fadeIn('fast');
	if(errorText.length>0)  { $('.jqueryErrorText').html(errorText).show(); }
	hideSpinner();
}
function hideError() {
	$('.jqueryErrorText').html();
	$('div.jqueryError').fadeOut('fast');
}
//hide error popup
$(document).on("click", "#hideError", function() {
	hideError();
	return false;
});

/* tooltip hiding fix */
function hideTooltips() { $('.tooltip').hide(); }

/* popups */
function showPopup(pClass) {
    $('#popupOverlay').fadeIn('fast');
    $('.'+pClass).fadeIn('fast');
    $('body').addClass('stop-scrolling');        //disable page scrolling on bottom
}
function hidePopup(pClass) {
    $('.'+pClass).fadeOut('fast');
}
function hidePopups() {
    $('#popupOverlay').fadeOut('fast');
    $('.popup').fadeOut('fast');
    $('body').removeClass('stop-scrolling');        //enable scrolling back
    $('.popup_w700').css("z-index", "100");        //set popup back
    hideSpinner();
}
function hidePopup2() {
    $('.popup_w400').fadeOut('fast');
    $('.popup_w500').fadeOut('fast');
    $('.popup_w700').css("z-index", "100");        //set popup back
    hideSpinner();
}
$(document).on("click", "#popupOverlay, button.hidePopups", function() { hidePopups(); });
$(document).on("click", "button.hidePopup2", function() { hidePopup2(); });

//prevent loading for disabled buttons
$('a.disabled, button.disabled').click(function() { return false; });

//fix for menus on ipad
$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); });

/*    generate random password */
function randomPass() {
    var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    var pass = "";
    var x;
    var i;
    for(x=0; x<10; x++) {
        i = Math.floor(Math.random() * 62);
        pass += chars.charAt(i);
    }
    return pass;
}

/* reload */
function reloadPage() {
	window.location.reload();
}

/* remove self on click */
$(document).on("click", ".selfDestruct", function() {
	$(this).parent('div').fadeOut('fast');
});


/* @cookies */
function createCookie(name,value,days) {
    var date;
    var expires;
    
    if (days) {
        date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    }
    else {
    }
    document.cookie = name+"="+value+expires+"; path=/";
}
function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}









/* @dashboard graphs ----------  */

if ($('#IPv4top10Hosts').length>0) {
	//first
	$.post('site/dashboard/top10_percentage.php', function(data) {
		$('#IPv4top10').html(data);
		// second
		$.post('site/dashboard/top10_hosts.php', {type:"IPv4"}, function(data) {
			$('#IPv4top10Hosts').html(data);
			//third
			$.post('site/dashboard/top10_hosts.php', {type:"IPv6"}, function(data) {
				$('#IPv6top10Hosts').html(data);
			});		
		});	
	});
}










/* @subnets list ----------  */

/* leftmenu toggle submenus */
// default hide
$('ul.submenu.submenu-close').hide();
// left menu folder delay tooltip
$('.icon-folder-close,.icon-folder-show, .icon-search').tooltip( {
    delay: {show:2000, hide:0}, 
    placement:"bottom"
});
// show submenus
$('ul#subnets').on("click", ".icon-folder-close", function() {
    //change icon
    $(this).removeClass('icon-folder-close').addClass('icon-folder-open');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideDown('fast');
});
// hide submenus
$('ul#subnets').on("click", "i.icon-folder-open", function() {
    //change icon
    $(this).removeClass('icon-folder-open').addClass('icon-folder-close');
    //find next submenu and hide it
    $(this).nextAll('.submenu').slideUp('fast');
});

//hide subnets list
$('#hideSubnets').click(function() {
    $('#leftMenu').hide('fast');
    //expand content
    $('#content').css("width","97.9147%");
    return false;
});

//expand/contract all
$('#expandfolders').click(function() {
    // get action
    var action = $(this).attr('data-action');
    //open
    if(action == 'close') {
        $('.subnets ul#subnets li.folder > i').removeClass('icon-folder-close').addClass('icon-folder-open');
        $('.subnets ul#subnets ul.submenu').removeClass('submenu-close').addClass('submenu-open').slideDown('fast');
        $(this).attr('data-action','open');
        createCookie('expandfolders','1','365');
        $(this).removeClass('icon-resize-full').addClass('icon-resize-small');
    }
    else {
        $('.subnets ul#subnets li.folder > i').addClass('icon-folder-close').removeClass('icon-folder-open');
        $('.subnets ul#subnets ul.submenu').addClass('submenu-close').removeClass('submenu-open').slideUp('fast');
        $(this).attr('data-action','close');
        createCookie('expandfolders','0','365');
        $(this).removeClass('icon-resize-small').addClass('icon-resize-full');
    }
});










/* @ipaddress list ---------- */


/*    add / edit / delete IP address
****************************************/
//show form
$(document).on("click", ".modIPaddr", function() {
    showSpinner();        
    var action    = $(this).attr('data-action');
    var id        = $(this).attr('data-id');
    var subnetId  = $(this).attr('data-subnetId');
    //format posted values
    var postdata = "action="+action+"&id="+id+"&subnetId="+subnetId;
    $.post('site/ipaddr/modifyIpAddress.php', postdata, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//move orphaned IP address
$(document).on("click", "a.moveIPaddr", function() {
    showSpinner();        
    var action      = $(this).attr('data-action');
    var id        = $(this).attr('data-id');
    var subnetId  = $(this).attr('data-subnetId');
    //format posted values
    var postdata = "action="+action+"&id="+id+"&subnetId="+subnetId;
    $.post('site/ipaddr/moveIpAddress.php', postdata, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//resolve DNS name
$(document).on("click", "#refreshHostname", function() {
    showSpinner();
    var ipaddress = $('input.ip_addr').val();
    $.post('site/tools/resolveDNS.php', {ipaddress:ipaddress}, function(data) {
        if(data.length !== 0) {
            $('input[name=dns_name]').val(data);
        }
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});
//submit ip address change
$(document).on("click", "button#editIPAddressSubmit", function() {
    //show spinner
    showSpinner();
    var postdata = $('form.editipaddress').serialize();
    
    //replace delete if from visual
    if($(this).attr('data-action') == "all-delete" ) { postdata = postdata + '&action-visual=delete';}

    $.post('site/ipaddr/modifyIpAddressCheck.php', postdata, function(data) {
        $('div.addnew_check').html(data);
        $('div.addnew_check').slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//ping check
$(".ping_ipaddress").click(function() {
	showSpinner();
	var id       = $(this).attr('data-id');
	var subnetId = $(this).attr('data-subnetId');
	//check
	$.post('site/ipaddr/pingCheck.php', {id:id, subnetId:subnetId}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
		hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});


/*    send notification mail
********************************/
//show form
$('a.mail_ipaddress').click(function () {
    //get IP address id
    var IPid = $(this).attr('data-id');
    $.post('site/ipaddr/mailNotifyIP.php', { id:IPid }, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//send mail with IP details!
$(document).on("click", "#mailIPAddressSubmit", function() {
    showSpinner();
    var mailData = $('form#mailNotify').serialize();
    //post to check script
    $.post('site/ipaddr/mailNotifyCheck.php', mailData, function(data) {
        $('div.sendmail_check').html(data).slideDown('fast');
        //hide if success!
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){hidePopups();}, 1500); }
        else                             { hideSpinner(); }    
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});




/*    sort IP address list
*********************************************************/
$(document).on("click", "table.ipaddresses th a.sort", function() {
    showSpinner();
    
    $(this).tooltip('hide');                            //hide tooltips fix for ajax-load
    
    var direction = $(this).attr('data-id');            //sort direction
    var subnetId  = $(this).attr('data-subnetId');        //id of the subnet
    
    $.post('site/ipaddr/ipAddressPrintTable.php', {direction:direction, subnetId:subnetId}, function(data) {
        $('div.ipaddresses_overlay').html(data);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    scan subnet
*************************/
//open popup
$('a.scan_subnet').click(function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	$.post('site/ipaddr/subnetScan.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
		hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//start scanning
$(document).on('click','#subnetScanSubmit', function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	var pingType = $('select[name=scanType]').find(":selected").val();
	$('#alert-scan').slideUp('fast');
	$.post('site/ipaddr/subnetScan'+pingType+".php", {subnetId:subnetId, pingType:pingType}, function(data) {
        $('#subnetScanResult').html(data);
		hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
})


/*    import IP addresses
*************************/
//load CSV import form
$('a.csvImport').click(function () {
    showSpinner();
    var subnetId = $(this).attr('data-subnetId');
    $.post('site/admin/CSVimport.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//display uploaded file
$(document).on("click", "input#csvimportcheck", function() {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    $.post('site/admin/CSVimportShowFile.php', { filetype : filetype }, function(data) {
        $('div.csvimportverify').html(data).slideDown('fast');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});
//import file script
$(document).on("click", "input#csvImportNo", function() {
    $('div.csvimportverify').hide('fast');
});
$(document).on("click", "input#csvImportYes", function() {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    // get active subnet ID
    var xlsSubnetId  = $('a.csvImport').attr('data-subnetId');
    var postData = "subnetId=" + xlsSubnetId + "&filetype=" + filetype;

    $.post('site/admin/CSVimportSubmit.php', postData, function(data) {
        $('div.csvImportResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});


/*    export IP addresses
*************************/
//show fields
$('a.csvExport').click(function() {
    showSpinner();
    var subnetId = $(this).attr('data-subnetId');
    //show select fields
    $.post('site/ipaddr/exportSelectFields.php', {subnetId:subnetId}, function(data) {
	    $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//export
$(document).on("click", "button#exportSubnet", function() {
    var subnetId = $('a.csvExport').attr('data-subnetId');
    //get selected fields
    var exportFields = $('form#selectExportFields').serialize();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportSubnet.php?subnetId=" + subnetId + "&" + exportFields + "'></iframe></div>");
    return false;
});


/*    request IP address for non-admins if locked or viewer
*********************************************************/
//show request form
$('a.request_ipaddress').click(function () {
    showSpinner();
    var subnetId  = $(this).attr('data-subnetId');
    $.post('site/tools/requestIPform.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//auto-suggest first available IP in selected subnet
$(document).on("click", "select#subnetId", function() {
    showSpinner();
    var subnetId = $('select#subnetId option:selected').attr('value');
    //post it via json to requestIPfirstFree.php
    $.post('site/login/requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
        $('input.ip_addr').val(data);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});

//submit request
$(document).on("click", "button#requestIPAddressSubmit", function() {
    showSpinner();
    var request = $('form#requestIP').serialize();
    $.post('site/login/requestIPresult.php', request, function(data) {
        $('div#requestIPresult').html(data).slideDown('fast');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});




//jump to page
$('select.jumptoPage').change(function() {
	var active    = $(this).find(":selected");
	var sectionId = active.attr('data-sectionId');
	var subnetId  = active.attr('data-subnetId');
    var page 	  = active.val(); 
    window.location.href = "subnets/"+sectionId+"/"+subnetId+"/"+page+"/";
});









/* @tools ----------- */


/* ipCalc */
//submit form
$('form#ipCalc').submit(function () {
    showSpinner();
    var ipCalcData = $(this).serialize();
    $.post('site/tools/ipCalcResult.php', ipCalcData, function(data) {
        $('div.ipCalcResult').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//reset input
$('form#ipCalc input.reset').click(function () {
    $('form#ipCalc input[type="text"]').val('');
    $('div.ipCalcResult').fadeOut('fast');
});


/* search */
//submit form
$('form#search').submit(function () {
    showSpinner();
    var ip = $('form#search .search').val();
    //update search page
    window.location = "tools/search/" + ip;
    return false;
});
//search export
$('a#exportSearch').click(function() {
    var searchTerm = $('form#search .search').val();
    $("div.dl").remove();                                                //remove old innerDiv
    $('div.exportDIVSearch').append("<div style='display:none' class='dl'><iframe src='site/tools/searchResultsExport.php?searchTerm=" + searchTerm + "'></iframe></div>");
    return false;
});


/* switches */
$('table#switchMainTable button[id^="switch-"]').click(function() {
    var swid = $(this).attr('id');                    //get id
    // change icon to down
    if( $('#content-'+swid).is(':visible') )     { $(this).children('i').removeClass('icon-chevron-down').addClass('icon-chevron-right'); }    //hide
    else                                         { $(this).children('i').removeClass('icon-chevron-right').addClass('icon-chevron-down'); }    //show
    //show content
    $('table#switchMainTable tbody#content-'+swid).slideToggle('fast');
});


/* hosts */
$('#hosts').submit(function() {
    showSpinner();
    var hostname = $('input.hostsFilter').val();    
    window.location = "tools/hosts/"+hostname;
    return false;
});


/* user menu selfchange */
$('form#userModSelf').submit(function () {
    var selfdata = $(this).serialize(); 
    $('div.userModSelfResult').hide();
    
    $.post('site/tools/userMenuSelfMod.php', selfdata, function(data) {
        $('div.userModSelfResult').html(data).fadeIn('fast').delay(2000).fadeOut('slow');
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//    Generate random pass
$(document).on("click", "#randomPassSelf", function() {
    var password = randomPass();
    $('input.userPass').val(password);
    $('#userRandomPass').html( password );
    return false;
});









/* @administration ---------- */

/* save server settings */
$('#settings').submit(function() {
    showSpinner();
    var settings = $(this).serialize();
    //load submit results
    $.post('site/admin/settingsEdit.php', settings, function(data) {
        $('div.settingsEdit').html(data).slideDown('fast');
        //reload after 1 second if all is ok!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    Edit users
***************************/
//open form
$('.editUser').click(function () {
    showSpinner();
    var id     = $(this).attr('data-userid');
    var action = $(this).attr('data-action');
    
    $.post('site/admin/usersEditPrint.php',{id:id, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//submit form
$(document).on("click", "#editUserSubmit", function() {
    showSpinner();
    var loginData = $('form#usersEdit').serialize();
    
    $.post('site/admin/usersEditResult.php', loginData, function(data) {
        $('div.usersEditResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//disable pass if domain user
$(document).on("change", "select#domainUser", function() {
    //get details - we need Section, network and subnet bitmask
    var type = $(this).val();
    //we changed to domain
    if(type == "1") { $('input.userPass').attr('disabled',''); }
    else             { $('input.userPass').removeAttr('disabled'); }
});
// generate random pass
$(document).on("click", "a#randomPass", function() {
    var password = randomPass();
    $('input.userPass').val(password);
    $(this).html( password );
    return false;
});


/*    Edit groups
***************************/
//open form
$('.editGroup').click(function () {
    showSpinner();
    var id     = $(this).attr('data-groupid');
    var action = $(this).attr('data-action');
    
    $.post('site/admin/groupEditPrint.php',{id:id, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//submit form
$(document).on("click", "#editGroupSubmit", function() {
    showSpinner();
    var loginData = $('form#groupEdit').serialize();
    
    $.post('site/admin/groupEditResult.php', loginData, function(data) {
        $('div.groupEditResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});    
    return false;
});
//add users to group - show form
$('.addToGroup').click(function() {
    showSpinner();
	var g_id = $(this).attr('data-groupid');	

    $.post('site/admin/groupAddUsers.php',{g_id:g_id}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});	
	return false;
});
//add users to group
$(document).on("click", "#groupAddUsersSubmit", function() {
	showSpinner();
	var users = $('#groupAddUsers').serialize();

    $.post('site/admin/groupAddUsersResult.php', users, function(data) {
        $('div.groupAddUsersResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});	
	return false;
});
//remove users frmo group - show form
$('.removeFromGroup').click(function() {
    showSpinner();
	var g_id = $(this).attr('data-groupid');	

    $.post('site/admin/groupRemoveUsers.php',{g_id:g_id}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});	
	return false;
});
//add users to group
$(document).on("click", "#groupRemoveUsersSubmit", function() {
	showSpinner();
	var users = $('#groupRemoveUsers').serialize();

    $.post('site/admin/groupRemoveUsersResult.php', users, function(data) {
        $('div.groupRemoveUsersResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});	
	return false;
});



/*    Edit AD settings
********************************/
$('form#ad').submit(function() {
    showSpinner();
    var addata = $(this).serialize();
    $.post('site/admin/manageADresult.php', addata, function(data) {
        $('div.manageADresult').html(data).slideDown('fast').delay(2000).fadeOut('slow');
            hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//check AD settings
$('#checkAD').click(function() {
    showSpinner();
    var addata = $('form#ad').serialize();
    $.post('site/admin/manageADcheck.php', addata, function(data) {
        $('div.manageADresult').html(data).slideDown('fast'); hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    instructions
***********************/
$('#instructionsForm').submit(function () {
	var instructions = CKEDITOR.instances.instructions.getData();
	$('div.instructionsPreview').hide('fast');
    
    showSpinner();
    $.post('site/admin/instructionsResult.php', {instructions:instructions}, function(data) {
        $('div.instructionsResult').html(data).fadeIn('fast');
        if(data.search("error") == -1)     	{ $('div.instructionsResult').delay(2000).fadeOut('slow'); hideSpinner(); }
        else                             	{ hideSpinner(); }      
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
$('#preview').click(function () {
    showSpinner();
    var instructions = CKEDITOR.instances.instructions.getData();

    $.post('site/admin/instructionsPreview.php', {instructions:instructions}, function(data) {
        $('div.instructionsPreview').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    log files
************************/
//display log files - selection change
$('form#logs').change(function () {
    showSpinner();
    var logSelection = $('form#logs').serialize();
    $.post('site/admin/logResult.php', logSelection, function(data) {
        $('div.logs').html(data);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});
//log files show details
$(document).on("click", "a.openLogDetail", function() {
    var id = $(this).attr('data-logid');
    $.post('site/admin/logDetail.php', {id:id}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();        
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//log files page change
$('#logDirection button').click(function() {
    showSpinner();
    /* get severities */
    var logSelection = $('form#logs').serialize();
    /* get first or last id based on direction */
    var direction = $(this).attr('data-direction');
    /* get Id */
    var lastId;
    if (direction == "next")     { lastId = $('table#logs tr:last').attr('id'); }
    else                         { lastId = $('table#logs tr:nth-child(2)').attr('id'); }
    
    /* set complete post */
    var postData = logSelection + "&direction=" + direction + "&lastId=" + lastId;

    /* show logs */
    $.post('site/admin/logResult.php', postData, function(data1) {
        $('div.logs').html(data1);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;   
});
//logs export 
$('#downloadLogs').click(function() {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/logsExport.php'></iframe></div>");
    hideSpinner();
    //show downloading
    $('div.logs').prepend("<div class='alert alert-info' id='logsInfo'><i class='icon-remove icon-gray selfDestruct'></i> Preparing download... </div>");
    return false;
});
//logs clear
$('#clearLogs').click(function() {
    showSpinner();
    $.post('site/admin/logClear.php', function(data) {
    	$('div.logs').html(data);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});



/*    Sections
********************************/
//load edit form
$('button.editSection').click(function() {
    showSpinner();
    var sectionId   = $(this).attr('data-sectionid');
    var action         = $(this).attr('data-action');
    //load edit data
    $.post("site/admin/manageSectionEdit.php", {sectionId:sectionId, action:action}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});
//edit section result
$(document).on("click", "#editSectionSubmit", function() {
    showSpinner();
    var sectionData = $('form#sectionEdit').serialize();
    
    $.post('site/admin/manageSectionEditResult.php', sectionData, function(data) {
        $('div.sectionEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    Subnets
********************************/
//show subnets
$('table#manageSubnets button[id^="subnet-"]').click(function() {
    showSpinner();
    var swid = $(this).attr('id');                    //get id
    // change icon to down
    if( $('#content-'+swid).is(':visible') )     { $(this).children('i').removeClass('icon-chevron-down').addClass('icon-chevron-right'); }    //hide
    else                                         { $(this).children('i').removeClass('icon-chevron-right').addClass('icon-chevron-down'); }    //show
    //show content
    $('table#manageSubnets tbody#content-'+swid).slideToggle('fast');
    hideSpinner();
});
//toggle show all / none
$('#toggleAllSwitches').click(function() {
    showSpinner();
    // show
    if( $(this).children().hasClass('icon-resize-full') ) {
        $(this).children().removeClass('icon-resize-full').addClass('icon-resize-small');            //change icon
        $('table#manageSubnets i.icon-chevron-right').removeClass('icon-chevron-right').addClass('icon-chevron-down');    //change section chevrons
        $('table#manageSubnets tbody[id^="content-subnet-"]').show();                                //show content
        createCookie('showSubnets',1,30);                                                            //save cookie
    }
    //hide
    else {
        $(this).children().removeClass('icon-resize-small').addClass('icon-resize-full');
        $('table#manageSubnets tbody[id^="content-subnet-"]').hide();    
        $('table#manageSubnets i.icon-chevron-down').removeClass('icon-chevron-down').addClass('icon-chevron-right');    //change section chevrons    
        createCookie('showSubnets',0,30);                                                            //save cookie
    }
    hideSpinner();
});
//load edit form
$('button.editSubnet').click(function() {
    showSpinner();
    var sectionId   = $(this).attr('data-sectionid');
    var subnetId    = $(this).attr('data-subnetid');
    var action         = $(this).attr('data-action');
    //format posted values
    var postdata    = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&action=" + action;
    
    //load edit data
    $.post("site/admin/manageSubnetEdit.php", postdata, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});
//resize / split subnet
$(document).on("click", "#resize, #split, #truncate", function() {
	showSpinner();
	var action = $(this).attr('id');
	var subnetId = $(this).attr('data-subnetId');
	//dimm and show popup2
    $.post("site/admin/manageSubnet"+action+".php", {action:action, subnetId:subnetId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        $('.popup_w700').css("z-index", "99");        //set behind popup
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//resize save
$(document).on("click", "button#subnetResizeSubmit", function() {
	showSpinner();
	var resize = $('form#subnetResize').serialize();
	$.post("site/admin/manageSubnetResizeSave.php", resize, function(data) {
		$('div.subnetResizeResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//split save
$(document).on("click", "button#subnetSplitSubmit", function() {
	showSpinner();
	var split = $('form#subnetSplit').serialize();
	$.post("site/admin/manageSubnetSplitSave.php", split, function(data) {
		$('div.subnetSplitResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//truncate save
$(document).on("click", "button#subnetTruncateSubmit", function() {
	showSpinner();
	var subnetId = $(this).attr('data-subnetId');
	$.post("site/admin/manageSubnetTruncateSave.php", {subnetId:subnetId}, function(data) {
		$('div.subnetTruncateResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//save edit subnet changes
$(document).on("click", ".editSubnetSubmit", function() {
    showSpinner();
    var subnetData = $('form#editSubnetDetails').serialize();
    
    //if ipaddress and delete then change action!
    if($(this).hasClass("editSubnetSubmitDelete")) {
        subnetData = subnetData.replace("action=edit", "action=delete");
    }
    
    //load results
    $.post("site/admin/manageSubnetEditResult.php", subnetData, function(data) {
        $('div.manageSubnetEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if all is ok!
        if(data.search("error") == -1) {
            showSpinner();
            var sectionId;
            var subnetId;
            var parameter;
            //reload IP address list if request came from there
            if(subnetData.search("IPaddresses") != -1) {
                //from ipcalc - load ip list
                sectionId = $('form#editSubnetDetails input[name=sectionId]').val();
                subnetId  = $('form#editSubnetDetails input[name=subnetId]').val();
                setTimeout(function (){window.location.reload();}, 1500);
            }
            //from ipcalc - ignore
            else if (subnetData.search("ipcalc") != -1) {
            }
            else {
                //from admin, reload
                setTimeout(function (){window.location.reload();}, 1500);
            }
        }
        //hide spinner - error
        else {
            hideSpinner();
        }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});

//get subnet info from ripe database
$(document).on("click", "#get-ripe", function() {
	showSpinner();
	var subnet = $('form#editSubnetDetails input[name=subnet]').val();
	
	$.getJSON("site/admin/manageSubnetEditRipeQuery.php", {subnet: subnet}, function(data) { 
		//fill fields
		$.each(data, function(key, val) {
			$('form#editSubnetDetails #field-'+key).val(val);
		});
		hideSpinner();
	});
	return false;
});
//change subnet permissions
$('.showSubnetPerm').click(function() {
	showSpinner();
	var subnetId  = $(this).attr('data-subnetId');
	var sectionId = $(this).attr('data-sectionId');
	
	$.post("site/admin/manageSubnetShowPermissions.php", {subnetId:subnetId, sectionId:sectionId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
		hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});
//submit permission change
$(document).on("click", ".editSubnetPermissionsSubmit", function() {
	showSpinner();
	var perms = $('form#editSubnetPermissions').serialize();
	$.post('site/admin/manageSubnetPermissionsSubmit.php', perms, function(data) {
		$('.editSubnetPermissionsResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
	return false;
});


/*    Add subnet from IPCalc result
*********************************/
$(document).on("click", "#createSubnetFromCalc", function() {
    $('tr#selectSection').show();
});
$(document).on("change", "select#selectSectionfromIPCalc", function() {
    //get details - we need Section, network and subnet bitmask
    var sectionId = $(this).val();
    var subnet      = $('table.ipCalcResult td#sub2').html();
    var bitmask      = $('table.ipCalcResult td#sub4').html();
    var postdata  = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&action=add&location=ipcalc";
    //make section active
    $('table.newSections ul#sections li#' + sectionId ).addClass('active');
    //load add Subnet form / popup
    $.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
});

/*    Edit subnet from ip address list
************************************/
$('a.edit_subnet, button.edit_subnet, button#add_subnet').click(function () {
    var subnetId  = $(this).attr('data-subnetId');
    var sectionId = $(this).attr('data-sectionId');
    var action    = $(this).attr('data-action');
    //format posted values
    var postdata     = "sectionId="+sectionId+"&subnetId="+subnetId+"&action="+action+"&location=IPaddresses";
    //load add Subnet form / popup
    $.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/* Show add new VLAN on subnet add/edit on-thy-fly
***************************************************/
$(document).on("change", "select[name=vlanId]", function() {
    var vlanId    = $(this).val();
    if(vlanId == 'Add') {
        showSpinner();            
        $.post('site/admin/manageVLANEdit.php', {action:"add", fromSubnet:"true"}, function(data) {
            $('div.popup_w400').html(data);
            showPopup('popup_w400');
            $('.popup_w700').css("z-index", "99");        //set behind popup
            hideSpinner();
		}).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    }
    return false;    
});
//    Submit new VLAN on the fly
$(document).on("click", ".vlanManagementEditFromSubnetButton", function() {
    showSpinner();
    var postData = $('form#vlanManagementEditFromSubnet').serialize();    
    var parameter;
    $.post('site/admin/manageVLANEditResult.php', postData, function(data) {
        $('div.vlanManagementEditFromSubnetResult').html(data).show();
        // ok
        if(data.search("error") == -1) {
            //reload add subnet
            var sectionId = $('#editSubnetDetails input[name=sectionId]').val(); 
            var subnetId  = $('#editSubnetDetails input[name=subnetId]').val(); 
            var sAction   = $('#editSubnetDetails input[name=action]').val();
            var postdata2 = "sectionId="+sectionId+"&subnetId="+subnetId+"&action="+sAction;
            $.post('site/admin/manageSubnetEdit.php', postdata2 , function(data) {
                $('div.popup_w700').html(data);
                //bring to front
                $('.popup_w700').delay(1000).css("z-index", "101");        //bring to front
                hideSpinner();
			}).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
            //hide popup after 1 second
            setTimeout(function (){hidePopup('popup_w400'); parameter = null;}, 1000);
        }
        else                      { hideSpinner(); }
    });
    return false;    
});




/*    Switches
********************************/
//open form
$('.editSwitch').click(function() {
    showSpinner();
    var switchId = $(this).attr('data-switchid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/manageDevicesEdit.php', {switchId:switchId, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;    
});
//Edit switch result
$(document).on("click", "#editSwitchsubmit", function() {
    showSpinner();
    var switchdata = $('form#switchManagementEdit').serialize();
    $.post('site/admin/manageDevicesEditResult.php', switchdata, function(data) {
        $('div.switchManagementEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/* VLAN
********************************/
//load edit form
$('.editVLAN').click(function() {
    showSpinner();
    var vlanId   = $(this).attr('data-vlanid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/manageVLANEdit.php', {vlanId:vlanId, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;    
});
//result
$(document).on("click", "#editVLANsubmit", function() {
    showSpinner();
    var vlandata = $('form#vlanManagementEdit').serialize();
    $.post('site/admin/manageVLANEditResult.php', vlandata, function(data) {
        $('div.vlanManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                               { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    VRF
*********/
//Load edit VRF form
$('button.vrfManagement').click(function() {
    showSpinner();
    var vrfId    = $(this).attr('data-vrfid');
    var action   = $(this).attr('data-action');
    var switchpost = "vrfId=" + vrfId + "&action=" + action;
    $.post('site/admin/manageVRFEdit.php', switchpost, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;    
});
//Edit VRF details
$(document).on("click", "#editVRF", function() {
    showSpinner();
    var vrfdata = $('form#vrfManagementEdit').serialize();
    $.post('site/admin/manageVRFEditResult.php', vrfdata, function(data) {
        $('div.vrfManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    edit IP request
***********************/
//show form
$('table#requestedIPaddresses button').click(function() {
    showSpinner();
    var requestId = $(this).attr('data-requestid');
    $.post('site/admin/manageRequestEdit.php', { requestId: requestId }, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;    
});
//approve / reject
$(document).on("click", "button.manageRequest", function() {
    showSpinner();
    var postValues = $('form.manageRequestEdit').serialize();
    var action     = $(this).attr('data-action');
    var postData   = postValues+"&action="+action;
    $.post('site/admin/manageRequestResult.php', postData, function(data) {
        $('div.manageRequestResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    Ripe AS import
****************************/
//get subnets form AS
$('form#ripeImport').submit(function() {
    showSpinner();
    var as = $(this).serialize();
    $.post('site/admin/ripeImportTelnet.php', as, function(data) {
        $('div.ripeImportTelnet').html(data).fadeIn('fast');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
// remove as line
$(document).on("click", "table.asImport .removeSubnet", function() {
    $(this).parent('tr').remove();
    hideTooltips();
});
// add selected to db
$(document).on("submit", "form#asImport", function() {
    showSpinner();
    //get subnets to add
    var importData = $(this).serialize();
    $.post('site/admin/ripeImportResult.php', importData, function(data) {
        $('div.ripeImportResult').html(data).slideDown('fast');
        //hide after 2 seconds
        if(data.search("error") == -1)     { $('table.asImport').delay(1000).fadeOut('fast'); hideSpinner(); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    set selected IP fields
********************************/
$('button#filterIPSave').click(function() {
    showSpinner();
    var addata = $('form#filterIP').serialize();
    $.post('site/admin/filterIPFieldsResult.php', addata, function(data) {
        $('div.filterIPResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { $('div.filterIPResult').delay(2000).fadeOut('slow');    hideSpinner(); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    custom IP fields
************************************/
//load edit form
$('table.customIP tbody#ip button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customIPFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//custom IP field edit submit form
$(document).on("click", "#editcustomSubmit", function() {
    showSpinner();
    var field = $('form#editCustomIPFields').serialize();
    $.post('site/admin/customIPFieldsEditResult.php', field, function(data) {
        $('div.customIPEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
// field ordering
$('table.customIP tbody#ip button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customIPFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customIPResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    custom subnet fields
************************************/
//load edit form
$('table.customIP tbody#subnet button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customSubnetFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//custom IP field edit submit form
$(document).on("click", "#editcustomSubnetSubmit", function() {
    showSpinner();
    var field = $('form#editCustomSubnetFields').serialize();
    $.post('site/admin/customSubnetFieldsEditResult.php', field, function(data) {
        $('div.customSubnetEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
// field ordering
$('table.customIP tbody#subnet button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customSubnetFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customSubnetResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    custom VLAN fields
************************************/
//load edit form
$('table.customIP tbody#vlan button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customVLANFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//custom IP field edit submit form
$(document).on("click", "#editcustomVLANSubmit", function() {
    showSpinner();
    var field = $('form#editCustomVLANFields').serialize();
    $.post('site/admin/customVLANFieldsEditResult.php', field, function(data) {
        $('div.customVLANEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
// field ordering
$('table.customIP tbody#vlan button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customVLANFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customVLANResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/*    custom user fields
************************************/
//load edit form
$('table.customIP tbody#customUser button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customUserFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
//custom IP field edit submit form
$(document).on("click", "#editcustomUserSubmit", function() {
    showSpinner();
    var field = $('form#editCustomUserFields').serialize();
    $.post('site/admin/customUserFieldsEditResult.php', field, function(data) {
        $('div.customUserEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});
// field ordering
$('table.customIP tbody#customUser button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customUserFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customUserResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)   { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});



/* Languages
*********/
//Load edit lang form
$('button.lang').click(function() {
    showSpinner();
    var langid    = $(this).attr('data-langid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/languageEdit.php', {langid:langid, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;    
});
//Edit lang details
$(document).on("click", "#langEditSubmit", function() {
    showSpinner();
    var ldata = $('form#langEdit').serialize();
    $.post('site/admin/languageEditResult.php', ldata, function(data) {
        $('div.langEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     	{ setTimeout(function (){window.location.reload();}, 1500); }
        else                             	{ hideSpinner(); }
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});






/*    Search and replace
************************/
$('button#searchReplaceSave').click(function() {
    showSpinner();
    var searchData = $('form#searchReplace').serialize();    
    $.post('site/admin/searchReplaceResult.php', searchData, function(data) {
        $('div.searchReplaceResult').html(data);
        hideSpinner();
    }).fail(function(xhr, textStatus, errorThrown) { showError(xhr.statusText);});
    return false;
});


/* exports
***********************/
// XLS exports
$('button#XLSdump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateXLS.php'></iframe></div>");
    hideSpinner();
});
// MySQL export
$('button#MySQLdump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateMySQL.php'></iframe></div>");
    hideSpinner();
});
// Hostfile export
$('button#hostfileDump').click(function () {
    showSpinner();
    $("div.dl").remove();    //remove old innerDiv
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateHostDump.php'></iframe></div>");
    hideSpinner();
});



return false;
});