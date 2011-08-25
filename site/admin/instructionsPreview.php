<?php
/**
 *	Preview instructions
 ***************************/
?>

<div class="normalTable" style="padding: 5px;">
<?php 
/* format line breaks */
$instructions = str_replace("\n\r", "<br>", $_POST['instructions']);
print $instructions; 
?>
</div>