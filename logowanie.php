<?php

	session_start();
	
	if((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{
			header('Location: index.php');
			exit();
	}
	
	require_once "connect.php";
	
	try
	{	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
		if($polaczenie->connect_errno != 0) 
		{
			throw new Exception(mysqli_connect_errno());
			echo "Error: ".$polaczenie->connect_errno;
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
			
			$login =  $_POST['login'];
			$haslo =  $_POST['haslo'];
			$login = htmlentities($login,ENT_QUOTES,"UTF-8");
			$haslo = htmlentities($haslo,ENT_QUOTES,"UTF-8");				//echo "login to:".$login;
																			//echo "haslo to:".$haslo;
			$zapytanie = sprintf("SELECT * FROM uzytkownik WHERE LOGIN = '%s'", 
													mysqli_real_escape_string($polaczenie,$login));
												
			if($polaczenie->query($zapytanie))
			{
				$rezultat = @$polaczenie->query($zapytanie);														//echo "Zapytanie git";
				$liczba_uzytkownikow = $rezultat->num_rows;
																			//echo"liczba = ".$liczba_uzytkownikow;
				if($liczba_uzytkownikow > 0)
				{
					$dane_uzytkownika = $rezultat->fetch_assoc();
					
					if(password_verify($haslo,$dane_uzytkownika['HASLO']))
					{
						$_SESSION['zalogowany'] = true;							//echo "jeden uzytkownik";
					
						$uzytkownik_id = $dane_uzytkownika['ID_UZYTKOWNIKA'];
						$uzytkownik_email = $dane_uzytkownika['EMAIL'];
																			
						$_SESSION['uzytkownik_id'] = $uzytkownik_id;
						$_SESSION['uzytkownik_email'] = $uzytkownik_email;		//echo "email to: ".$uzytkownik_email;
						unset($_SESSION['blad_logowania']);
					
						$zapytanie2 = "SELECT * FROM ADRES WHERE ID_UZYTKOWNIKA = '$uzytkownik_id'";
																			
						if(@$polaczenie->query($zapytanie2))
						{	
							$rezultat2 = @$polaczenie->query($zapytanie2);
							$adres_uzytkownika = $rezultat2->fetch_assoc();
							$_SESSION['uzytkownik_imie'] = $adres_uzytkownika['IMIE'];
						}
					
						$rezultat->free();
						$rezultat2->free();
						header('Location: zalogowany.php');
					} 
					else
					{
						$_SESSION['blad_logowania'] = '<span style = "color : red"> Podane haslo jest nieprawidłowe!</span>';
						header('Location: index.php');
					}	
				}
				else 
				{
					$_SESSION['blad_logowania'] = '<span style = "color : red"> Nie istnieje użytkownik o takim loginie!</span>';
					header('Location: index.php');
				}
				
			}	
						
			$polaczenie->close();
		}
	}
	catch(Exception $wyjatek)
		{
			echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
			echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
		}
	
?>