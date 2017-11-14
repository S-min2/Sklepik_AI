<!DOCTYPE HTML>		
<html lang = "pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">					<!-- Lightbox -->
</head>


<?php
	session_start();	
	
	if((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany'] == true))
	{
		header('Location: zalogowany.php');
		exit();
	}
?>

<body>
   
	<form action = "logowanie.php" method = "post" >
	
	   Login: <br /> <input type = "text" name = "login" /> <br />
	   Hasło: <br /> <input type = "password" name = "haslo" /> <br />
	   <input type = "submit" value = "Zaloguj się" />
	</form>
	
	<b><p> <a href = "rejestracja.php" > Załóż konto </a> </br> </br>
	
<?php
	if(isset($_SESSION['blad_logowania'])) { echo "".$_SESSION['blad_logowania']; }
	
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
			echo "<p> Czego potrzebujesz? <p/>";		
									
			WybierzKategorie();	
						
			if(isset($_GET['kat_id'])) { $kategoria_id = $_GET['kat_id']; unset($_GET['kat_id']); }
			else {$kategoria_id = 0; }
																							echo "kategoria: ".$kategoria_id;
			PokazProdukty($kategoria_id);
			
			$polaczenie->close();
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}

	
	
/////////  FUNKCJE	
	
	 function WybierzKategorie()
	{
		global $polaczenie;
		$rezultat1 = $polaczenie -> query("SELECT * FROM kategoria");
		
		if(!$rezultat1) throw new Exception($polaczenie -> error);
		else
		{	
			echo "<a href= 'index.php?kat_id=0'> Strona główna </a><br/>";
			$ile_kategorii = $rezultat1 -> num_rows;
			while($wiersz = $rezultat1->fetch_assoc())
			{
				
				$kategoria_id = $wiersz['ID_KATEGORII']; 
				$kategoria_nazwa = $wiersz['NAZWA'];
				echo "<p>";
				echo "<a href='index.php?kat_id=$kategoria_id'>$kategoria_nazwa</a>";
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
				
				echo "<img src=FOTY/mini/".$zdjecie."><br>";
				
				// nazwa -- link do strony produktu
				echo " <a href='produkt.php?model=$index'>";
				echo $wiersz['MODEL']."<br>";
				echo "</a>";
				
				// cena
				echo $wiersz['CENA']."zł <br>";
			
					//echo "<a rel ='lightbox[$index]' href='FOTY/$zdjecie'>";		// <a href="images/image-2.jpg" data-lightbox="roadtrip">Image #2</a>
					//echo "<img src=FOTY/mini/".$zdjecie.">";
					//echo "</a>";
	
				echo "</div>";
			}
		
			$rezultat1->free();
		}
	}
	
	
	
	function PobierzZdjeciaProduktu($model)
	{
		$zdjecia = array();
		
		for($i = 1; $i < 10; $i++)
		{
			$nazwa = $model."-".$i.".jpg";		//['MODEL']-index   --->  ZD-971-x
			$sciezka = "FOTY/mini/".$nazwa;	 
			if(file_exists($sciezka))
			{
				$zdjecia[] = $nazwa;
			}
		}
		
		return $zdjecia;
	}
	
?>
	
	
<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->

</body>
</html>	   