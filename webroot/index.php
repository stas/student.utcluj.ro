<?php
/* Short and sweet */

/* - load limonade */
require_once '../app/lib/limonade.php';
/* - config */
if(file_exists('../app/config.php'))
   require_once '../app/config.php';
else
    die('No config.php found.');
/* - vendors */
require_once '../app/lib/vendors.php';
/* - bootstrap */
require_once '../app/bootstrap.php';

?>