<?php
$con= new mysqli('localhost:3307', 'root', '', 'crudoperations');

if(!$con){
    die(mysqli_error($con));
}

?>