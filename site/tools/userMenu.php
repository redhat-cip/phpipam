<?php

/**
 *
 * Usermenu - user can change password and email
 *
 */
 
/* include required scripts */
require_once('../../functions/functions.php');

/* verify that user is authenticated! */
isUserAuthenticated ();

/* get username */
$ipamusername = getActiveUserDetails ();


/* print hello */
print '<h3>'. $ipamusername['real_name'] .', here you can change your account details:</h3>';

?>

<div class="normalTable userModSelf">

<form id="userModSelf">

<table class="userModSelf normalTable">

<!-- real name -->
<tr>
    <td>Real name</td> 
    <td>
        <input type="text" name="real_name" value="<?php print $ipamusername['real_name']; ?>">
    </td>
    <td class="info">Enter your real name</td>
</tr>

<!-- username -->
<tr>
    <td>e-mail</td> 
    <td>
        <input type="text" name="email" value="<?php print $ipamusername['email']; ?>">
    </td>
    <td class="info">Enter your email address (mail with details will be sent after creation!)</td>
</tr>

<!-- password -->
<tr>
    <td>Password</td> 
    <td>
        <input type="password" class="userPass" name="password1">
    </td>   
    <td class="info">Users password (<a href="" id="randomPassSelf">click to generate random!</a>)</td>
</tr>

<!-- password repeat -->
<tr>
    <td>Password (repeat)</td> 
    <td>
        <input type="password" class="userPass" name="password2">
    </td>   
    <td class="info">Re-type password</td>
</tr>

<!-- Submit and hidden values -->
<tr class="th">
    <td></td> 
    <td class="submit">
        <input type="hidden" name="id"     value="<?php print $ipamusername['id']; ?>">
        <input type="submit" value="Edit">
    </td>   
    <td></td>
</tr>

<!-- Edit / add result -->
<tr class="th">
    <td colspan="3">
        <div class="userModSelfResult"></div>
    </td>
    <td></td>
</tr>

</table>
</form>
</div>
