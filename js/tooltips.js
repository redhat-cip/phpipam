/*	tooltips
*********************************************/

$("body").live("mouseover",function () {
	/* sections */
	$('table.newSections li,table.newSections td#instructions').tooltip({ position: "bottom center", tipClass:'tooltipBottom'});
	/* hide subnets */
	$("th.hideSubnets").tooltip({ position: "center left", tipClass:'tooltipLeft' });
	/* add new subnet */
	$("table.subnets td.plusSubnet").tooltip({position: "center right"});	
	$('table.subnets img.structure').tooltip({position: "center right"});
	$('table.subnets td.subnet').tooltip({position: "center right", tipClass:'tooltipRightSubnets'});
	/* add new IP address */
	$("table.ipaddress_subnet tr.info img").tooltip({position: "center left", tipClass:'tooltipLeft' });
	$('table.ipaddresses img').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('table.ipaddresses img.info').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('table.slaveSubnet td.lock,table.slaveSubnet td.requests').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('img.addIpAddress,img.refreshHostname').tooltip({position: "top center", tipClass:'tooltipTop' });
	/* switch management */
	$('table.switchManagement img').tooltip({position: "top center", tipClass:'tooltipTop' });
	
	/* hide all on hover */
	$('.tooltipTop, .tooltip, .tooltipBottom, tooltipLeft').mouseenter(function () { $(this).hide();});
	/* hide all on click */
	$('td.lock, table.vlans dd').live('click', function() {
		$('.tooltipTop, .tooltip, .tooltipBottom, tooltipLeft').hide();
	});

	/* VLAN table hover */
	$('table.vlans td.lock, table.vlans td.requests').tooltip({position: "top center", tipClass:'tooltipTop' });

	/* Logs */
	$('table.logs td.detailed img').tooltip({position: "center right"});
	$('span.logDirection input').tooltip({position: "top center", tipClass:'tooltipTop' });	
	
	/* admin pages */
	$('table.manageSection td img').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('table.manageSubnets td img').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('table.manageSubnets td.lock').tooltip({position: "top center", tipClass:'tooltipTop' });
	$('table.userPrint td img').tooltip({position: "top center", tipClass:'tooltipTop' });
	
	/* search, switches and devices info */
	$('table.searchTable img.info,table.hosts img.info,table.switches img.info').tooltip({position: "top center", tipClass:'tooltipTop' });
	
	/* donate */
	$('td#donate').tooltip({position: "top center", tipClass:'tooltipTopDonate' });
});

/* hide all tooltips on mouseout */
$('div.tooltip,div.tooltipLeft,div.tooltipTop').live('mouseout', function () {
	$(this).fadeOut('fast');
});

