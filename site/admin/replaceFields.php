<?php

/**
 *	Script to replace fields in IP address list
 ***********************************************/



/* verify that user is admin */
checkAdmin();


?>


<h4><?php print _('Search and replace fields in IP address list'); ?></h4>
<hr><br>


<form id="searchReplace">
<table class="table table-striped" style="width:auto">

<tr>
	<td><?php print _('Select field to replace'); ?>:</td>
	<td>
	<select name="field">
		<option value="description"><?php print _('Description'); ?></option>
		<option value="dns_name"><?php print _('Hostname'); ?></option>
		<option value="owner"><?php print _('Owner'); ?></option>
		<option value="mac"><?php print _('MAC address'); ?></option>
		<option value="switch"><?php print _('Switch'); ?></option>
		<option value="port"><?php print _('Port'); ?></option>
		
		<?php
		#get all custom fields!
		$myFields = getCustomIPaddrFields();
		if(sizeof($myFields) > 0) {
			foreach($myFields as $myField) {
				print '<option value="'. $myField['name'] .'"> '. $myField['name'] .'</option>';
			}
		}
		?>
		
	</select>
	</td>
</tr>

<tr>
	<td><?php print _('Select search string'); ?></td>
	<td>
		<input type="text" name="search" placeholder="<?php print _('search string'); ?>">
	</td>
</tr>

<tr>
	<td><?php print _('Select replace string'); ?></td>
	<td>
		<input type="text" name="replace" placeholder="<?php print _('replace string'); ?>">
	</td>
</tr>

<tr class="th">
	<td></td>
	<td>
		<button class="btn btn-small" id="searchReplaceSave"><i class="icon-gray icon-ok"></i> <?php print _('Replace'); ?></button>
	</td>
</tr>

</table>
</form>


<!-- result -->
<div class="searchReplaceResult"></div>