/*	change buttons on hover (add, ...)
****************************************/


//hide subnet list
$('table.subnets tr th').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$('img.rewind').attr("src","css/images/rewindHover.png");
	else
		$('img.rewind').attr("src","css/images/rewind.png");
});
//subnets img hover
/*
$('table.subnets tr[class!=th]').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover') {
		$(this).children('td').children('img').attr("src","css/images/folderClosed.png");
	}
	else {	
		$(this).children('td').children('img').attr("src","css/images/folderClosed.png");
	}
});
*/
$('table.ipaddress_subnet tr.add_ipaddress').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover') {
		$('img.add_ipaddress').attr("src","css/images/addHover.png");
		$('img.add_ipaddress_lock').attr("src","css/images/lockHover.png");
	}
	else {
		$('img.add_ipaddress').attr("src","css/images/add.png");
		$('img.add_ipaddress_lock').attr("src","css/images/lock.png");
	}
});
$('table.ipaddress_subnet tr.request_ipaddress').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover') {
		$('img.request_ipaddress').attr("src","css/images/addHover.png");
	}
	else {
		$('img.request_ipaddress').attr("src","css/images/add.png");
	}
});
$('table.ipaddress_subnet tr.edit_subnet').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover') {
		$('img.edit_subnet').attr("src","css/images/editHover.png");
	}
	else {
		$('img.edit_subnet').attr("src","css/images/edit.png");
	}
});
$('table.ipaddress_subnet tr.csvImport').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$('img.csvImport').attr("src","css/images/uploadHover.png");
	else
		$('img.csvImport').attr("src","css/images/upload.png");
});
$('table.ipaddress_subnet tr.csvExport').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$('img.csvExport').attr("src","css/images/downloadHover.png");
	else
		$('img.csvExport').attr("src","css/images/download.png");
});

$('table.manageSubnets tr.info').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$('img.Add').attr("src","css/images/addHover.png");
	else
		$('img.Add').attr("src","css/images/add.png");
});
$('table.manageSection tr td.info').live("mouseover mouseout", function(event) {
	if (event.type == 'mouseover')
		$('img.sectionAdd').attr("src","css/images/addHover.png");
	else
		$('img.sectionAdd').attr("src","css/images/add.png");
});
