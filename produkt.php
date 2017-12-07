<!DOCTYPE HTML>		
<html lang = "pl">

<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">		
<link rel= "stylesheet" href= "style.css" type= "text/css" />			<!-- Lightbox -->
</head>
<body>
<div id = "container">
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
			
			echo "<div id = 'tabela'>";
			echo "<div id = 'tabelaL'> <div id = 'galeria'>"; 
			echo "<table> <tr> ";
			
			$index = $wiersz['MODEL'];	
			$i = 1;
			foreach(PobierzZdjeciaProduktu($index) as $zdjecie)
			{
				echo "<td>";
				echo "<a rel ='lightbox[$index]' href='ZDJECIA/$zdjecie'>";	//echo $zdjecie;	// <a href="images/image-2.jpg" data-lightbox="roadtrip">Image #2</a>
				echo "<img src = 'ZDJECIA/".$zdjecie."' height='140' width='140'>";
				echo "</a>";
				echo "</td>";
				if($i%2 == 0) { echo "</tr> <tr>"; }
				$i++;
			}
			echo "</tr> </table> </div>";	// galeria
			
			echo "</div> </div>";	// tabelaL podpis opcja
			
			echo "<div id = 'tabelaR'> <div id = 'parametry'> ";
			echo "<h2> &nbsp &nbsp &nbsp".$wiersz['TYTUL'];	
			echo "<div class = 'button'>";
			echo "<a href = 'DoKoszyka.php?model=$index' > Dodaj do koszyka </a></h2>";
			echo "</div>";
			echo "<h2> &nbsp &nbsp &nbsp &nbsp Cena: ".$wiersz['CENA']." zł</h2>";
			
			
			echo "</div> </div> </div>";	//tabelaR  parametry tabela
			echo "<div id = 'opis'>";
			echo "<h2> Parametry: </h2>".$wiersz['PARAMETRY'];
			echo "<h2> Opis przedmiotu: </h2>".$wiersz['OPIS'];
			echo "</div>";
			
			$rezultat1->free(); 
			$polaczenie -> close();
		}
	}
	
?>

</div>
<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->
</body>
</html>