<?php

$connect = mysqli_connect('localhost:3308','root','','virtualna_nastenka');
mysqli_query($connect,"set names 'utf8'");
error_reporting(0);

$connectToMembers = mysqli_connect('localhost:3308','root','','user_manager');
mysqli_query($connectToMembers,"set names 'utf8'");
error_reporting(0);

?>