<?php
// use your credentials to connect to your mysql database
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=resume','root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);