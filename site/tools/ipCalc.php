<?php  
/* verify that user is authenticated! */
require_once('../../functions/functions.php');
isUserAuthenticated ();
?>

<h3>IPv4v6 calculator</h3>

<!-- ipCalc form -->
<form name="ipCalc" id="ipCalc">

<!-- form table content -->
<div class="normalTable">
<table class="normalTable ipCalc">

    <!-- IP address input -->
    <tr>
        <td>IP address / mask</td>
        <td>
            <input type="text" name="cidr" size="40">
        </td>
        <td class="info">
            Please enter IP address and mask in CIDR format
        </td>
    </tr>

    <!-- Submit -->
    <tr class="th">
        <td></td>
        <td>
            <input type="submit" value="Calculate">
            <input type="button" value="Reset" class="reset">
        </td>
        <td></td>
    </tr>


</table>
</div>
<!-- end form table content -->

<!-- end ipCalc form -->
</form>


<!-- result -->
<br>
<div class="ipCalcResult"></div>