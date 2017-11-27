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

//require "funkcje.php";
require_once "connect.php";
	
$model = $_GET['model'];
$polaczenie = new mysqli($host, $db_user, "$db_password", $db_name);

PokazProdukt($model);


///// FUNKCJE //////////////////////////////
function PobierzZdjeciaProduktu($model)
	{
		$zdjecia = array();
		
		for($i = 1; $i < 10; $i++)
		{
			$nazwa = $model."-".$i.".jpg";		//['MODEL']-index   --->  ZD-971-x
			$sciezka = "ZDJECIA/".$nazwa;	 
			if(file_exists($sciezka))
			{
				$zdjecia[] = $nazwa;
			}
		}
		
		return $zdjecia;
	}
	
	
function PokazProdukt($model)
	{
		global $polaczenie;
		
		$rez = $polaczenie -> query('SET CHARACTER SET UTF8');
		$rez2 = $polaczenie -> query('SET collation_connection = UTF8_general_ci');
		if(!$rez || !$rez2) throw new Exception($polaczenie -> error);
			else 
		{	
			unset($rez); unset($rez2);
		}
			
		$rezultat1 = $polaczenie -> query("SELECT * FROM produkt WHERE MODEL = '$model'");

		if(!$rezultat1) throw new Exception($polaczenie -> error);
		else
		{
			$wiersz = $rezultat1 -> fetch_assoc();
			
			echo "<div>";
			echo "<h2>".$wiersz['TYTUL']."</h2>";
			echo "<h3> Cena: ".$wiersz['CENA']." zł</h2>";
				
			$index = $wiersz['MODEL'];
			foreach(PobierzZdjeciaProduktu($index) as $zdjecie)
			{
				echo "<a rel ='lightbox[$index]' href='ZDJECIA/$zdjecie'>";	//echo $zdjecie;	// <a href="images/image-2.jpg" data-lightbox="roadtrip">Image #2</a>
				echo "<img src=ZDJECIA/".$zdjecie.">";
				echo "</a>";
			}
			echo "<h4> Parametry: ".$wiersz['PARAMETRY']."</h4>";
			echo "<h5> Opis przedmiotu: ".$wiersz['OPIS']."</h5>";
			echo "<br><br>";
			echo "<a href = 'DoKoszyka.php?model=$index' > Dodaj do koszyka </a>";
			echo "</div>";
			
			$rezultat1->free(); 
			$polaczenie -> close();
		}
	}
	
?>

<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->
</body>
</html>