<?php

	require_once "connect.php";

	session_start();

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
				$_SESSION['adres'] = NULL;
				
				$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
				
				if(!$rezultat1) throw new Ecxeption(error);
				else 
				{
					$wiersz = $rezultat1 -> fetch_assoc();					
					$suma = 0;
					$zamowienie = '';
						while($produkt = $rezultat1 -> fetch_assoc())
						{
																			
							$model = $produkt['MODEL'];
							$ilosc = $produkt['ILOSC'];
							$cena = $produkt['CENA'] * $produkt['ILOSC'];
							$suma += $cena;
							
							$zamowienie = $zamowienie.$model;
							$zamowienie = $zamowienie."x";
							$zamowienie = $zamowienie.$ilosc;
							$zamowienie = $zamowienie.".";
						}	
						
						$zamowienie = $zamowienie."==";
						$zamowienie = $zamowienie.$suma;													
						
						$odbiorca = $_SESSION['adres'];
						
						$adres = $odbiorca['imie'];
						$adres = $adres."/";
						$adres = $odbiorca['nazwisko'];
						$adres = $adres."/";
						$adres = $odbiorca['ulica'];
						$adres = $adres."/";
						$adres = $odbiorca['nr_budynku'];
						$adres = $adres."/";
						$adres = $odbiorca['kod_pocztowy'];
						$adres = $adres."/";
						$adres = $odbiorca['miejscowosc'];
						$adres = $adres."/";
						$adres = $odbiorca['wojewodztwo'];
						$adres = $adres."/";
						$adres = $odbiorca['telefon'];
						$adres = $adres.".";
						
					$rezultat2 = $polaczenie -> query("INSERT INTO zamowienie (ID_FAKTURY, ID_UZYTKOWNIKA, MODEL, SUMA, ADRES) VALUES (NULL, $id_uzytkownika, '$zamowienie', $suma, '$adres')");
					if(!$rezultat2) throw new Exception(error);
					else
					{	$rezultat3 = $polaczenie -> query("DELETE FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
						echo "<b><p> Twoje zamówienie zostało przyjęte do realizacji. Dziękujemy! <b>";
					}
					$rezultat1 -> free();
					
					unset($rezultat2);
					$polaczenie -> close();	
					
					echo "<p>  &nbsp  &nbsp &nbsp  &nbsp <a href= 'zalogowany.php' > Powrót do sklepu </a>";
					echo " &nbsp  &nbsp  &nbsp  &nbsp  &nbsp  &nbsp";
					echo "<a href= 'wyloguj.php' > Wyloguj </a>";
				}
			}
		}
		catch(Exception $blad_polaczenia)
		{
			echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
			echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
		}
			
?>