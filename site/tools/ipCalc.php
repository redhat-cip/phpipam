<?php  
/* verify that user is authenticated! */
isUserAuthenticated ();
?>

<h4><?php print _('IPv4v6 calculator');?></h4>
<hr>

<!-- ipCalc form -->
<form name="ipCalc" id="ipCalc">
<table class="ipCalc table">

    <!-- IP address input -->
    <tr>
        <td><?php print _('IP address');?> / <?php print _('mask');?></td>
        <td>
            <input type="text" name="cidr" size="40">
        </td>
        <td>
            <div class="alert alert-warn" style="margin-bottom:0px;"><?php print _('Please enter IP address and mask in CIDR format');?></div>
        </td>
    </tr>

    <!-- Submit -->
    <tr class="th">
        <td></td>
        <td>
        	<div class="btn-group">
            	<button type="submit" class="btn btn-small"><i class="icon-gray icon-ok"></i> <?php print _('Calculate');?></button>
				<input type="button" class="btn btn-small reset" value="<?php print _('Reset');?>">
        	</div>
        </td>
        <td></td>
    </tr>


</table>
</form>


<!-- result -->
<br>
<div class="ipCalcResult"></div>