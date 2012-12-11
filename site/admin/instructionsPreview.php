<?php
/**
 *	Preview instructions
 ***************************/
?>

<div class="normalTable" style="padding: 5px;">
<?php 
/* format line breaks */
$instructions = str_replace("\n", "<br>", $_POST['instructions']);
print "<div class='well'>";
print $instructions; 
print "</div>";
?>
</div>