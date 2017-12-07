<!DOCTYPE HTML>		
<html lang = "pl">

<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">			
<link rel= "stylesheet" href= "style.css" type= "text/css" />		<!-- Lightbox -->
</head>
<body>
<div id = "container">
<table>
<?php

	session_start();

	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany'] == true)) { unset($_SESSION['blad_koszyk']); $id_uzytkownika = $_SESSION['uzytkownik_id'];	}
	else 
	{
		$_SESSION['blad_koszyk'] = '<span style = "color : red"> Aby dodać produkt do koszyka musisz się zalogować! <br><br> </span>';
		header("Location: index.php");	
			
	}
	
	require_once "connect.php";
	
	try
	{
		$polaczenie = new mysqli($host, $db_user, "$db_password", $db_name);
			
		if($polaczenie->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{
	
			$rez = $polaczenie -> query('SET CHARACTER SET UTF8');
			$rez2 = $polaczenie -> query('SET collation_connection = UTF8_general_ci');
			if(!$rez || !$rez2) throw new Exception($polaczenie -> error);
			else 
			{	
				unset($rez); unset($rez2);
			}
			
			$id_uzytkownika = $_SESSION['uzytkownik_id']; //echo $id_uzytkownika;
			$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika"); //print_r($rezultat1);
			
			if(!$rezultat1) throw new Ecxeption(error);
			else 
			{															
				$suma = 0;
																		
					while($produkt = $rezultat1 -> fetch_assoc())
					{
																	
						$model = $produkt['MODEL'];
						
						$rezultat2 = $polaczenie -> query("SELECT TYTUL FROM produkt WHERE MODEL = '$model'");
						$tytul = $rezultat2 -> fetch_assoc();
						$nazwa = $tytul['TYTUL'];
						
						$ilosc = $produkt['ILOSC']; 
						$cena = $produkt['CENA'] * $produkt['ILOSC']; 
						$suma += $cena;
						
						echo "<tr>";
						echo "<td>";
						echo "&nbsp <img src=ZDJECIA/mini/".$model."-1.jpg>";	
						echo "</td> <td> <div id = 'podpis'>";
						echo "&nbsp &nbsp <h2>"; echo $nazwa; 
						echo "<br> &nbsp &nbsp".$cena." zł";	
						echo " </h2> </td> <td> <div id = 'podpis'> <br/> <h1>";		//podpis
						echo $ilosc." szt. </h1> </td> <td>";
						$_SESSION['ilosc_produktu_w_koszyku'] = $ilosc;
						echo "<div class = 'button'>";
						echo "<a href= 'dodaj.php?model=$model'> + </a> &nbsp &nbsp";
						echo "</div>";
						echo "<div class = 'button'>";
						echo "<a href= 'usun.php?model=$model'> - </a>";
						echo "</div> </div>";
						echo "</td> </tr>";
						
					}	
					echo "</table>";
					
					if($suma == 0) { echo "<span style= 'font-size: 22px'> <span style= 'color: red'>"; echo "<h1> Twój koszyk jest pusty :( </h1>" ; }
					else
					{
						echo "<span style= 'font-size: 32px'> <span style= 'color: red'>";
						echo "<br/> Łącznie do zapłaty: <b>".$suma."zł </b> </span> </span>";
						$_SESSION['suma'] = $suma;
						echo " <br><br> <div class ='button2'> ";
						echo " <a href= 'podsumowanie.php?id=$id_uzytkownika' > Zamawiam </a>";
						echo "</div>";
					}
				
				echo "<br/> <br/> <br/> <br/> <div class ='button2'> ";
				echo "<a href= 'zalogowany.php?kat_id=0'> Strona główna </a> <br/> </div>";
				$rezultat1 -> free();
				$polaczenie -> close();
			}
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}
?>

</div>
<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->
</body>
</html>