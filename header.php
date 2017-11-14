<html lang = "pl">
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome 1"/>
<title> Sklep Internetowy </title>
<link href="css/lightbox.css" rel="stylesheet">					<!-- Lightbox -->
</head>
<?php

	require "funkcje.php";
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
	

?>	
	
	
	
	
	