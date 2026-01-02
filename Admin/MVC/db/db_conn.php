<?php

$host="localhost"; // Server name

$user="root";  // Username of the DB

$pass="";  // Password if any

$dbname="pet-adoption-center"; //Database name

// create the connectiin

 

$conn= new mysqli($host,$user,$pass,$dbname);

if ($conn->connect_error)

{

    die("Connect Error: " . $conn->connect_error);
}
