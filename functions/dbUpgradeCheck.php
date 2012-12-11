<?php

/**
 * Check if database needs upgrade to newer version
 ****************************************************/

/* use required functions */

/* redirect */
if($settings['version'] < VERSION) { header("Location: upgrade/"); }
?>