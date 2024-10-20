<?php

//permite CORS continut
header('Access-Control-Allow-Origin: http://tid4kg122.ro');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

//start aplicatie tid4k
header("location: qr_code.php");
?>
