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
    $('.popup_w700').css("z-index", "100");        //set popup back
    hideSpinner();
}
$('#popupOverlay, button.hidePopups').live('click', function() { hidePopups(); });
$('button.hidePopup2').live('click', function() { hidePopup2(); });

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

/* open location */
function openLocation(loc0, loc1, loc2) {
    var loc0;
    var loc1;
    var loc2;
    //only loc0
    if(loc1.length === 0)     { window.location = loc0+"/"; }
    //loc0 + loc1
    if(loc2.length === 0)     { window.location = loc0+"/"+loc1+"/"; }
    //both
    else                      { window.location = loc0+"/"+loc1+"/"+loc2+"/"; }
}
/* reload */
function reloadPage() {
	window.location.reload();
}

/* remove self on click */
$('.selfDestruct').live('click', function() {
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
$('a.modIPaddr').live("click", function () {
    showSpinner();        
    var action      = $(this).attr('data-action');
    var id        = $(this).attr('data-id');
    var subnetId  = $(this).attr('data-subnetId');
    //format posted values
    var postdata = "action="+action+"&id="+id+"&subnetId="+subnetId;
    $.post('site/ipaddr/modifyIpAddress.php', postdata, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    });
    return false;
});
//move orphaned IP address
$('a.moveIPaddr').live("click", function () {
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
    });
    return false;
});
//    resolve DNS name
$('#refreshHostname').live('click', function() {
    showSpinner();
    var ipaddress = $('input.ip_addr').val();
    $.post('site/tools/resolveDNS.php', {ipaddress:ipaddress}, function(data) {
        if(data.length !== 0) {
            $('input[name=dns_name]').val(data);
        }
        hideSpinner();
    });
});
//    submit ip address change
$('button#editIPAddressSubmit').live("click", function () {
    //show spinner
    showSpinner();
    var postdata = $('form.editipaddress').serialize();

    $.post('site/ipaddr/modifyIpAddressCheck.php', postdata, function(data) {
        $('div.addnew_check').html(data);
        $('div.addnew_check').slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });    
    return false;
});


/*    send notification mail
********************************/
//show form
$('a.mail_ipaddress').live("click", function () {
    //get IP address id
    var IPid = $(this).attr('data-id');
    $.post('site/ipaddr/mailNotifyIP.php', { id:IPid }, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    });
    return false;
});
//send mail with IP details!
$('#mailIPAddressSubmit').live('click', function () {
    showSpinner();
    var mailData = $('form#mailNotify').serialize();
    //post to check script
    $.post('site/ipaddr/mailNotifyCheck.php', mailData, function(data) {
        $('div.sendmail_check').html(data).slideDown('fast');
        //hide if success!
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){hidePopups();}, 1500); }
        else                             { hideSpinner(); }    
    });
    return false;
});




/*    sort IP address list
*********************************************************/
$('table.ipaddresses th a.sort').live('click', function() {
    showSpinner();
    
    $(this).tooltip('hide');                            //hide tooltips fix for ajax-load
    
    var direction = $(this).attr('data-id');            //sort direction
    var subnetId  = $(this).attr('data-subnetId');        //id of the subnet
    
    $.post('site/ipaddr/ipAddressPrintTable.php', {direction:direction, subnetId:subnetId}, function(data) {
        $('div.ipaddresses_overlay').html(data);
        hideSpinner();
    });
    return false;
});


/*    import IP addresses
*************************/
// load CSV import form
$('a.csvImport').click(function () {
    showSpinner();
    $.post('site/admin/CSVimport.php', function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    });
    return false;
});
//    display uploaded file
$('input#csvimportcheck').live('click', function () {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    $.post('site/admin/CSVimportShowFile.php', { filetype : filetype }, function(data) {
        $('div.csvimportverify').html(data).slideDown('fast');
        hideSpinner();
    });
});
//    import file script
$('input#csvImportNo').live('click',function () {
    $('div.csvimportverify').hide('fast');
});
$('input#csvImportYes').live('click',function () {
    showSpinner();
    //get filetype
    var filetype = $('span.fname').html();
    /* get active subnet ID */
    var xlsSubnetId  = $('a.csvImport').attr('data-subnetId');
    var postData = "subnetId=" + xlsSubnetId + "&filetype=" + filetype;

    $.post('site/admin/CSVimportSubmit.php', postData, function(data) {
        $('div.csvImportResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });

});


/*    export IP addresses
*************************/
//show fields
$('a.csvExport').click(function() {
    showSpinner();
    //show select fields
    $('div.popup_w400').load('site/ipaddr/exportSelectFields.php', function() {
        showPopup('popup_w400');
        hideSpinner();
    });    
    return false;
});
//export
$('button#exportSubnet').live("click", function () {
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
$('a.request_ipaddress').live("click", function () {
    showSpinner();
    var subnetId  = $(this).attr('data-subnetId');
    $.post('site/tools/requestIPform.php', {subnetId:subnetId}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();
    });
    return false;
});
//    auto-suggest first available IP in selected subnet
$('select#subnetId').live('change', function() {
    showSpinner();
    var subnetId = $('select#subnetId option:selected').attr('value');
    //post it via json to requestIPfirstFree.php
    $.post('site/login/requestIPfirstFree.php', { subnetId:subnetId}, function(data) {
        $('input.ip_addr').val(data);
        hideSpinner();
    });
});

//    submit request
$('button#requestIPAddressSubmit').live('click', function() {
    showSpinner();
    var request = $('form#requestIP').serialize();
    $.post('site/login/requestIPresult.php', request, function(data) {
        $('div#requestIPresult').html(data).slideDown('fast');
        hideSpinner();
    });

    return false;
});


/*    IP address next/prev page
*********************************************************/
//prev
$('a#prevItem').live('click', function() {
    $('div.loading').fadeIn('fast');
    var active = $('table.ipaddresses').find("tbody.ipPart:visible");
    //not first!
    var index = active.index();
    var dolzina = ($('table.ipaddresses tbody').length) - 1;
    if(index != 1) {
        $(active).prev('tbody:hidden').css({'display':'table-row-group'});
        $(active).css({'display':'none'});    
        $('span.stran').html("Page " + (index-1) + '/' + dolzina);    //change text
        //change select
        $("select.jumptoPage").val('page-'+(index-2));
    }
    $('div.loading').fadeOut('fast');
    return false;
});
//next
$('a#nextItem').live('click', function() {
    $('div.loading').fadeIn('fast');
    var active = $('table.ipaddresses').find("tbody.ipPart:visible");
    //not last!
    var index = active.index();
    var dolzina = ($('table.ipaddresses tbody').length) - 1;
    
    if(index != dolzina) {
        $(active).next('tbody:hidden').css({'display':'table-row-group'});
        $(active).css({'display':'none'});    
        $('span.stran').html("Page " + (index+1) + '/' + dolzina);    //change text
        //change select
        $("select.jumptoPage").val('page-'+index);
    }
    $('div.loading').fadeOut('fast');
    return false;
});
//jump to page
$('select.jumptoPage').change(function() {
    $('div.loading').fadeIn('fast');
    var page = $(this).val();        //get page id
    var pageTemp = page.replace("page-","");
    var active = $('table.ipaddresses').find("tbody.ipPart:visible");
    
    $('table.ipaddresses tbody.'+page).css({'display':'table-row-group'});    //show new
    $(active).css({'display':'none'});
    
    pageTemp++;
    var dolzina = ($('table.ipaddresses tbody').length) - 1;
    $('span.stran').html("Page " + pageTemp + '/' + dolzina);        //change text
    $('div.loading').fadeOut('fast');
});








/* @tools ----------- */


/* ipCalc */
//submit form
$('form#ipCalc').live('submit', function () {
    showSpinner();
    var ipCalcData = $(this).serialize();
    $.post('site/tools/ipCalcResult.php', ipCalcData, function(data) {
        $('div.ipCalcResult').html(data).fadeIn('fast');
        hideSpinner();
    });
    return false;
});
//reset input
$('form#ipCalc input.reset').live('click', function () {
    $('form#ipCalc input[type="text"]').val('');
    $('div.ipCalcResult').fadeOut('fast');
});


/* search */
//submit form
$('form#search').live('submit', function () {
    showSpinner();
    var ip = $('form#search .search').val();
    //update search page
    window.location = "tools/search/" + ip;
    return false;
});
//from homepage usermenu
$('form#userMenuSearch').live('submit', function() {
    var ip = $('#userMenuSearch #appendedInputButton').val();
    window.location = "tools/search/" + ip;
    return false;
});
//search export
$('a#exportSearch').live('click', function() {
    var searchTerm = $('form#search .search').val();
    $("div.dl").remove();                                                //remove old innerDiv
    $('div.exportDIVSearch').append("<div style='display:none' class='dl'><iframe src='site/tools/searchResultsExport.php?searchTerm=" + searchTerm + "'></iframe></div>");
    return false;
});


/* switches */
$('table#switchMainTable button[id^="switch-"]').live('click', function() {
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
$('form#userModSelf').live('submit', function () {
    var selfdata = $(this).serialize();
    
    $('div.userModSelfResult').hide();
    
    $.post('site/tools/userMenuSelfMod.php', selfdata, function(data) {
        $('div.userModSelfResult').html(data).fadeIn('fast').delay(2000).fadeOut('slow');
    });
    return false;
});
//    Generate random pass
$('#randomPassSelf').live('click', function () {
    var password = randomPass();
    $('input.userPass').val(password);
    $('#userRandomPass').html( password );
    return false;
});









/* @administration ---------- */

/*    save server settings */
$('#settings').submit(function() {
    showSpinner();
    var settings = $(this).serialize();
    //load submit results
    $.post('site/admin/settingsEdit.php', settings, function(data) {
        $('div.settingsEdit').html(data).slideDown('fast');
        //reload after 1 second if all is ok!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    });
    return false;
});


/*    Edit users
***************************/
//open form
$('.editUser').click(function () {
    showSpinner();
    var id     = $(this).attr('data-userid');
    var action = $(this).attr('data-action');
    
    $.post('site/admin/userModPrint.php',{id:id, action:action}, function(data) {
        $('div.popup_w700').html(data);
        showPopup('popup_w700');
        hideSpinner();
    });
    return false;
});
//submit form
$('#editUserSubmit').live('click', function () {
    showSpinner();
    var loginData = $('form#userMod').serialize();
    
    $.post('site/admin/userModResult.php', loginData, function(data) {
        $('div.userModResult').html(data).show();
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });
    
    return false;
});
//disable pass if domain user
$('select#domainUser').live('change', function () {
    //get details - we need Section, network and subnet bitmask
    var type = $(this).val();
    //we changed to domain
    if(type == "1") { $('input.userPass').attr('disabled',''); }
    else             { $('input.userPass').removeAttr('disabled'); }
});
// generate random pass
$('a#randomPass').live('click', function () {
    var password = randomPass();
    $('input.userPass').val(password);
    $(this).html( password );
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
    });
    return false;
});
//check AD settings
$('#checkAD').click(function() {
    showSpinner();
    var addata = $('form#ad').serialize();
    $.post('site/admin/manageADcheck.php', addata, function(data) {
        $('div.manageADresult').html(data).slideDown('fast'); hideSpinner();
    });
    return false;
});


/*    instructions
***********************/
$('#instructions').submit(function () {
    var instructions = $(this).serialize();
    showSpinner();
    $.post('site/admin/instructionsResult.php', instructions, function(data) {
        $('div.instructionsResult').html(data).fadeIn('fast');
        if(data.search("error") == -1)     { $('div.instructionsResult').delay(2000).fadeOut('slow'); hideSpinner(); }
        else                             { hideSpinner(); }      
    });
    return false;
});
$('#preview').click(function () {
    showSpinner();
    var instructions = $('form#instructions ').serialize();
    $.post('site/admin/instructionsPreview.php', instructions, function(data) {
        $('div.instructionsPreview').html(data).fadeIn('fast');
        hideSpinner();
    });
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
    });
});
//log files show details
$('a.openLogDetail').live('click',function() {
    var id = $(this).attr('data-logid');
    $.post('site/admin/logDetail.php', {id:id}, function(data) {
        $('div.popup_w500').html(data);
        showPopup('popup_w500');
        hideSpinner();        
    });
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
    }); 
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
    $('div.logs').load('site/admin/logClear.php', function() {
        hideSpinner();
    });
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
    });
});
//edit section result
$('#editSectionSubmit').live('click', function() {
    showSpinner();
    var sectionData = $('form#sectionEdit').serialize();
    
    $.post('site/admin/manageSectionEditResult.php', sectionData, function(data) {
        $('div.sectionEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    });

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
    });
});
//resize / split subnet
$('#resize, #split').live('click', function() {
	showSpinner();
	var action = $(this).attr('id');
	var subnetId = $(this).attr('data-subnetId');
	//dimm and show popup2
    $.post("site/admin/manageSubnet"+action+".php", {action:action, subnetId:subnetId}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        $('.popup_w700').css("z-index", "99");        //set behind popup
        hideSpinner();
    });	
	return false;
});
//save edit subnet changes
$('.editSubnetSubmit').live('click',function () {
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
    });
    return false;
});

/*    Add subnet from IPCalc result
*********************************/
$('#createSubnetFromCalc').live('click', function () {
    $('tr#selectSection').show();
});
$('select#selectSectionfromIPCalc').live('change', function () {
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
    });    
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
    });        
    return false;
});


/* Show add new VLAN on subnet add/edit on-thy-fly
***************************************************/
$('select[name=vlanId]').live('change', function() {
    var vlanId    = $(this).val();
    if(vlanId == 'Add') {
        showSpinner();            
        $.post('site/admin/manageVLANEdit.php', {action:"add", fromSubnet:"true"}, function(data) {
            $('div.popup_w400').html(data);
            showPopup('popup_w400');
            $('.popup_w700').css("z-index", "99");        //set behind popup
            hideSpinner();
        });
    }
    return false;    
});
//    Submit new VLAN on the fly
$('.vlanManagementEditFromSubnetButton').live('click', function() {
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
            var postdata2 = "sectionId="+sectionId+"&subnetId="+subnetId+"&action=add";
            $.post('site/admin/manageSubnetEdit.php', postdata2 , function(data) {
                $('div.popup_w700').html(data);
                //bring to front
                $('.popup_w700').delay(1000).css("z-index", "101");        //bring to front
                hideSpinner();
            });        
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
$('.editSwitch').live('click', function() {
    showSpinner();
    var switchId = $(this).attr('data-switchid');
    var action   = $(this).attr('data-action');
    $.post('site/admin/manageSwitchesEdit.php', {switchId:switchId, action:action}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    });
    return false;    
});
//    Edit switch result
$('#editSwitchsubmit').live('click', function() {
    showSpinner();
    var switchdata = $('form#switchManagementEdit').serialize();
    $.post('site/admin/manageSwitchesEditResult.php', switchdata, function(data) {
        $('div.switchManagementEditResult').html(data).slideDown('fast');

        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); hideSpinner();
        }
    });
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
    });
    return false;    
});
//result
$('#editVLANsubmit').live('click', function() {
    showSpinner();
    var vlandata = $('form#vlanManagementEdit').serialize();
    $.post('site/admin/manageVLANEditResult.php', vlandata, function(data) {
        $('div.vlanManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                               { hideSpinner(); }
    });
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
    });
    return false;    
});
//Edit VRF details
$('#editVRF').live('click', function() {
    showSpinner();
    var vrfdata = $('form#vrfManagementEdit').serialize();
    $.post('site/admin/manageVRFEditResult.php', vrfdata, function(data) {
        $('div.vrfManagementEditResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });
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
    });
    return false;    
});
//approve / reject
$('button.manageRequest').live('click', function() {
    showSpinner();
    var postValues = $('form.manageRequestEdit').serialize();
    var action     = $(this).attr('data-action');
    var postData   = postValues+"&action="+action;
    $.post('site/admin/manageRequestResult.php', postData, function(data) {
        $('div.manageRequestResult').html(data);
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });
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
    });
    return false;
});
// remove as line
$('table.asImport .removeSubnet').live('click', function () {
    $(this).parent('tr').remove();
    hideTooltips();
});
// add selected to db
$('form#asImport').live('submit', function () {
    showSpinner();
    //get subnets to add
    var importData = $(this).serialize();
    $.post('site/admin/ripeImportResult.php', importData, function(data) {
        $('div.ripeImportResult').html(data).slideDown('fast');
        //hide after 2 seconds
        if(data.search("error") == -1)     { $('table.asImport').delay(1000).fadeOut('fast'); hideSpinner(); }
        else                             { hideSpinner(); }
    });
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
    });
    return false;
});


/*    custom IP fields
************************************/
//load edit form
$('table.customIP button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customIPFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    });
    return false;
});
//custom IP field edit submit form
$('#editcustomSubmit').live('click', function() {
    showSpinner();
    var field = $('form#editCustomIPFields').serialize();
    $.post('site/admin/customIPFieldsEditResult.php', field, function(data) {
        $('div.customIPEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });

    return false;
});
// field ordering
$('table.customIP button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customIPFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customIPResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    });
    return false;
});


/*    custom subnet fields
************************************/
//load edit form
$('table.customSubnet button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customSubnetFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    });
    return false;
});
//custom IP field edit submit form
$('#editcustomSubnetSubmit').live('click', function() {
    showSpinner();
    var field = $('form#editCustomSubnetFields').serialize();
    $.post('site/admin/customSubnetFieldsEditResult.php', field, function(data) {
        $('div.customSubnetEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });

    return false;
});
// field ordering
$('table.customSubnet button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customSubnetFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customSubnetResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    });
    return false;
});


/*    custom VLAN fields
************************************/
//load edit form
$('table.customVLAN button[data-direction!=down]').click(function() {
    showSpinner();
    var action       = $(this).attr('data-action');
    var fieldName = $(this).attr('data-fieldname');
    $.post('site/admin/customVLANFieldsEdit.php',  {action:action, fieldName: fieldName}, function(data) {
        $('div.popup_w400').html(data);
        showPopup('popup_w400');
        hideSpinner();
    });
    return false;
});
//custom IP field edit submit form
$('#editcustomVLANSubmit').live('click', function() {
    showSpinner();
    var field = $('form#editCustomVLANFields').serialize();
    $.post('site/admin/customVLANFieldsEditResult.php', field, function(data) {
        $('div.customVLANEditResult').html(data).slideDown('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1500); }
        else                             { hideSpinner(); }
    });

    return false;
});
// field ordering
$('table.customVLAN button.down').click(function() {
    showSpinner();
    var current  = $(this).attr('data-fieldname');
    var next      = $(this).attr('data-nextfieldname');
    $.post('site/admin/customVLANFieldsOrder.php', {current:current, next:next}, function(data) {
        $('div.customVLANResult').html(data).slideDown('fast');
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1)     { setTimeout(function (){window.location.reload();}, 1000); }
        else                             { hideSpinner(); }
    });
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
    });  
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
    $('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateMySQL.php' ></iframe></div>");
    hideSpinner();
});



return false;
});