<?php

/*
 * Script to print pie graph for subnet usage
 ********************************************/

$free = $CalculateSubnetDetails['freehosts_percent'];
$used = 100 - $CalculateSubnetDetails['freehosts_percent'];

?>

<script type="text/javascript">

var chart;

$(document).ready(function() {
	chart = new Highcharts.Chart({
	
	chart: {
         renderTo: 'pieChart',
         plotBackgroundColor: null,
         plotBorderWidth: null,
         plotShadow: false,
         backgroundColor: null,
         spacingTop: 0,
         spacingRight: 0,
         spacingLeft: 0,
         spacingBottom: 0,
         
	},
    legend: {
        enabled: false
    },
	colors: [
		'#D8D8D8',
/* 		'#AA4643' */
		'#da4f49'
	],
    credits: {
        enabled: false
    },
	title: {
		text: '',
        floating: true
	},
      tooltip: {
         formatter: function() {
            return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
         }
      },
      plotOptions: {
         pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
               enabled: true,
               distance: -30,
/*                color: 'white', */
               color: '#424242',
               formatter: function() {
                  return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
               }
            }
         }
      },
   
       series: [{
         type: 'pie',
         name: 'TYPE',
         data: [
            ['Free',   <?php print $free; ?>],
            {
               name: 'Used',    
               y: <?php print $used; ?>,
               sliced: true,
               selected: true
            }
         ]
      }]
   });
});


</script>
   