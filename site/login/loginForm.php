<div id="login">
<form name="login" id="login">  

<div class="loginForm">
<table class="login">

	<legend><?php print _('Please login'); ?></legend>
          
		<!-- username -->
		<tr>
			<th><?php print _('Username'); ?></th>
            <td>
            	<input type="text" id="username" name="ipamusername" class="login" placeholder="<?php print _('Username'); ?>"></input>
            </td>
        </tr>
            
        <!-- password -->
        <tr>
            <th><?php print _('Password'); ?></th>
            <td>
                <input type="password" id="password" name="ipampassword" class="login" placeholder="<?php print _('Password'); ?>"></input>
                <?php
                // add requested var for redirect
                if(isset($_SESSION['phpipamredirect'])) {
	                print "<input type='hidden' name='phpipamredirect' id='phpipamredirect' value='$_SESSION[phpipamredirect]'>";
                }
                ?>
            </td>
        </tr>
            
        <!-- submit -->
        <tr>
            <td class="submit" colspan="2">
                <input type="submit" value="<?php print _('Login'); ?>" class="btn btn-small pull-right"></input>
            </td>
        </tr>
        
        
                   
</table>
</div>

</form> 


<?php   
/* show request module if enabled in config file */
if($settings['enableIPrequests'] == 1) {
?>
<div class="iprequest">
	<a href="request_ip/">
	<i class="icon-plus icon-gray"></i> <?php print _('Request new IP address'); ?>
	</a>	
</div>
<?php
}
?>

</div>  