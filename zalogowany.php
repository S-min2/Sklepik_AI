<?php

	session_start(); 
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['zalogowany']);
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
<meta charset = " utf-8 " />
<meta http-equiv = " X-UA-Compatible " content = " IE = edge, chrome 1 " />
<title> Sklep Internetowy </title>
</head>

<body>
   
<?php
	$uzytkownik_imie = $_SESSION['uzytkownik_imie'];
	$uzytkownik_email = $_SESSION['uzytkownik_email'];
	
	echo "<p> Witaj ".$uzytkownik_imie."  (".$uzytkownik_email.")";
	echo "<p><b> Stan Twojego koszyka: " ;//.
	echo '<a href = "wyloguj.php"> <b><p> Wyloguj się </a> </p>';
?>

</body>
</html>	   