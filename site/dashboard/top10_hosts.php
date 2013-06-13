<?php
/*
 * Print graph of Top IPv4 / IPv6 hosts by percentage
 *
 * 		Inout must be IPv4 or IPv6!
 **********************************************/

/* required functions */
require_once( dirname(__FILE__) . '/../../functions/functions.php' );
# no errors!
ini_set('display_errors', 0);

# get subnets statistic
$type = $_POST['type'];
$subnetHost = getSubnetStatsDashboard($type, 10, false);

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
					$subnet['subnet'] = transform2long($subnet['subnet']);
					$subnet['descriptionLong'] = $subnet['description'];
					# odd/even if more than 5 items
					if(sizeof($subnetHost) > 5) {
						if ($m&1) 	{ print "['|<br>$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
						else		{ print "['$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}
					}
					else {
									{ print "['$subnet[description]', $subnet[usage], '$subnet[descriptionLong] ($subnet[subnet]/$subnet[mask])'],";	}			
					}	
					# next
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
    $("#<?php print $type; ?>top10Hosts").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

            if (item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
                    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
                    
                    showTooltip(item.pageX, item.pageY,
                    			
                                data[x][2] + "<br>" + y + " hosts");
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
                fillColor: "rgba(170, 70, 67, 0.8)"
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
        shadowSize: 10,
        highlightColor: '#AA4643',
        colors: ['#AA4643' ],
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
    
    $.plot($("#<?php print $type; ?>top10Hosts"), [ data ], options);
});
</script>