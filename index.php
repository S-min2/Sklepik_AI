<!DOCTYPE HTML>		
<html lang = "pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">					<!-- Lightbox -->
<link rel= "stylesheet" href= "style.css" type= "text/css" />
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

   <div id = 'logo'>
		<img src='SCIANY/LOGO2.jpg'>
   </div>

   <div id = 'tabela'>
	<div id = 'tabelaL'>
   <div id = 'logowanie'> <br>
	<form action = "logowanie.php" method = "post" >
	
	   &nbsp &nbsp &nbsp <b>Login:  <br/>  &nbsp &nbsp &nbsp &nbsp <input type = "text" name = "login" /> <br /> <br/>
	   &nbsp &nbsp &nbsp	 Hasło:   <br/> &nbsp &nbsp &nbsp &nbsp <input type = "password" name = "haslo" /> </b> <br /> <br/>
	   
<?php
	if(isset($_SESSION['blad_logowania']))  { echo $_SESSION['blad_logowania']; unset($_SESSION['blad_logowania']); echo "<br/>"; echo "<br/>"; }
?>

	&nbsp &nbsp &nbsp &nbsp  &nbsp  &nbsp  &nbsp  <input type = "submit" value = "Zaloguj się" />
	</form>
	<p>
	<div class ='opcja'>
	&nbsp <b> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp <a href = "rejestracja.php" > Załóż konto </a> 
	</div>
	</div>
	</br> </br>
	
<?php
	
	if(isset($_SESSION['blad_koszyk'])) { echo $_SESSION['blad_koszyk'];  unset($_SESSION['blad_koszyk']); echo "<br/>"; }
	
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
			echo "<div id='menu'>";
			echo "<p> &nbsp Czego potrzebujesz? <p/>";
			WybierzKategorie();		
			echo "</div>";
			echo "</div>";  // tabelaL
			if(isset($_GET['kat_id'])) { $kategoria_id = $_GET['kat_id']; unset($_GET['kat_id']); }
			else {$kategoria_id = 0; }	
			echo "<div id = 'tabelaR'>";
			echo "<div id= 'produkty'>";
			PokazProdukty($kategoria_id);
			echo "</div>";
			echo "</div>";
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
			
			echo "<div class= 'opcja'>";
			echo "<a href= 'index.php?kat_id=0'> Wszystkie produkty </a><br/>"; 
			echo "</div>";
			
			$ile_kategorii = $rezultat1 -> num_rows;
			while($wiersz = $rezultat1->fetch_assoc())
			{
				
				$kategoria_id = $wiersz['ID_KATEGORII']; 
				$kategoria_nazwa = $wiersz['NAZWA'];
				echo "<p>";
				echo "<div class= 'opcja'>";
				echo "<a href='index.php?kat_id=$kategoria_id'>$kategoria_nazwa</a>";
				echo "</div>";
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
			
			echo " <p> &nbsp Znaleziono ".$ile_produktow." produktów";
			echo "<table>";
			
			while($wiersz = $rezultat1 -> fetch_assoc())
			{
				
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
				
				echo "<tr>";
				echo "<td>";
				
				echo "&nbsp <img src=ZDJECIA/mini/".$zdjecie.">";		// nazwa -- link do strony produktu
				echo "</td>"; echo "<td>";
				echo "&nbsp &nbsp <a href='produkt.php?model=$index'>  ";
				echo $wiersz['TYTUL']."<br>";
				echo "</a> &nbsp &nbsp ";
				// cena
				echo $wiersz['CENA']."zł <br>";
				echo "</td>";
				echo "</tr>";
				
			}
			echo "</table>";
			
			$rezultat1->free();
		}
	}
	
	
	
	function PobierzZdjeciaProduktu($model)
	{
		$zdjecia = array();
		
		for($i = 1; $i < 10; $i++)
		{
			$nazwa = $model."-".$i.".jpg";		//['MODEL']-index   --->  ZD-971-x
			$sciezka = "ZDJECIA/mini/".$nazwa;	 
			if(file_exists($sciezka))
			{
				$zdjecia[] = $nazwa;
			}
		}
		
		return $zdjecia;
	}
	
?>
</div> 
<script src="js/lightbox-plus-jquery.js"></script>			<!-- Lightbox -->

</body>
</html>	   