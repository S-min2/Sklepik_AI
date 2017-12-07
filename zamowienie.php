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

	<div id = 'container'>
	<div id = 'logo'>
		<img src='ZDJECIA/SCIANY/LOGO2.jpg'>
    </div>
	<div id = 'zamowienie'>
	
<?php

	require_once "connect.php";

	session_start();
//var_dump($_SESSION['adres']);
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
				//$_SESSION['adres'] = NULL;
				
				$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika"); 
				
				if(!$rezultat1) throw new Ecxeption(error);
				else 
				{
					//$wiersz = $rezultat1 -> fetch_assoc();					
					$suma = 0;
					$zamowienie = '';
						while($produkt = $rezultat1 -> fetch_assoc())
						{
																			
							$model = $produkt['MODEL'];								 //echo $model;
							$ilosc = $produkt['ILOSC'];									//echo $ilosc;
							$cena = $produkt['CENA'] * $produkt['ILOSC'];
							$suma += $cena;
							
							$zamowienie = $zamowienie.$model;
							$zamowienie = $zamowienie."x";
							$zamowienie = $zamowienie.$ilosc;
							$zamowienie = $zamowienie."/";				
						}	
						
						$zamowienie = $zamowienie."==";
						$zamowienie = $zamowienie.$suma;													
						
						$odbiorca = $_SESSION['adres']; //var_dump($odbiorca); 
						$adres = $odbiorca['IMIE']; 	
						$adres = $adres."/";
						$adres = $adres.$odbiorca['NAZWISKO'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['ULICA'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['NR BUDYNKU'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['KOD POCZTOWY'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['MIEJSCOWOSC'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['WOJEWODZTWO'];
						$adres = $adres."/";
						$adres = $adres.$odbiorca['TELEFON'];
						$adres = $adres."."; 				

						/*$adres = sprintf("'%s'/'%s'/'%s'/'%s'/'%s'/'%s'/'%s'/'%s'.", $adres = $odbiorca['NAZWISKO'], $adres = $odbiorca['ULICA'], 
																					$adres = $odbiorca['NR BUDYNKU'],
																					 $adres = $odbiorca['KOD POCZTOWY'], $adres = $odbiorca['MIEJSCOWOSC'], 
																					 $adres = $odbiorca['WOJEWODZTWO'], $adres = $odbiorca['TELEFON']);		*/
										//echo $adres;
						
					$rezultat2 = $polaczenie -> query("INSERT INTO zamowienie (ID_FAKTURY, ID_UZYTKOWNIKA, MODEL, SUMA, ADRES) VALUES ('NULL', $id_uzytkownika, '$zamowienie', $suma, '$adres')");
					if(!$rezultat2) throw new Exception(error);
					else
					{	$rezultat3 = $polaczenie -> query("DELETE FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
						echo "<b><p> Twoje zamówienie zostało przyjęte do realizacji. Dziękujemy! <b>";
					}
					$rezultat1 -> free();
					
					unset($rezultat2);
					$polaczenie -> close();	
					
					echo "<p>   <div class = 'button2'> <a href= 'zalogowany.php' > Powrót do sklepu </a> </div> &nbsp  &nbsp &nbsp  &nbsp";
					echo " &nbsp  &nbsp  &nbsp  &nbsp  &nbsp  &nbsp";
					echo "<div class = 'button2'> <a href= 'wyloguj.php' > Wyloguj </a> </div>";
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
</div>
</div>
</body>
</html>