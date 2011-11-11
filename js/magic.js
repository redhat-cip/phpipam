/**
 *
 * Javascript / jQuery functions
 *
 *
 */

$(document).ready(function () {


/* hide error div if jquery loads ok
*********************************************/
$('div.jqueryError').hide();
$('div.loading').hide();

/*	close error div
*******************************/
$('div.error').live('click', function() {
	$(this).fadeOut();
});

/*	set reload duration after successfull edits!
**************************************************/
var reloadTimeout = 1000;


/***************************************************************
		all page functions
***************************************************************/

/*	Check and redirect
************************************/
function checkRedirection(data) {
	if(data.search('<a href="login">') != -1) {
		window.location = "login";
	}
}

/*	Load homepage 
*********************/
function loadHome() {
    showSpinner();
    $('td#subnets').hide();
    $('div.content').hide().load('site/home/home.php', function () {
        hideSpinner();
    }).fadeIn('fast');
}

/*	load subnets
********************/
function loadSubnets(section) {
    //show loading spinner
    showSpinner();
    
	//slide down slave subnets
	var hash = document.location.toString();
	var slaveId = hash.split('|')[1];
    
    //show subnets td if hidden
    $('td#subnets').slideDown();
	
	$.post('site/subnets.php', {section:section, slaveId:slaveId}, function(data) {
		$('div.subnets').html(data).slideDown('fast');
		hideSpinner();
		//redirect to home if "please login first"
		checkRedirection(data);
	});
	//set text in content div
	$('div.content').html('<h4>Please select Subnet from left menu!</h4>');
	
	//hide Spinner
	hideSpinner();
}

/*	load ip addresses
********************/
function loadipaddresses(subnetId, section) {
	$('div.content').fadeIn('fast');
	showSpinner();
	
	$.post('site/ipAddressPrint.php', {subnetId:subnetId}, function(data) {
		$('div.content').html(data);
		hideSpinner();
		checkRedirection(data);
	});
}


/*	load slave ip addresses
****************************/
function loadSlaveIPAddresses(subnetId) {
	showSpinner();
	
	$.post('site/ipAddressSlavesPrint.php', {subnetId:subnetId}, function(data) {
		$('div.content').html(data).fadeIn('fast');
		hideSpinner();
		checkRedirection(data);
	});
}


/*	modifyipaddress - load form
********************************/
function modifyipaddress(action,id,$subnetId) {
    //format posted values
	postdata = "action=" + action + "&id=" + id + "&subnetId=" + $subnetId;
	
	$.post('site/modifyIpAddress.php', postdata, function(data) {
		$('div.addnew_check').slideDown('fast');
		$('div.addnew').removeClass('mail').html(data).slideDown('fast');
		$('.tooltipTop').hide();
	});	

}

/*	resolve DNS name
****************************/
$('img.refreshHostname').live('click', function() {
	showSpinner();
	var ipaddress = $('input[name=ip_addr]').val();
	$.post('site/tools/resolveDNS.php', {ipaddress:ipaddress}, function(data) {
		if(data.length != 0) {
			$('input[name=dns_name]').val(data);
		}
		hideSpinner();
	});
});

/*	modifyipaddress - check input
**********************************/
function modifyipaddresscheck() {
	//show spinner
	showSpinner();
	//get active section
	section = $('table.newSections li.active').attr('id');
	//get form values + add subnet
	postdata = $('form.editipaddress').serialize() + "&section=" + section;
	
	//we need subnetId to reload after successfull edit / add / delete
	subnetId = $('table.subnets tr.selected').attr('id');

	$.post('site/modifyIpAddressCheck.php', postdata, function(data) {
		$('div.addnew_check').html(data);
		$('div.addnew_check').slideDown('fast');
		//hideSpinner
		hideSpinner();
		//reload after 2 seconds if all is ok!
		if(data.search("error") == -1) {
			setTimeout( function (){
			     loadipaddresses(subnetId); 
					parameter = null;
			     }, reloadTimeout);
		}
	});	
}

/*	load CSV import
********************/
function loadCSVImport() {
	showSpinner();
	
	$.post('site/admin/CSVimport.php', function(data) {
		$('div.ipaddresses_overlay').html(data);
		hideSpinner();
	});
}



/*	load Subnets, then load ip addresses in selected subnet 
************************************************************/
function hashLoadSubnets (section, subpage) {
    loadSubnets(section);
    setTimeout(function (){loadipaddresses(subpage, section); parameter = null;}, 100);
    setTimeout(function (){addActiveSubnetClass(subpage); parameter = null;}, 100);
}
function hashLoadAdmin   (section, subpage) {
    loadSubnets(section);
    setTimeout(function (){loadAdminSubpage(subpage); parameter = null;}, 100);
    setTimeout(function (){addActiveAdminClass(subpage); parameter = null;}, 100);
}

/*	set active subnet on hash-load 
*************************************/
function addActiveSubnetClass (subpage) {
    $('table.subnets tr.' + subpage).addClass('selected');
}
function addActiveAdminClass (subpage) {
    $('table.admin tr.' + subpage).addClass('selected');
}

/*	Load admin subpage 
************************/
function loadAdminSubpage(subpage) {

    //show subnets td if hidden
    $('td#subnets').slideDown();

    showSpinner();
    $('div.content').load('site/admin/' + subpage + '.php', function(data) {
        hideSpinner();
        checkRedirection(data);
    }).fadeIn('fast');
}

/*	function to reload sections
********************************/
function reloadSections() {
    $.post('site/sections.php', function(data) {
        $('div.sections').html(data).fadeIn('fast');
    });
}

/*	function to show all subnets 
	in selected section in admin page
***************************************/
function showManageSubnetsBody (sectionId) {
    //hide all
    $('table.manageSubnets tbody').hide();
    $('div.manageSubnetEdit').hide();
    //show all belonging to selected class
    $('table.manageSubnets tbody.'+ sectionId ).show('fast');
    //check redirection
    checkRedirection(data);
}

/*	function to load admin subpage
	and body after small delay
***************************************/
function showManageSubnetsPageAndBody (subpage, sectionId) {
    loadAdminSubpage(subpage);
    setTimeout(function (){showManageSubnetsBody(sectionId); parameter = null;}, 50);
    hideSpinner();
}

/*	load usermod subpage - if edit / delete successfull!
*********************************************************/
function loadUserModSubpage() {
    showSpinner();
    $('div.content').load('site/admin/userMod.php', function () {
        hideSpinner();
    }).fadeIn('fast');
}

/*	reload IP requests if accepted by admin!
*********************************************************/
function loadAdminIPRequestSubpage () {
	hashLoadAdmin("Administration", "manageRequests");
}

/*	generate random password
******************************/
function randomPass() {
    chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    pass = "";
    for(x=0; x<10; x++) {
        i = Math.floor(Math.random() * 62);
        pass += chars.charAt(i);
    }
    return pass;
}

/* Load Tools menu left 
**************************/
function loadToolsMenu (subPage) {
    $('div.subnets').load('site/tools/toolsMenu.php').slideDown('fast', function () {
        $('table.menu tr').removeClass('selected');
        $('table.menu a#' + subPage).closest('tr').addClass('selected');
    });
}

/* Load Tools subpage
**************************/
function loadToolsSubpage (subPage) {
    showSpinner();
    $('div.content').load('site/tools/' + subPage + '.php').slideDown('fast', function () {
        $('table.menu tr').removeClass('selected');
        $('table.menu a#' + subPage).closest('tr').addClass('selected');
        hideSpinner();
    });
}

/* Load both tools ans subpage 
*********************************/
function loadToolsMenuAndSubpage (subPage) {
	//show subnets td if hidden
    $('td#subnets').slideDown();
    
    $('table.menu tr').removeClass('selected');
    loadToolsMenu (subPage);
    setTimeout(function (){loadToolsSubpage (subPage); parameter = null;}, 100);  
}

/*	load search Page and post searchTerm
********************************************/
function loadSearchPage(searchTerm) {

	//set href
	document.location.href = "#tools|search";

	showSpinner();
	
	//show subnets td if hidden
    $('td#subnets').slideDown();
	//load tools menu on right
	loadToolsMenu ('search');

    $.post('site/tools/search.php', searchTerm, function(data) {
        $('div.content').html(data).fadeIn('fast');
        hideSpinner();
    });
}

/*	update search Page
************************/
function updateSearchPage(searchTerm) {

	showSpinner();

    $.post('site/tools/search.php', searchTerm, function(data) {
        $('div.content').html(data).fadeIn('fast');
        hideSpinner();
    });
}


/*	loading spinner functions
*******************************/
function showSpinner() {
    $('div.loading').show();
}
function hideSpinner() {
    $('div.loading').fadeOut('fast');
}

/*
general table style
*****************************/
//normalTable hover
$('table.normalTable tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$(this).addClass('hover');
	else
		$(this).removeClass('hover');	
});




/***************************************************************
		load hash-requested site
***************************************************************/
var hash = document.location.toString();
if (hash.match('#')) 
{
	var section = hash.split('#')[1];
	var subpage = section.split('|')[1];
	var section2 = section.split('|')[0];
	
	//if subpage exists we must use section without |
	if (subpage) {
		//var section2 = section.split('|')[0];
		//Add active class to selected section and subnet!
	   $('table.newSections li[section="' + section2 + '"]').closest('li').addClass('active');
	}
	else {
	   $('table.newSections li[section="' + section + '"]').closest('li').addClass('active');
	}
	
	//do nothig if login
	if (hash.indexOf("login") != -1) {
	}
	//load home if only # -> empty section
	else if (section.length === 0) {
        loadHome();
	}
	//load tools
	else if (section.indexOf("tools") != -1) {
        if(subpage)
            loadToolsMenuAndSubpage (subpage);
        else 
            loadToolsMenu(section);
    }
	//load admin
	else if ( section.indexOf("Administration") != -1) {
        if(subpage)
            hashLoadAdmin(section2, subpage);
        else 
            loadSubnets(section);
	}
	//load subnets
	else {
	   if (subpage)
	       hashLoadSubnets(section2, subpage);
	   else
	       loadSubnets(section);
	}
}
else 
    loadHome();




/****************************************************************
		section menu 
***************************************************************/
$('table.newSections ul li, table.newSections td#Administration, table.newSections td#instructions').live('click', function () {
	//get requested location - section
	sectionName = $(this).attr('section');
	sectionId   = $(this).attr('id');
	
	//set href
	document.location.href = "#" + sectionName;
	
	//remove active class and add it to new clicked
	$('table.newSections ul li').removeClass('active');
	$(this).addClass('active');
	
	//if info load info
	if (sectionId == "instructions") {
		loadToolsMenuAndSubpage (sectionId);
	}
	//loadmainpage 
	else {
		loadSubnets(sectionId);
	}
});



/*	header link click
**********************/
$('div.header a').click(function () {
    loadHome();
});

/*	load admin subpage form admin hover
*****************************************/
$('div.adminMenuDropdown dd').live('click', function() {
	//get variables
	sectionName = $(this).attr('section');
	subpage = $(this).attr("id");
	//set href
	document.location.href = "#" + sectionName + "|" + subpage;
	//load proper page
	hashLoadAdmin('Administration', subpage);
});

/*	Show/hide admin hover menu
*****************************************/
$('table.newSections td#Administration').live("mouseover mouseout", function(event) {
	if(event.type == "mouseover") {
		$('div.adminMenuDropdown').stop(true,true).fadeIn('fast');
	}
	else {
		$('div.adminMenuDropdown').delay(500).fadeOut('fast');	
	}
});
$('div.adminMenuDropdown dd').live("mouseenter", function() {
	$('div.adminMenuDropdown').stop(true,true);
	$('div.adminMenuDropdown').show();
});

$('div.adminMenuDropdown').live("mouseleave", function() {
	$(this).delay(300).fadeOut('fast');
});





/***************************************************************
		subnets 
***************************************************************/
//table hover
$('table.subnets tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover') {
		$(this).addClass('hover');
		$(this).children('td').children('img').attr("src","css/images/expandHover.png");
	}
	else {
		$(this).removeClass('hover');	
		$(this).children('td').children('img').attr("src","css/images/expand.png");
	}
});

/*	click on subnets to load it into content
*********************************************/
$('table.subnets tr[class!=th] dd[class!=slavesToggle]').live("click", function () {
	//get subnet and link
	subnet = $(this).attr('id');
	link   = $(this).attr('section');
	
	//set href
	document.location.href = "#" + link;
	
	//make it bold and red!
	$('table.subnets tr').removeClass('selected');
	$(this).closest('tr').addClass('selected');
	//load ip addresses
	loadipaddresses(subnet);
});

/*	Toggle slaves!
*********************************************/
$('table.subnets tr[class!=th] dd.slavesToggle').live("click", function () {
	var masterId = $(this).attr('id');
	
	//show details in main page
	loadSlaveIPAddresses (masterId);

	//show Requested
	$('div.slaveSubnets-' + masterId).slideDown('fast');
	//hide all tooltips!
	$('div.tooltip,div.tooltipLeft,div.tooltipTop').hide();
});


/*	add new subnet form subnets table
****************************************/
$('table.subnets td.plusSubnet').live("click", function () {
    //set variables
    sectionId = $("table.newSections li.active").attr('id');
    postdata  = "sectionId=" + sectionId + "&subnetAction=Add&location=subnets";
    loadAddSectionFromSubnets(postdata);
});

/*	export Subnet and IP addresses
****************************************/
$('img.csvExport').live("click", function () {
	subnetId = $(this).attr('subnetId');

	$("div.dl").remove();	//remove old innerDiv
	$('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportSubnet.php?subnetId=" + subnetId + "'></iframe></div>");
});

/*	load CSV import form
****************************************/
$('img.csvImport').live("click", function () {
	loadCSVImport();
	//hide add ip form
	$('table.ipaddress_subnet div.addnew').slideUp('fast');
});

/*	display uploaded file
****************************************/
$('input#csvimportcheck').live('click', function () {
	showSpinner();
	
	//get filetype
	var filetype = $('span.fname').html();
	
	$.post('site/admin/CSVimportShowFile.php', { filetype : filetype }, function(data) {
		$('div.csvimportverify').html(data).slideDown('fast');
		hideSpinner();
	});
});

/*	show / hide subnets
****************************************/
$('th.hideSubnets').live('click', function () {
	$('table.content td#subnets, .tooltipLeft').fadeOut('fast');
});


/*	show / hide slave Subnets
****************************************/
$('img.structure').live('click', function () {
	var Id = $(this).attr('subnetId');
	
	//slideUp all
	$('div.slaveSubnets').not('div.slaveSubnets-' + Id).slideUp('fast');
	//show Requested
	$('div.slaveSubnets-' + Id).slideToggle('fast');
});


/*	import file script
****************************************/
$('input#csvImportNo').live('click',function () {
	$('div.csvimportverify').hide('fast');
});
$('input#csvImportYes').live('click',function () {

	showSpinner();
	
	//get filetype
	var filetype = $('span.fname').html();
	/* get active subnet ID */
	var xlsSubnetId  = $('table.subnets  tr.selected').attr('id');
	
	var postData = "subnetId=" + xlsSubnetId + "&filetype=" + filetype;

	$.post('site/admin/CSVimportSubmit.php', postData, function(data) {
		$('div.csvImportResult').html(data).slideDown('fast');
		hideSpinner();
		
		//reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            setTimeout(function (){loadipaddresses(xlsSubnetId); parameter = null;}, reloadTimeout);
        }
	});

});


/*	check result
********************/
$('form#manageSubnetEditFromSubnets').live('submit', function () {

    subnetData = $(this).serialize();
    //load results
    $.post("site/admin/manageSubnetEditResult.php", subnetData, function(data) {
        $('div.manageSubnetEditResult').html(data).slideDown('fast');

		//reload subnets after 2 seconds if all is ok!
		if(data.search("error") == -1) {
            showSpinner();
            sectionId = $("table.newSections li.active").attr('id');
            setTimeout(function (){loadSubnets(sectionId); parameter = null;}, reloadTimeout);
		}
    });
    return false;
});

/*	cancel button
********************/
$('form#manageSubnetEditFromSubnets input.cancel').live('click', function () {
    $('div.content').slideUp('fast');
});

/*	function to load addSubnet section from subnets
*****************************************************/
function loadAddSectionFromSubnets(postdata) {
	$.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
		$('div.content').html(data).slideDown('fast');
	});	
}




/***************************************************************
		ip address table 
***************************************************************/

/*	add new ip address, delete, edit
****************************************/
$('img.add_ipaddress, img.edit_ipaddress, img.delete_ipaddress, img.add_ipaddress_lock').live("click", function () {
		
	action	  = $(this).attr('class');
	id	      = $(this).attr('id');
	subnetId  = $('table.subnets tr.selected').attr('id');
    
	//load modify ip field
	modifyipaddress(action,id,subnetId);
	
	//hide edit subnet if opened
	$('div.edit_subnet').slideUp('fast');
});


/*	edit subnet
****************************************/
$('img.edit_subnet').live("click", function () {
	subnetId  = $('table.subnets tr.selected').attr('id');
	sectionId = $('table.newSections li.active').attr('id');
    subnetAction = "Edit";
    locationAction = "IPaddresses";
    //format posted values
	postdata     = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&subnetAction=" + subnetAction + "&location=" + locationAction;
	//hide addnew IP address ad if present
	$('div.addnew').slideUp('fast');
	
	$.post('site/admin/manageSubnetEdit.php', postdata, function(data) {
		$('div.edit_subnet').html(data).addClass('edit_subnet').slideDown('fast');
	});	
});

/*	show sendmail form
********************************/
$('img.mail_ipaddress').live("click", function () {
	//get IP address id
	var IPid = $(this).attr('id');
	//hide tooltip
	$('.tooltipTop').hide();
	
	//hide edit subnet if opened
	$('div.edit_subnet').slideUp('fast');
	
	$.post('site/mailNotifyIP.php', { id:IPid }, function(data) {
		$('div.addnew').html(data).addClass('mail').slideDown('fast');
	});
});

/*	send mail with IP details!
*******************************/
$('form#mailNotify').live('submit', function () {
	var mailData = $(this).serialize();
	//post to check script
	$.post('site/mailNotifyCheck.php', mailData, function(data) {
		$('div.sendmail_check').html(data).slideDown('fast');
		//hide if success!
		if(data.search("error") == -1) {
			$('div.addnew').animate({opacity: '1.0'},1500).slideUp('fast');
		}	
	});
	return false;
});
$('form#mailNotify input.cancel').live('click', function () {
	$('td.addnew').slideUp('fast');
});

/*	submit ip address change!
******************************/
$('form.editipaddress').live("submit", function () {
	modifyipaddresscheck();
	return false;
});

/*	close change field
************************/
$('input.cancel').live('click', function () {
	$('div.addnew').slideUp('fast');
});

/*	load subnet from slave table in ip addresses
*************************************************/
$('table.slaveSubnet tr[class!=th]').live('click', function() {
	var subnetId = $(this).attr('subnetId');
	//add active class to selected
	$('table.subnets td.slaveSubnets tr').removeClass('selected');
	$('table.subnets td.slaveSubnets tr#' + subnetId).addClass('selected');
	//load
	loadipaddresses(subnetId);
});



/***************************************************************
		admin section
***************************************************************/

/*	left table hover
**********************/
$('table.admin tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$(this).addClass('hover');
	else
		$(this).removeClass('hover');	
});

/*	subpage table hover
**************************/
$('table.manageSection tr[class!=th], table.manageSubnets tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$(this).addClass('hover');
	else
		$(this).removeClass('hover');	
});

/*	load admin subpage
************************/
$('table.admin tr[class!=th]').live('click', function () {
    subpage = $(this).attr("id");
    
    //set href
    link    = $(this).children('td').attr('link');
    document.location.href = "#" + link;
    
	//make it selected!
	$('table.admin tr').removeClass('selected');
	$(this).addClass('selected');
    //load appropriate subpage into content div
    loadAdminSubpage(subpage);
});

/*	save server settings
************************/
$('#settings').live('submit', function() {
	var settings = $(this).serialize();
	//load submit results
	$.post('site/admin/settingsEdit.php', settings, function(data) {
		$('div.settingsEdit').html(data).slideDown('fast');
		//reload
		setTimeout(function (){window.location.reload();}, reloadTimeout);
	});
	return false;
});

/*	add, edit, delete sections
********************************/
$('img.sectionAdd, img.sectionDelete, img.sectionEdit').live("click", function () {
	action	  = $(this).attr('class');
	id	      = $(this).attr('id');
	//load modify field
	$.post('site/admin/manageSectionEdit.php', { action:action, id:id }, function(data) {
		$('div.manageSectionEdit').html(data).slideDown('fast');
	});
});


/* section edit result
************************/
$('form.sectionEdit').live('submit', function () {
    sectionDetails = $(this).serialize();
	$.post('site/admin/manageSectionEditResult.php', sectionDetails, function(data) {
	
		$('div.sectionEditResult').hide().html(data).slideDown('fast');
		
		//reload after 2 seconds if all is ok!
		if(data.search("error") == -1) {
            subpage = $('table.admin tr.selected').attr("id");
			setTimeout(function (){loadAdminSubpage(subpage); parameter = null;}, reloadTimeout);
			setTimeout(reloadSections, reloadTimeout);
		}
	});	
    return false;
});

/*	section edit cancel button
********************************/
$('input.cancelSectionEdit').live('click', function () {
    $('div.manageSectionEdit').slideUp('fast');
});


/*	add, edit, delete subnets inside admin
*******************************************/
$('table.manageSubnets img').live('click', function () {
    sectionId    = $(this).attr('sectionId');
    subnetId     = $(this).attr('subnetId');
    subnetAction = $(this).attr('class');
    //format posted values
	postdata     = "sectionId=" + sectionId + "&subnetId=" + subnetId + "&subnetAction=" + subnetAction;
    
    //load edit data
    $.post("site/admin/manageSubnetEdit.php", postdata, function(data) {
        $('div.manageSubnetEdit').html(data).slideDown('fast');
    });
});

/*	on thead click hide all other subnet bodies 
	and show only ones in requested section
**************************************************/
$('table.manageSubnets thead').live('click', function () {
    sectionId = $(this).attr('class');
    showManageSubnetsBody (sectionId);
});


/*	form manageSubnetEdit submit / cancel
	submit editing subnets form
*******************************************/
$('form#manageSubnetEdit input.cancel').live('click', function () {
    $('div.manageSubnetEdit').slideUp('fast');
});
$('form#manageSubnetEdit').live('submit', function () {

    subnetData = $(this).serialize();
    //load results
    $.post("site/admin/manageSubnetEditResult.php", subnetData, function(data) {
        $('div.manageSubnetEditResult').html(data).slideDown('fast');

		//reload after 2 seconds if all is ok!
		if(data.search("error") == -1) {
		    showSpinner();
            subpage = $('table.admin tr.selected').attr("id");
            //reload IP address list if request came from there
            if(subnetData.search("IPaddresses") != -1) {
                subnetId = $('table.subnets tr.selected').attr('id');
                setTimeout(function (){loadipaddresses(subnetId); parameter = null;}, reloadTimeout);
            }
            else {
                //reload Admin Subpage and subnets body
				setTimeout(function (){showManageSubnetsPageAndBody(subpage, sectionId); parameter = null;}, reloadTimeout);
			}
		}
    });
    return false;
});


/*
add, edit, delete users
************************/

/*	Add new user form
**********************/
$('form#userMod').live('submit', function () {

    loginData = $(this).serialize();
    
    $.post('site/admin/userModResult.php', loginData, function(data) {
        $('div.userModResult').html(data).slideDown('fast');
    
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            setTimeout(loadUserModSubpage, reloadTimeout);
        }
    });
    
    return false;
});

/* generate random pass
*************************/
$('a#randomPass').live('click', function () {
    password = randomPass();
    $('input.userPass').val(password);
    $(this).html( password );
});

/*	Edit existing user
***************************/
$('table.userPrint td.edit img').live('click', function () {
    id     = $(this).attr('id');
    action = $(this).attr('class');
    //format posted values
	postdata     = "id=" + id + "&action=" + action;
	
	$.post('site/admin/userModPrint.php', postdata, function(data) {
	   $('div.userEditLoad').html(data).slideDown('fast');
	});

});


/*	display log files
************************/
$('form#logs').live('change', function () {
    showSpinner();
    var logSelection = $('form#logs').serialize();
    $.post('site/admin/logResult.php', logSelection, function(data) {
        $('table.logs').html(data);
        hideSpinner();
    });
});
/*	log files page change
**************************/
$('form#logs input').live('click', function() {
    showSpinner();
    /* get severities */
    var logSelection = $('form#logs').serialize();
    /* get first or last id based on direction */
    var direction = $(this).attr('class');
    /* get Id */
    if (direction == "next") {
        var lastId = $('table.logs tr:last').attr('id');
    }
    else {
        var lastId = $('table.logs tr:nth-child(2)').attr('id');    	
    }
    
    /* set complete post */
    postData = logSelection + "&direction=" + direction + "&lastId=" + lastId;

	/* show logs */
    $.post('site/admin/logResult.php', postData, function(data1) {
        $('div.logs').html(data1);
        hideSpinner();
    });    
});

/*	Hover over table
********************/
$('table.logs tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$(this).addClass('hover');
	else
		$(this).removeClass('hover');	
});


/*	XLS export
***********************/
/* get cert */
$('input#XLSdump').live('click', function () {
	$("div.dl").remove();	//remove old innerDiv
	$('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateXLS.php'></iframe></div>");
});

/*	MySQL export
***********************/
/* get cert */
$('input#MySQLdump').live('click', function () {
	$("div.dl").remove();	//remove old innerDiv
	$('div.exportDIV').append("<div style='display:none' class='dl'><iframe src='site/admin/exportGenerateMySQL.php' ></iframe></div>");
});


/*	instructions
***********************/
$('#instructions').live('submit', function () {
	var instructions = $(this).serialize();
	
    showSpinner();

    $.post('site/admin/instructionsResult.php', instructions, function(data) {
        $('div.instructionsResult').html(data).fadeIn('fast');
        hideSpinner();
        
        setTimeout(function (){loadAdminSubpage ("instructions"); parameter = null;}, reloadTimeout);
    });
    return false;
});
$('#preview').live('click', function () {
	var instructions = $('form#instructions ').serialize();
	
    showSpinner();

    $.post('site/admin/instructionsPreview.php', instructions, function(data) {
        $('div.instructionsPreview').html(data).fadeIn('fast');
        hideSpinner();
    });
    return false;
});

/*	edit IP request
***********************/
$('table.requestedIPaddresses img').live('click', function() {
	
	showSpinner();
	
	var requestId = $(this).attr('requestId');
	
    $.post('site/admin/manageRequestEdit.php', { requestId: requestId }, function(data) {
        $('div.manageRequestEdit').html(data).fadeIn('fast');
        hideSpinner();
    });
    return false;	
});

/*	Confirm IP request
********************************/
$('form.manageRequestEdit').live('submit', function() {

	showSpinner();
	
	postValues = $(this).serialize();
	postData   = postValues + "&action=confirmed";

	$.post('site/admin/manageRequestResult.php', postData, function(data) {
		$('div.manageRequestResult').html(data);
		hideSpinner();
		
		//reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            setTimeout(loadAdminIPRequestSubpage, reloadTimeout);
        }
	});
	
	return false;
});

/*	Reject IP request
********************************/
$('form.manageRequestEdit .reject').live('click', function() {

	showSpinner();
	
	postValues = $('form.manageRequestEdit').serialize();
	postData   = postValues + "&action=reject";

	$.post('site/admin/manageRequestResult.php', postData, function(data) {
		$('div.manageRequestResult').html(data);
		hideSpinner();

		//reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            setTimeout(loadAdminIPRequestSubpage, reloadTimeout);
        }
	});
	return false;
});




/***************************************************************
		tools section
***************************************************************/

/*	hover topNav
**********************/
$('ul.topNav li').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$(this).addClass('hover');
	else
		$(this).removeClass('hover');	
});

/* load tools subpage on topnav click
**************************************/
$('ul.topNav li[id!=search], ul.subNav li').live('click', function () {

    //show subnets td if hidden
    $('td#subnets').slideDown();
    
    //set href
    var link = $(this).attr('link');
	document.location.href = "#" + link;

    subPage = $(this).attr('id');
    loadToolsMenu (subPage);
    
    $('table.newSections li').removeClass('active');
    $('table.menu tr').removeClass('selected');
    setTimeout(function (){loadToolsSubpage (subPage); parameter = null;}, 100);
});
$('table.menu a').live('click', function () {
    subPage = $(this).attr('id');
    loadToolsSubpage (subPage);  
});


/*	ipCalc form submit 
************************/
$('form#ipCalc').live('submit', function () {
    var ipCalcData = $(this).serialize();
    showSpinner();

    $.post('site/tools/ipCalcResult.php', ipCalcData, function(data) {
        $('div.ipCalcResult').html(data).fadeIn('fast');
        hideSpinner();
    });
    return false;
});

/*	ipCalc reset input
************************/
$('form#ipCalc input.reset').live('click', function () {
    $('form#ipCalc input[type="text"]').val('');
    $('div.ipCalcResult').slideUp('fast');
});

/*	Add subnet from result
***************************/
$('img.createSubnetFromCalc').live('click', function () {
	$('tr#selectSection').show();
});
$('select#selectSectionfromIPCalc').live('change', function () {
	//get details - we need Section, network and subnet bitmask
	var sectionId = $(this).val();
	var subnet	  = $('table.ipCalcResult td#sub2').html();
	var bitmask	  = $('table.ipCalcResult td#sub4').html();
	var postdata  = "sectionId=" + sectionId + "&subnet=" + subnet + "&bitmask=" + bitmask + "&subnetAction=Add&location=ipcalc";
	//make section active
	$('table.newSections ul#sections li#' + sectionId ).addClass('active');
	//now load the section and add subnet
	loadSubnets(sectionId);
	//load add Subnet
	$.post('site/admin/manageSubnetEdit.php', postdata , function(data) {
		$('div.content').html(data).slideDown('fast');
		hideSpinner();
	});	
});

/*	VLAN link to subnets
************************/
$('table.vlans tr.vlanLink').live('click', function () {
	var sectionId = $(this).attr('sectionId'); 
	var subnetId  = $(this).attr('subnetId');
    var link      = $(this).attr('link');
	
	//set href
	document.location.href = "#" + link;
	
	//set active section
	$('table.newSections li#' + sectionId ).addClass('active');
	
	hashLoadSubnets (sectionId, subnetId);
});

/*	Switches link to subnets
****************************/
$('table.switches tr[class!=th]').live('click', function () {
	var sectionId = $(this).attr('sectionId'); 
	var subnetId  = $(this).attr('subnetId');
	var link      = $(this).attr('link');
	var id	      = $(this).attr('id');			//ip address id
	
	//set href
	document.location.href = "#" + link;
	
	//set active section
	$('table.newSections li#' + sectionId ).addClass('active');
	
	hashLoadSubnets (sectionId, subnetId);
	//load modify ip field after 1 sec when subnets should be loaded!
	setTimeout(function (){modifyipaddress("edit",id,subnetId); parameter = null;}, 1000);
});

/*	Hosts list filter form
****************************/
$('#hosts').live('submit', function() {
	var hostname = $('input#hostsFilter').val();
	showSpinner();
	
    $.post('site/tools/hostsResult.php', {hostname:hostname}, function(data) {
        $('div.hostsFilterResult').html(data).fadeIn('fast');
        hideSpinner();
    });	
    
	return false;
});

/*	Hosts link to ipaddresses
****************************/
$('table.hosts tr[class!=th]').live('click', function () {
	var sectionId = $(this).attr('sectionId'); 
	var subnetId  = $(this).attr('subnetId');
	var link      = $(this).attr('link');
	var id	      = $(this).attr('id');			//ip address id
	
	//set href
	document.location.href = "#" + link;
	
	//set active section
	$('table.newSections li#' + sectionId ).addClass('active');
	
	hashLoadSubnets (sectionId, subnetId);
	//load modify ip field after 1 sec when subnets should be loaded!
	setTimeout(function (){modifyipaddress("edit",id,subnetId); parameter = null;}, 1000);
});

/*	user selfchange form submit
********************************/
$('form#userModSelf').live('submit', function () {
    var selfdata = $(this).serialize();
    
    $('div.userModSelfResult').hide();
    
    $.post('site/tools/userMenuSelfMod.php', selfdata, function(data) {
        $('div.userModSelfResult').html(data).fadeIn('fast');
        
        //reload after 2 seconds if succeeded!
        if(data.search("error") == -1) {
            setTimeout(function (){loadToolsSubpage ("userMenu"); parameter = null;}, reloadTimeout); 
        }
    });
    return false;
});

/*	Generate random pass 
***************************/
$('a#randomPassSelf').live('click', function () {
    password = randomPass();
    $('input.userPass').val(password);
    $(this).html( password );
    return false;
});


/*	search form submit
***********************/




/***************************************************************
		homepage section
***************************************************************/

/*	usermenu show/hide
**************************/
$("ul.topNav li#userMenu").hover(function () {
	$(this).parent().find("ul.subNav").slideDown('fast').show();
	$(this).css('color: white');

	$(this).parent().hover(function () {
	}, function()
	{
		$(this).parent().find("ul.subNav").slideUp('fast');
		$(this).stop(); 
	});
});

/*	search - empty on click
***********************************/
$('form#userMenuSearch input[type=text]').live('click', function () {
	$(this).val('');
});

/*	search - submit userMenu form (top right)
**********************************************/
$('form#userMenuSearch').live('submit', function () {
	searchTerm = $(this).serialize();
    loadSearchPage(searchTerm);
    return false;
});

/*	load admin page from dashboard buttons
********************************************/
$('table.homeStats td.HomeManage input').live('click', function () {
	
	section2	= 'Administration';
	var subpage =  $(this).attr('name');
	
	hashLoadAdmin(section2, subpage);
});

/*	load tools from dashboard buttons
*****************************************/
$('table.homeStats td.HomeTools input').live('click', function () {
	var subpage =  $(this).attr('name');
	loadToolsMenuAndSubpage (subpage);
});

/*	load searchpage and post search request
*********************************************/
$('form#homeIPSearch').live('submit', function () {
	var searchTerm = $(this).serialize();
	//load search page - leftmenu + page
	loadSearchPage(searchTerm);
	return false;
});

/*	search form from search page
*********************************************/
$('form#search').live('submit', function () {
	var searchTerm = $(this).serialize();
	//update search page
	updateSearchPage(searchTerm);
	return false;
});

/*	open IP addressing gude from home
*********************************************/
$('table.homeStats a.instructions, table.homeStats img.instructions').live('click', function () {
	loadToolsMenuAndSubpage ("instructions");
});

/*	open IP requests from home page
*********************************************/
$('#adminRequestNotif').live('click', function () {
	hashLoadAdmin("Administration", "manageRequests");
});


return false;
});