<?php

/**
 * Script to manage languages
 ****************************************/

/* verify that user is admin */
checkAdmin();

/* get all languages */
$langs = getLanguages ();
?>


<h4><?php print _('Languages'); ?></h4>
<hr>
<?php print "<p class='muted'>"._('You can edit different language translations here')."</p>"; ?>

<!-- Add new -->
<button class="btn bnt-small lang" data-action='add' style="margin-bottom:10px;"><i class="icon-plus"></i></button> <span class="muted"><?php print _("Create new language"); ?></span>

<table class="table table-striped table-auto table-top" style="min-width:400px;">

	<!-- Language list -->
	<?php
	/* no results */
	if(sizeof($langs) == 0) { ?>
		<tr>
			<td colspan="4"><div class="alert alert-info alert-nomargin"><?php print _('No custom fields created yet'); ?></div></td>
		</tr>
	<?php } else {
		# headers
		print "<tr>";
		print "	<th>"._('Language code')."</th>";
		print "	<th>"._('Language name')."</th>";
		print "	<th>"._('Validity')."</th>";
		print "	<th>"._('Version')."</th>";
		print "	<th></th>";
		print "</tr>";
		
		# print
		foreach($langs as $lang) {
		
			# verify validity
			$valid = verifyTranslation($lang['l_code']);
			
			# check version
			if($valid) {
				$tversion = getTranslationVersion($lang['l_code']);
			}
			else {
				$tversion = "NA";
			}
			
			if($valid)  { $vPrint = "<span class='alert alert-success'>"._('Valid')."</span>"; }
			else		{ $vPrint = "<span class='alert alert-error'>"._('Invalid')."</span>"; }
		
			print "<tr>";
			print "	<td>$lang[l_code]</td>";
			print "	<td>$lang[l_name]</td>";
			print "	<td>$vPrint</td>";
			print "	<td>$tversion</td>";
			print "	<td>";
			print "	<div class='btn-group'>";
			print "		<button class='btn btn-small lang' data-action='edit' data-langid='$lang[l_id]'><i class='icon-pencil'></i></button>";
			print "		<button class='btn btn-small lang' data-action='delete' data-langid='$lang[l_id]'><i class='icon-remove'></i></button>";
			print "	</div>";
			print "	</td>";
			print "</tr>";
		}
	}
	?>


</table>

<hr>
<div class="alert alert-info alert-block alert-auto alert-absolute">
	<?php print _('Instructions'); ?>:<hr>
	<ol>
		<li><?php print _('Add translation file to directory functions/locale/ in phpipam'); ?></li>
		<li><?php print _('Create new language with same code as translation file'); ?></li>
	</ol>
</div>
