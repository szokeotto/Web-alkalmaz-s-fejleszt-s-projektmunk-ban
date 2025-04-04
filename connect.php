<?php
//Adatbázis csatlakozás
$dbHost = 'localhost';//host
$dbUser = 'root';//user
$dbPassword = '';//password
$dbName = 'php_vizsga';//dbname

//csatlakozás vagy állj
$link = @mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName) or die('Hiba az adatbázis kapcsolatban...'.mysqli_connect_error());