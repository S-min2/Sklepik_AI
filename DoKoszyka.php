<?php

	session_start(); 
	require_once "connect.php";
	$model = $_GET['model'];
	
	if(isset($_SESSION['zalogowany']) && ($_SESSION['zalogowany'] == true)) { unset($_SESSION['blad_koszyk']); $id_uzytkownika = $_SESSION['uzytkownik_id'];	}
	else 
	{
		$_SESSION['blad_koszyk'] = '<span style = "color : red"> Aby dodać produkt do koszyka musisz się zalogować! <br><br> </span>';
		header("Location: index.php");	
			
	}
	
	//sprawdz czy juz go nie ma
	try
	{
		$polaczenie = new mysqli($host, $db_user, "$db_password", $db_name);
			
		if($polaczenie->connect_errno != 0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{	
	
			$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika AND MODEL = '$model'");
			
			if(!$rezultat1)  throw new Exception($polaczenie -> error);
			else
			{
				$wiersz = $rezultat1 -> fetch_assoc();
				$liczba_wierszy = $rezultat1 -> num_rows;
				
				if($liczba_wierszy > 0)
				{															
					 $ilosc = $wiersz['ILOSC'];echo $wiersz['ILOSC'];
					 $cena = $wiersz['CENA']; echo $cena;
				
					if( $ilosc > 0)
					{
						$ilosc = $ilosc + 1;
						$rezultat2 = $polaczenie -> query("UPDATE koszyk SET ILOSC = $ilosc WHERE MODEL = '$model' AND ID_UZYTKOWNIKA = $id_uzytkownika");
						unset($rezultat2);
					}
				}
				else 
				{
					$rezultat2 = $polaczenie -> query("SELECT * FROM produkt WHERE MODEL = '$model'"); 
					$wiersz = $rezultat2 -> fetch_assoc();
					$rezultat2->free();																			echo "rezultat =".$wiersz['CENA'].$wiersz['MODEL'];
					$cena = $wiersz['CENA'];
					echo "nowy"; echo $id_uzytkownika;
					$rezultat3 = $polaczenie -> query("INSERT INTO koszyk (ID_UZYTKOWNIKA, MODEL, CENA, ILOSC) VALUES ($id_uzytkownika, '$model', $cena, 1)"); 
					unset($rezultat3);
				}
			}
			header("Location: zalogowany.php");	
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}
?>
