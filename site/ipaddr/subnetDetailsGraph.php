<?php

/*
 * Script to print pie graph for subnet usage
 ********************************************/
 
# if slaves reset IP addresses!
$slaves = getAllSlaveSubnetsBySubnetId ($subnetId); 

# slaves details are provided with ipaddressprintslaves script
if(sizeof($slaves)>0) {
	$ipaddresses   = getIpAddressesBySubnetIdSlavesSort ($subnetId);
}

# get offline, reserved and DHCP
$out['offline']  = 0;
$out['online']   = 0;
$out['reserved'] = 0;
$out['dhcp']     = 0;
	
foreach($ipaddresses as $ip) {
	if		($ip['state'] == "0")	{ $out['offline']++; 	}
	else if ($ip['state'] == "1")	{ $out['online']++; 	}
	else if ($ip['state'] == "2")	{ $out['reserved']++; 	}
	else if ($ip['state'] == "3")	{ $out['dhcp']++; 		}
}
# get details
$details = calculateSubnetDetailsNew ( $SubnetDetails['subnet'], $SubnetDetails['mask'], $out['online'], $out['offline'], $out['reserved'], $out['dhcp'] );	
?>

<!-- charts -->
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.pie.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->


<script type="text/javascript">
$(function () {
    
    var data = [
    	<?php
     	if($details['freehosts_percent']>0) 
    	print "{ label: '"._('Free')."',     data: $details[freehosts_percent], color: '#D8D8D8' }, ";		# free hosts
    	
    	if($details['online_percent']>0)
    	print "{ label: '"._('Active')."',   data: $details[online_percent],    color: '#A9C9A4' }, ";		# active hosts
    	
    	if($details['offline_percent']>0) 
    	print "{ label: '"._('Offline')."',  data: $details[offline_percent],   color: '#F59C99'  },";		# offline hosts	    	

    	if($details['reserved_percent']>0) 
    	print "{ label: '"._('Reserved')."', data: $details[reserved_percent],  color: '#9AC0CD' },";			# reserved hosts	     	

    	if($details['dhcp_percent']>0) 
    	print "{ label: '"._('DHCP')."',     data: $details[dhcp_percent],      color: '#a9a9a9' },";		# dhcp hosts	 
    	
    	?>
    

	];
	
	var options = {
    series: {
        pie: {
            show: true,
            label: {
	            show: true,
	            radius: 1,
	            threshold: 0.01	//hide < 1%
            },
            background: {
	            color: 'red'
            },
            radius: 0.9,
            stroke: {
	            color: '#fff',
	            width: 2
            },
            offset: {
	            left: 0
            }
            
        }
    },
    legend: {
	    show: true,
	    backgroundColor: ""
    },
	grid: {
		hoverable: false,
	  	clickable: true
	},
    highlightColor: '#AA4643',
    colors: ['#D8D8D8', '#a9a9a9', '#da4f49', '#08c', '#5bb75b' ],		//free, active, offline, reserved, dhcp
    grid: {
	        show: true,
	        aboveData: false,
	        color: "#666",
	        backgroundColor: "white",
    		borderWidth: 0,
    		borderColor: null,
    		minBorderMargin: null,
    		clickable: true,
    		hoverable: true,
    		autoHighlight: true,
    		mouseActiveRadius: 3
    		}
    };
    
    $.plot($("#pieChart"), data, options);
});
</script>