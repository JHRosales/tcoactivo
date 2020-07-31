<?php
$Server = "ec2-3-17-204-123.us-east-2.compute.amazonaws.com";
$DbName = "titania_test";
// $DbName="titania_prod";
//$DbName="git_desarrollo";
$User = "postgres";
//////$Pwd="Kaka.08.RC7";
$Pwd = "buchisapa";
//	$Pwd="gtige@#20150502#.";
//$Pwd="maldini23";
$cnString = "host=$Server port=3003 dbname=$DbName user=$User password=$Pwd";
$cnString2 = "host=$Server port=3003 dbname=$DbName user=$User password=$Pwd";

if (@!pg_connect($cnString)) {
	//$this->connection = pg_connect(self::$driver2);
	$ConeccionRatania = pg_connect($cnString2) or die('La conexion fall�. Error: ' . pg_last_error());
} else {
	$ConeccionRatania = pg_connect($cnString) or die('La conexion fall�. Error: ' . pg_last_error());
}
