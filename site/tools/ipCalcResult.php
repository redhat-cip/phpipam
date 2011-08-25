<?php

/**
 *
 * Script to calculate IP subnetting
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

/* check referer and requested with */
CheckReferrer();

/* get requested IP addresses */
$cidr = $_POST['cidr'];

/* verify input */
$errors = verifyCidr ($cidr,0);

if (sizeof($errors) != 0) {
    die('<div class="error">Invalid input: '.  $errors[0] .'</div>');
}

/* calculate results */
$ipCalcResults = calculateIpCalcResult ($cidr);

?>

<!-- IPcalc result table -->
<div class="normalTable">
<table class="normalTable ipCalcResult">

    <!-- title -->
    <tr class="th">
        <th colspan="2">Subnetting details for <?php print $cidr; ?></th>
    </tr>
    
    <!-- IP details -->
    <?php
    foreach ($ipCalcResults as $key=>$line) 
    {
        print '<tr>';
        
        print '<td>'. $key .'</td>';
        print '<td>'. $line .'</td>';
        
        print '</tr>';
    }
    
    ?>

</table>
</div>