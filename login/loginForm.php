<form name="login" id="login">  

<table class="login">
          
		<!-- username -->
		<tr>
			<th>Username</th>
        </tr>
        <tr id="username1" title="Enter username">
            <td>
            	<input type="text" id="username" name="ipamusername" class="login"></input>
            </td>
        </tr>
            
        <!-- password -->
        <tr>
            <th>Password</th>
        </tr>
        <tr>
            <td>
                <input type="password" id="password" name="ipampassword" class="login"></input>
            </td>
        </tr>
            
        <!-- submit -->
        <tr>
            <td class="submit">
                <input type="submit" value="Login" class="submit"></input>
            </td>
        </tr>

		<?php
		/* require functions */
		require_once('../functions/loginFunctions.php'); 
		/* get all site details */
		$settings = getAllSettings();
        
        /* show request module if enabled in config file */
        if($settings['enableIPrequests'] == 1) {
        	print '<tr>' . "\n";
            print '	<td class="requestIP">' . "\n";
            print '		<a href="#requestIP" class="requestIP">' . "\n";
			print '		<div style="float:right;margin-left:3px">Request new IP address' . "\n";
			print '		</div>' . "\n";
			print '		<img src="../css/images/add.png" style="float:right"> ' . "\n";
			print '		</a>' . "\n";
            print '	</td>' . "\n";
        	print '</tr>' . "\n";
        }
        ?>
                   
</table>

</form>    
