<div id="login">
<form name="login" id="login">  

<div class="loginForm">
<table class="login">

	<legend>Please login</legend>
          
		<!-- username -->
		<tr>
			<th>Username</th>
            <td>
            	<input type="text" id="username" name="ipamusername" class="login"></input>
            </td>
        </tr>
            
        <!-- password -->
        <tr>
            <th>Password</th>
            <td>
                <input type="password" id="password" name="ipampassword" class="login"></input>
            </td>
        </tr>
            
        <!-- submit -->
        <tr>
            <td class="submit" colspan="2">
                <input type="submit" value="Login" class="btn btn-small pull-right"></input>
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
	<i class="icon-plus icon-gray"></i> Request new IP address
	</a>	
</div>
<?php
}
?>

</div>  