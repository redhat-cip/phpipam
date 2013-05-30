<?php

/**
 *
 * Script to calculate IP subnetting
 *
 */

/* include required scripts */
require_once('../../functions/functions.php');

# check referer and requested with
CheckReferrer();

# get requested IP addresses
$cidr = $_POST['cidr'];

# verify input CIDR
$errors = verifyCidr ($cidr,0);

# die on errors
if (sizeof($errors) != 0) { die('<div class="alert alert-error alert-absolute">'._('Invalid input').': '.  $errors[0] .'</div>'); }

/* calculate results */
$ipCalcResults = calculateIpCalcResult ($cidr);
?>

<hr>
<h4><?php print _('Subnetting details for');?> <?php print $cidr; ?>:</h4>

<!-- IPcalc result table -->
<table class="ipCalcResult table table-striped table-condensed">
    
    <!-- IP details -->
    <?php
    $m = 0;		//needed for add subnet mapping
    foreach ($ipCalcResults as $key=>$line) 
    {
        print '<tr>';
        print ' <td>'._("$key").'</td>';
        print ' <td id="sub'. $m .'">'. $line .'</td>';
        print '</tr>';
        
        $m++;
    }
    
    ?>
    
    <!-- add subnet button -->
    <tr>
    	<td></td>
    	<td style="padding-top:10px">
    		<button id="createSubnetFromCalc" class="btn btn-small"><i class="icon-plus"></i> <?php print _('Create subnet from result');?></button>
    	</td>
    </tr>
    
    <!-- select section -->
	<tr id="selectSection" style="display:none">
		<td style="text-align:right"><?php print _('Select Section');?>:</td>
		<td>
		
		<select name="selectSectionfromIPCalc" id="selectSectionfromIPCalc">
			<option value=""><?php print _('Please select');?>:</option>
		<?php
			//get all sections
			$sections = fetchSections ();
			
			foreach($sections as $section) {
				print '<option value="'. $section['id'] .'">'. $section['name'] .'</option>';
			}
		?>
		</select>
		
		</td>
	</tr>

</table>
</div>