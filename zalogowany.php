<?php

	session_start(); 
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
// Usunięcie zmiennych pamietających dane wpisane do formularza
	if(isset($_SESSION['fr_login']))  unset($_SESSION['fr_login']);
	if(isset($_SESSION['fr_haslo']))  unset($_SESSION['fr_haslo']);
	if(isset($_SESSION['fr_haslo2']))  unset($_SESSION['fr_haslo2']);
	if(isset($_SESSION['fr_imie']))  unset($_SESSION['fr_imie']);
	if(isset($_SESSION['fr_nazwisko']))  unset($_SESSION['fr_nazwisko']);
	if(isset($_SESSION['fr_ulica']))  unset($_SESSION['fr_ulica']);
	if(isset($_SESSION['fr_nr_budynku']))  unset($_SESSION['fr_nr_budynku']);
	if(isset($_SESSION['fr_kod_pocztowy']))  unset($_SESSION['fr_kod_pocztowy']);
	if(isset($_SESSION['fr_miejscowosc']))  unset($_SESSION['fr_miejscowosc']);
	if(isset($_SESSION['fr_wojewodztwo']))  unset($_SESSION['fr_wojewodztwo']);
	if(isset($_SESSION['fr_email']))  unset($_SESSION['fr_email']);
	if(isset($_SESSION['fr_telefon']))  unset($_SESSION['fr_telefon']);
	if(isset($_SESSION['fr_regulamin']))  unset($_SESSION['fr_regulamin']);
?>

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
	$uzytkownik_imie = $_SESSION['uzytkownik_imie'];
	$uzytkownik_email = $_SESSION['uzytkownik_email'];
	$id_uzytkownika = $_SESSION['uzytkownik_id'];
	
	echo "<p> Witaj ".$uzytkownik_imie."  (".$uzytkownik_email.")";

	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);		// wyłącz wyświetlanie błędów
	
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
			
			$suma = SumaKoszyk($id_uzytkownika);
			
			echo "<p><b> Stan Twojego koszyka: ".$suma."zł";
			echo '<a href = "wyloguj.php"> <b><p> Wyloguj się </a> </p>';
			echo "<p> Czego potrzebujesz? <p/>";		
									
			WybierzKategorie();	
						
			if(isset($_GET['kat_id'])) { $kategoria_id = $_GET['kat_id']; unset($_GET['kat_id']); }
			else {$kategoria_id = 0; }
																							//echo "kategoria: ".$kategoria_id;
			PokazProdukty($kategoria_id);
			
			//$polaczenie->close();
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}

/////////  FUNKCJE	
	function SumaKoszyk($id_uzytkownika)
	{
		global $polaczenie;
		
		$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
		
		$suma = 0;
		while($produkt = $rezultat1 -> fetch_assoc())
			{
				$ilosc = $produkt['ILOSC'];
				$cena = $produkt['CENA'] * $produkt['ILOSC'];
				$suma += $cena;
				//return $suma;			
			}
		return $suma;
	}	

	
	function WybierzKategorie()
	{
		global $polaczenie;
		$rezultat1 = $polaczenie -> query("SELECT * FROM kategoria");
		
		if(!$rezultat1) throw new Exception($polaczenie -> error);
		else
		{	
			echo "<a href= 'zalogowany.php?kat_id=0'> Strona główna </a><br/>";
			echo "<a href= 'koszyk.php'> KOSZYK </a> <br/>";
			$ile_kategorii = $rezultat1 -> num_rows;
			while($wiersz = $rezultat1->fetch_assoc())
			{
				
				$kategoria_id = $wiersz['ID_KATEGORII']; 
				$kategoria_nazwa = $wiersz['NAZWA'];
				echo "<p>";
				echo "<a href='zalogowany.php?kat_id=$kategoria_id'>$kategoria_nazwa</a>";
			}
			
		}	
		
		$rezultat1->free();	
		
	}
	
	
	function PokazProdukty($kategoria_id)
	{
		global $polaczenie;
		
		if($kategoria_id)
		{
			$rezultat1 = $polaczenie -> query("SELECT * FROM produkt WHERE ID_KATEGORII = $kategoria_id");
		}
		else
		{	
			$rezultat1 = $polaczenie -> query("SELECT * FROM produkt");
		}
		
		if(!$rezultat1) throw new Exception($polaczenie -> error);
		else
		{
			$ile_produktow = $rezultat1 -> num_rows;
			
			echo "<p>Znaleziono ".$ile_produktow." produktów";
			
			while($wiersz = $rezultat1 -> fetch_assoc())
			{
				echo "<div>";
				echo "<h2>";
				$index = $wiersz['MODEL'];
				
				// zdjecie
				$zdjecia_produktu = PobierzZdjeciaProduktu($index);
				if(!empty($zdjecia_produktu))		
				{	
					$zdjecie = $zdjecia_produktu[0];
				}
				else 
				{
					$zdjecie = 'no-foto.jpg';
				}
				//echo $zdjecie;
				echo "<img src=ZDJECIA/mini/".$zdjecie."><br>";
				//echo "<img src=ZDJECIA/mini/CAVRA-3.jpg>";
				// nazwa -- link do strony produktu
				echo " <a href='produkt.php?model=$index'>";
				echo $wiersz['TYTUL']."<br>";
				echo "</a>";
				
				// cena
				echo $wiersz['CENA']."zł <br>";
				echo "</div>";
			}
		
			$rezultat1->free();
			//$polaczenie->close();
		}
	}
	
	
	function PobierzZdjeciaProduktu($model)
	{
		$zdjecia = array();
		
		for($i = 1; $i < 10; $i++)
		{
			$nazwa = $model."-".$i.".jpg";		//['MODEL']-index   --->  ZD-971-x
			$sciezka = "ZDJECIA/mini/".$nazwa;	 //echo $sciezka;
			if(file_exists($sciezka))
			{
				$zdjecia[] = $nazwa;
			}
		}
		
		return $zdjecia;
	}
	
	$polaczenie->close();
?>
	
	
<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->

</body>
</html>	   
</body>
</html>	   