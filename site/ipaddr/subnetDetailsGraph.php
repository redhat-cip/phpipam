<?php

/*
 * Script to print pie graph for subnet usage
 ********************************************/

$free = $CalculateSubnetDetails['freehosts_percent'];
$used = 100 - $CalculateSubnetDetails['freehosts_percent'];

?>



<!-- charts -->
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="js/flot/jquery.flot.pie.js"></script>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot/excanvas.min.js"></script><![endif]-->




<script type="text/javascript">
$(function () {
    
    var data = [
    	{ label: "Free", data: <?php print $free; ?>},
    	{ label: "Used", data: <?php print $used; ?>}
	];
	
	var options = {
    series: {
        pie: {
            show: true,
            label: {
	            show: true,
	            radius: 1
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
    colors: ['#D8D8D8', '#da4f49' ],		//free, used
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