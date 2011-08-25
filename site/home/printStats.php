<!-- 

OLD!

 -->

<!-- HighCharts script -->
<script type="text/javascript" src="js/Highcharts-2.1.2/js/highcharts.js"></script>

<div id="container" style="height:250px">aaa</div>


<?php

/**
 * Get the sections and subnets in array
 * First ipv4 subnets and that IPv6
 *  - level 1 is section
 *  - level 2 is subnet
 *  - level 3 is usage
 */

/* get array */

?>


<!-- create data! -->
<script type="text/javascript">

/**
 *
 * functions to draw the Highcharts graphs
 *
 *
 */

/*
color definitions
*/
var color1 = 'rgba(0,0,0,0.1)';
var color2 = 'rgba(0,255,0,0.1 )';
var color3 = 'rgba(0,0,255,0.1)';
var color4 = '#FFFF00';
var color5 = '#00FFFF';
var color6 = '#FF00FF';
var color7 = '#FFFFFF';
var color8 = '#000000';
var color9 = '';
var color0 = '';


/*
create graph
*/
$(document).ready(function() {

var chart = new Highcharts.Chart(
{
    chart: 
    {
        renderTo: 'container',
        defaultSeriesType: 'column',
        borderColor: 'rgba(64,64,64,0.1)',
        borderWidth: 2,
    },
    plotOptions: {
        series: {
            shadow: false,
            borderColor: 'rgba(64,64,64,0.4)'
        }
    },
    title: {
        text: 'Used IP addresses percentage by Subnet'  
    },
    tooltip: {
        borderWidth: 0,
        formatter: function() {
            return '<b>' + this.point.name + '</b><br>' + this.y + ' IP addresses used';
        }
    },
    legend: {
        enabled: false
    },
    credits: {
        enabled: false
    },
    xAxis: 
    {
        categories: [ '10.12.50.0/24','10.12.51.0/24','212.212.213.0/24','10.12.50.51/24','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20'],
        minPadding: 0.03,
        labels: {
            enabled: false
        }
    },
    
    yAxis:
    {
        gridLineColor: 'rgba(64,64,64,0.1)',
        labels: {
            enabled: false
        }
    },
    
    series: [{
        data: 
        [
            { name: 'Subnet 1', color: color1, y: 1}, 
            { name: 'Subnet 2', color: color1, y: 5},
            { name: 'Subnet 2', color: color1, y: 7},
            { name: 'Subnet 2', color: color2, y: 4},
            { name: 'Subnet 3', color: color2, y: 12}, 
            { name: 'Subnet 3', color: color2, y: 6},
            { name: 'Subnet 3', color: color2, y: 34},
            { name: 'Subnet 4', color: color2, y: 3},
            { name: 'Subnet 5', color: color3, y: 15}, 
            { name: 'Subnet 6', color: color3, y: 5},
            { name: 'Subnet 7', color: color3, y: 17},
            { name: 'Subnet 8', color: color3, y: 3},
            { name: 'Subnet 1', color: color1, y: 1}, 
            { name: 'Subnet 2', color: color1, y: 5},
            { name: 'Subnet 2', color: color1, y: 7},
            { name: 'Subnet 2', color: color2, y: 4},
            { name: 'Subnet 1', color: color2, y: 12}, 
            { name: 'Subnet 2', color: color2, y: 6},
            { name: 'Subnet 2', color: color2, y: 34},
            { name: 'Subnet 2', color: color2, y: 3}
        ] 
    }]
});     
});
</script>