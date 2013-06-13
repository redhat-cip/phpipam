<?php
/*
 * Print graph of Top IPv4 hosts by percentage
 **********************************************/

/* required functions */
require_once( dirname(__FILE__) . '/../../functions/functions.php' );
# no errors!
ini_set('display_errors', 0);

$type = "IPv4";

# get subnets statistic
$subnetHost = getSubnetStatsDashboard($type, 10, true);

/* detect duplicates */
$unique = array();	
$numbering = array();													
$m = 0;
foreach($subnetHost as $line) {
	# check if already in array
	if(in_array($line['description'], $unique)) {
		$numbering[$line['description']]++;
		$subnetHost[$m]['description'] = $line['description'].' #'.$numbering[$line['description']];
	}
	$unique[] = $subnetHost[$m]['description'];
	$m++;
}

# set maximum for graph
$max = $subnetHost[0]['percentage'];
$max = str_replace(",", ".", $max);

?>


<?php
# only print if some hosts exist
if(sizeof($subnetHost)>0) {
?>
<script type="text/javascript">
$(function () {
    
    var data = [
    <?php
	if(sizeof($subnetHost) > 0) {
		$m=0;
		foreach ($subnetHost as $subnet) {
			if($m < 10) {
				# verify user access
				$sp = checkSubnetPermission ($subnet['id']);
				if($sp != "0") {
					$subnet['subnet'] = long2ip($subnet['subnet']);
					$subnet['descriptionLong'] = $subnet['description'];
					
					# set percentage because of localisation
					$subnet['percentage'] = str_replace(",", ".", $subnet['percentage']);
					$subnet['percentage'] = $subnet['percentage'];
			
					# odd/even if more than 5 items
					if(sizeof($subnetHost) > 5) {
						if ($m&1) 	{ print "['|<br>$subnet[description]', $subnet[percentage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
						else		{ print "['$subnet[description]', $subnet[percentage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
					}
					else {
									{ print "['$subnet[description]', $subnet[percentage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}			
					}	
					$m++;
				}
			}
		}
	}
	?>
	];
	

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css( {
            position: 'absolute',
            display: 'none',
            top: y - 29,
            left: x,
            border: '1px solid white',
            'border-radius': '4px',
            padding: '4px',
            'font-size': '11px',
            'background-color': 'rgba(0,0,0,0.7)',
            color: 'white'
        }).appendTo("body").fadeIn(500);
    }

    var previousPoint = null;
    $("#<?php print $type; ?>top10").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                    
                    showTooltip(item.pageX, item.pageY,
                    			
                                data[x][2] + "<br>" + y + "% used");
                }
            }
            else {
                $("#tooltip").remove();
                previousPoint = null;            
            }
        
    });
	
		var options = {
        series: {
            bars: {
                show: true,
                barWidth: 0.6,
                lineWidth: 1,
                align: "center",
                fillColor: "rgba(69, 114, 167, 0.7)"
            }
        },
        xaxis: {
            mode: "categories",
            tickLength: 0,
            color: '#666',
            tickLength: 1,
            show: true
        },
        yaxis: {
        	max: <?php print $max; ?>
        },
        margin: {
	        top: 10,
	        left: 30,
	        bottom: 10,
	        right: 10
	    },
	    grid: {
		  	hoverable: true,
		  	clickable: true
	    },
	    bars: {
		    barWidth: 0.9
	    },
        legend: {
	        show: false
	    },
        shadowSize: 2,
        highlightColor: '#4572A7',
        colors: ['#4572A7' ],
        grid: {
	        show: true,
	        aboveData: false,
	        color: "#666",
	        backgroundColor: "white",
/*     margin: number or margin object */
/*     labelMargin: number */
/*     axisMargin: number */
/*     markings: array of markings or (fn: axes -> array of markings) */
    		borderWidth: 0,
    		borderColor: null,
    		minBorderMargin: null,
    		clickable: true,
    		hoverable: true,
    		autoHighlight: true,
    		mouseActiveRadius: 3
    		}
    };
    
    $.plot($("#<?php print $type; ?>top10"), [ data ], options);
});
</script>

<?php
}
else {
	print "<blockquote style='margin-top:90px;margin-left:50px;'>";
	print "<p>"._("No $type hosts configured")."</p>";
	print "<small>"._("Add some hosts to subnets to calculate usage percentage")."</small>";
	print "</blockquote>";
}
?>