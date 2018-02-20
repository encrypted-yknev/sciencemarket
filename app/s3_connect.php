<?php

use Aws\S3\S3Client;

require 'composer/vendor/autoload.php';
$config = require('config.php');

/* S3 connection  */
   $s3 = S3Client::factory(
	array(
	'key' => $config['s3']['key'],
	'secret' => $config['s3']['secret']
	)
   ); 
 
?>
  