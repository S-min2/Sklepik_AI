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
	
			$id_uzytkownika = $_SESSION['uzytkownik_id']; 
			$rezultat1 = $polaczenie -> query("SELECT * FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika");
			
			if(!$rezultat1) throw new Ecxeption(error);
			else 
			{
				$wiersz = $rezultat1 -> fetch_assoc();					//var_dump($wiersz); print_r($wiersz); 
				$suma = 0;
				
					while($produkt = $rezultat1 -> fetch_assoc())
					{
																			//print_r($produkt); echo"<p>";
						$model = $produkt['MODEL'];
						$ilosc = $produkt['ILOSC'];
						$cena = $produkt['CENA'] * $produkt['ILOSC'];
						$suma += $cena;
						
						echo $model; echo "<br>";
						echo $ilosc;
						$_SESSION['ilosc_produktu_w_koszyku'] = $ilosc;
						echo "<a href= 'dodaj.php?model=$model'> + </a>";
						echo "<a href= 'usun.php?model=$model'> - </a>"; echo "<br>";
						echo $cena;	echo "<br>";
					}	
					
					if($suma == 0) echo "Twój koszyk jest pusty :(" ;
					else
					{
						echo "Łącznie do zapłaty: ".$suma."zł";
						$_SESSION['suma'] = $suma;
						echo " <br><br>";
						echo " <a href= 'podsumowanie.php?id=$id_uzytkownika' > Zamawiam </a>";
					}
				
				echo "<br><br>";
				echo "<a href= 'zalogowany.php?kat_id=0'> Strona główna </a><br/>";
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