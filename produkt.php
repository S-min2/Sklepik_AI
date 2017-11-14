<!DOCTYPE HTML>		
<html lang = "pl">

<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">					<!-- Lightbox -->
</head>
<body>
<?php

require "funkcje.php";
require_once "connect.php";
	
$model = $_GET['model'];
$polaczenie = new mysqli($host, $db_user, "$db_password", $db_name);

PokazProdukt($model);

function PokazProdukt($model)
	{
		global $polaczenie;
		
		
		$rezultat1 = $polaczenie -> query("SELECT * FROM produkt WHERE MODEL = '$model'");

		if(!$rezultat1) throw new Exception($polaczenie -> error);
		else
		{
			$wiersz = $rezultat1 -> fetch_assoc();
			
			echo "<div>";
			echo "<h2>".$wiersz['MODEL']."</h2>";
			echo "<h3> Cena: ".$wiersz['CENA']." zł</h2>";
				
			$index = $wiersz['MODEL'];
			foreach(PobierzZdjeciaProduktu($index) as $zdjecie)
			{
				echo "<a rel ='lightbox[$index]' href='FOTY/$zdjecie'>";		// <a href="images/image-2.jpg" data-lightbox="roadtrip">Image #2</a>
				echo "<img src=FOTY/mini/".$zdjecie.">";
				echo "</a>";
			}
			echo "<h4> Parametry: ".$wiersz['PARAMETRY']."</h4>";
			echo "<h5> Opis przedmiotu: ".$wiersz['OPIS']."</h5>";
			echo "</div>";
			
		
			$rezultat1->free();
		}
	}
	
	
?>

<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->
</body>
</html>