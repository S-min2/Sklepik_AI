<!DOCTYPE HTML>		
<html lang = "pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">					<!-- Lightbox -->
<link rel= "stylesheet" href= "style.css" type= "text/css" />
</head>
 
<body>

<div id = "podsumowanie">
<div id = "paragon">
<?php

	session_start();

	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany'] == true)) { unset($_SESSION['blad_koszyk']); $id_uzytkownika = $_SESSION['uzytkownik_id'];	}
	else 
	{
		$_SESSION['blad_koszyk'] = '<span style = "color : red"> Aby kupować w naszym sklepie musisz się zalogować! <br><br> </span>';
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
			
			$id_uzytkownika = $_SESSION['uzytkownik_id'];
			
			$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
			$rezultat2 = $polaczenie -> query("SELECT * FROM adres WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
			
			if(!$rezultat1 || !$rezultat2) throw new Ecxeption(error);
			else
			{
				$adresat = $rezultat2 -> fetch_assoc();	
																		
				echo "<p> <b> Zamawiane przedmioty: </b><br><br> ";
				
				$suma = 0;	
				while($produkt = $rezultat1 -> fetch_assoc())
					{
						
						$model = $produkt['MODEL'];
						
						$rezultat3 = $polaczenie -> query("SELECT TYTUL FROM produkt WHERE MODEL = '$model'");
						$wiersz = $rezultat3 -> fetch_assoc();
						$nazwa = $wiersz['TYTUL'];
						
						$ilosc = $produkt['ILOSC'];
						$cena = $produkt['CENA'] * $produkt['ILOSC'];
						$suma += $cena;
						
						echo $nazwa; echo "  x  "; echo $ilosc; echo "...........".$cena." PLN <br>";
					}
					
				echo "...........................................................................................";
				echo "<b><br>Łącznie do zapłaty: ".$suma."zł </b><p>";
				$_SESSION['suma'] = $suma;
				
				
				echo "<p><b><br/>Dane do wysyłki: </b> <br>    ";
				echo " <div class= 'button'> <a href= 'dane_wysyłki.php'> Zmień adres wysyłki </a> </div>";
				if(isset($_SESSION['dane_wysylki'])) 
				{
					$dane_wysylki = $_SESSION['dane_wysylki'];				//var_dump($dane_wysylki);
					
					$imie = $dane_wysylki['IMIE'];
					$nazwisko = $dane_wysylki['NAZWISKO'];
					$ulica = $dane_wysylki['ULICA'];
					$nr_domu = $dane_wysylki['NR BUDYNKU'];
					$kod_pocztowy = $dane_wysylki['KOD POCZTOWY'];
					$miejscowosc = $dane_wysylki['MIEJSCOWOSC'];
					$wojewodztwo = $dane_wysylki['WOJEWODZTWO'];	
					$telefon = $dane_wysylki['TELEFON'];
					$email = $dane_wysylki['EMAIL'];
					
					$_SESSION['adres'] = $dane_wysylki;
					unset($_SESSION['dane_wysylki']);
				}
				else
				{	
					$imie = $adresat['IMIE'];
					$nazwisko = $adresat['NAZWISKO'];
					$ulica = $adresat['ULICA'];
					$nr_domu = $adresat['NR BUDYNKU'];
					$kod_pocztowy = $adresat['KOD POCZTOWY'];
					$miejscowosc = $adresat['MIEJSCOWOSC'];
					$wojewodztwo = $adresat['WOJEWODZTWO'];
					$telefon = $adresat['TELEFON'];
					//$email = $adresat['EMAIL'];
					
					$_SESSION['adres'] = $adresat;
				}
				
				echo $imie." ".$nazwisko; echo "<br>";
				echo "ul.".$ulica, $nr_domu; echo "<br>";
				echo $kod_pocztowy; echo" ".$miejscowosc; echo "<br>";
				echo "woj.".$wojewodztwo;  echo "<br>";
				//echo "adres e-mail: ".$email;  echo "<br>";
				echo "tel.".$telefon;  

			}
				$rezultat1 -> free();
				$rezultat2 ->free();
				$polaczenie->close();
				
				echo " <p> <br/> <div class= 'button2'> <a href= 'zamowienie.php' > Zamawiam i płacę </div> </a>"; 
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}
	
	//var_dump($_SESSION['adres']);
?>
</div>
</div>
</body>