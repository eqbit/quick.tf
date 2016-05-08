<?php
ini_set('max_execution_time', 180);
require 'cache30.php';
require_once 'steamapi.php';

getBPData();
getNewBPPrices();

require 'cache_footer.php';
?>