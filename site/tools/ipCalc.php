<?php  
/* verify that user is authenticated! */
isUserAuthenticated ();
?>

<h4>IPv4v6 calculator</h4>
<hr>

<!-- ipCalc form -->
<form name="ipCalc" id="ipCalc">
<table class="ipCalc table">

    <!-- IP address input -->
    <tr>
        <td>IP address / mask</td>
        <td>
            <input type="text" name="cidr" size="40">
        </td>
        <td>
            <div class="alert alert-warn" style="margin-bottom:0px;">Please enter IP address and mask in CIDR format</div>
        </td>
    </tr>

    <!-- Submit -->
    <tr class="th">
        <td></td>
        <td>
            <input type="submit" class="btn btn-small" value="Calculate">
            <input type="button" class="btn btn-small" value="Reset" class="reset">
        </td>
        <td></td>
    </tr>


</table>
</form>


<!-- result -->
<br>
<div class="ipCalcResult"></div>