<?php

	session_start();
 
	if(!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	$ilosc = $_SESSION['ilosc_produktu_w_koszyku'];	echo $ilosc;
	$ilosc -= 1;									echo $ilosc;
	$model = $_GET['model'];						echo $model;
	$id_uzytkownika = $_SESSION['uzytkownik_id'];	echo $id_uzytkownika;
	
	mysqli_report(MYSQLI_REPORT_STRICT);	
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
			
			if($ilosc <= 0) 
			{
				$rezultat2 = $polaczenie -> query("DELETE FROM koszyk WHERE ID_UZYTKOWNIKA = $id_uzytkownika AND MODEL = '$model'");
			}
			else
			{
				$rezultat2 = $polaczenie -> query("UPDATE koszyk SET ILOSC = $ilosc WHERE MODEL = '$model' AND ID_UZYTKOWNIKA = $id_uzytkownika");
			}
			unset($rezultat2);
			$polaczenie -> close();
		}
	}
	catch(Exception $blad_polaczenia)
	{
		echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
		echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
	}
	
	header("Location: koszyk.php");
	
?>