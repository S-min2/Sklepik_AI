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
	
			$id_uzytkownika = $_SESSION['uzytkownik_id'];
			
			$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
			$rezultat2 = $polaczenie -> query("SELECT * FROM adres WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
			
			if(!$rezultat1 || !$rezultat2) throw new Ecxeption(error);
			else
			{
				$adres = $rezultat2 -> fetch_assoc();
																		
				echo "<p> <b> Zamawiane przedmioty: </b><br><br> ";
				
				$suma = 0;	
				while($produkt = $rezultat1 -> fetch_assoc())
					{
						
						$model = $produkt['MODEL'];
						$ilosc = $produkt['ILOSC'];
						$cena = $produkt['CENA'] * $produkt['ILOSC'];
						$suma += $cena;
						
						echo $model; echo "  x  "; echo $ilosc; echo "...........".$cena." PLN <br>";
					}
					
				echo "<b><br>Łącznie do zapłaty: ".$suma."zł </b><p>";
				$_SESSION['suma'] = $suma;
				
				
				echo "<p><b>Dane do wysyłki: </b> <br>    ";
				echo " <a href= 'dane_wysyłki.php'> Zmień adres wysyłki </a></br></br>";
				//$dane_wysyłki = $_SESSION['adres'];
				if(!isset($_SESSION['dane_wysyłki'])) 
				{
					$imie = $adres['IMIE'];
					$nazwisko = $adres['NAZWISKO'];
					$ulica = $adres['ULICA'];
					$nr_domu = $adres['NR BUDYNKU'];
					$kod_pocztowy = $adres['KOD POCZTOWY'];
					$miejscowosc = $adres['MIEJSCOWOSC'];
					$wojewodztwo = $adres['WOJEWODZTWO'];
					$telefon = $adres['TELEFON'];
					
					$_SESSION['adres'] = $adres;
				}
				else
				{	
					$imie = $dane_wysyłki['IMIE'];
					$nazwisko = $dane_wysyłki['NAZWISKO'];
					$ulica = $dane_wysyłki['ULICA'];
					$nr_domu = $dane_wysyłki['NR BUDYNKU'];
					$kod_pocztowy = $dane_wysyłki['KOD POCZTOWY'];
					$miejscowosc = $dane_wysyłki['MIEJSCOWOSC'];
					$wojewodztwo = $dane_wysyłki['WOJEWODZTWO'];	
					$telefon = $dane_wysyłki['TELEFON'];
					
					$_SESSION['adres'] = $dane_wysyłki;
				}
				
				echo $imie, $nazwisko; echo "<br>";
				echo "ul. ".$ulica, $nr_domu; echo "<br>";
				echo $kod_pocztowy; echo" ".$miejscowosc; echo "<br>";
				echo "woj. ".$wojewodztwo;  echo "<br>";
				echo "tel.".$telefon;  

			}
				echo " <p><a href= 'zamowienie.php' > Zamawiam i płacę </a>";
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}
?>