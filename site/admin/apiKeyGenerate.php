<?php

/**
 * Script to disaply api edit result
 *************************************/
 
/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is admin */
checkAdmin();

/* checks */
print str_shuffle(md5(microtime()));
?>