<?php

/**
 *
 * Usermenu - user can change password and email
 *
 */
 
/* verify that user is authenticated! */
isUserAuthenticated ();

/* get username */
$ipamusername = getActiveUserDetails ();


/* print hello */
print "<h4>$ipamusername[real_name], here you can change your account details:</h4>";
print "<hr><br>";

?>



<form id="userModSelf">
<table id="userModSelf" class="table table-striped table-hover ">

<!-- real name -->
<tr>
    <td>Real name</td> 
    <td>
        <input type="text" name="real_name" value="<?php print $ipamusername['real_name']; ?>">
    </td>
    <td class="info">Display name</td>
</tr>

<!-- username -->
<tr>
    <td>e-mail</td> 
    <td>
        <input type="text" name="email" value="<?php print $ipamusername['email']; ?>">
    </td>
    <td class="info">Email address</td>
</tr>

<?php
# show pass only to local users!
if($ipamusername['domainUser'] == "0") {
?>
<!-- password -->
<tr>
    <td>Password</td> 
    <td>
        <input type="password" class="userPass" name="password1">
    </td style="white-space:nowrap">   
    <td class="info">Password <button id="randomPassSelf" class="btn btn-small"><i class="icon-gray icon-random"></i> Random</button><span id="userRandomPass" style="padding-left:15px;"></span></td>
</tr>

<!-- password repeat -->
<tr>
    <td>Password (repeat)</td> 
    <td>
        <input type="password" class="userPass" name="password2">
    </td>   
    <td class="info">Re-type password</td>
</tr>
<?php } ?>

<!-- Submit and hidden values -->
<tr class="th">
    <td></td> 
    <td class="submit">
        <input type="hidden" name="userId"     value="<?php print $ipamusername['id']; ?>">
        <input type="submit" class="btn btn-small" value="Save changes">
    </td>   
    <td></td>
</tr>

</table>
</form>


<!-- result -->
<div class="userModSelfResult"></div>