<?php
	session_start();	
	
	if(isset($_POST['haslo2']))		// byle jaka zmienna - czy formularz przeslany?
	{
		$formularz_OK = true;
		
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		$haslo2 = $_POST['haslo2'];
		$imie = $_POST['imie'];
		$nazwisko = $_POST['nazwisko'];
		$ulica = $_POST['ulica'];
		$nr_budynku = $_POST['nr_budynku'];
		$kod_pocztowy = $_POST['kod_pocztowy'];
		$miejscowosc = $_POST['miejscowosc'];
		$wojewodztwo = $_POST['wojewodztwo'];
		$email = $_POST['email'];
		$telefon = $_POST['telefon'];
	
// login:	
		if((strlen($login) < 3) || (strlen($login) > 10))
		{
			$formularz_OK = false;
			$_SESSION['blad_login'] = "Login musi posiadać od 3 do 10 znaków!";
		}
		
		if(ctype_alnum($login) == false)				// znaki alfanumeryczne
		{
			$formularz_OK = false;
			$_SESSION['blad_login'] = "Login może zawierać tylko litery i cyfry (bez polskich znaków)!";	
		}	
		
// hasło:	
		if((strlen($haslo) < 8) || (strlen($login) > 25))
		{
			$formularz_OK = false;
			$_SESSION['blad_haslo'] = "Hasło musi posiadać od 8 do 25 znaków!";
		}
		
		if(!ctype_alnum($haslo))				
		{
			$formularz_OK = false;
			$_SESSION['blad_login'] = "Hasło może zawierać tylko litery i cyfry (bez polskich znaków)!";	
		}
		
// hasło2:
		if($haslo != $haslo2)
		{
			$formularz_OK = false;
			$_SESSION['blad_haslo2'] = "Podane hasła różnią się!";
		}	
		
// hashwanie hasła:
		$haslo_hash = password_hash($haslo,PASSWORD_DEFAULT);
																					//		echo $haslo_hash; exit();

// imie:
		if(PolskiWzorzec($imie) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_imie'] = "W imieniu znajdują się niedozwolone znaki!";
		}
// nazwisko:
		if(PolskiWzorzec($nazwisko) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_nazwisko'] = "W nazwisku znajdują się niedozwolone znaki!";
		}
// ulica:		
		if(PolskiWzorzec($ulica) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_ulica'] = "W nazwie ulicy znajdują się niedozwolone znaki!";
		}
// nr budynku:
		if(SzukajCyfry($ulica) == true)
		{
			$formularz_OK = false;
			$_SESSION['blad_numer_budynku'] = "W numerze budynku nie znajduje się cyfra!";
		}
		
// kod_pocztowy:
		if(SprawdzKodPocztowy($kod_pocztowy) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_kod'] = "Użyj formatu XX-XXX !";
		}
		
// miejscowosc:
		if(PolskiWzorzec($miejscowosc) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_miejscowosc'] = "W nazwie miejscowosci znajdują się niedozwolone znaki!";
		}
		
// wojewodztwo:
		
		
		if(PolskiWzorzec($wojewodztwo) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_wojewodztwo'] = "W nazwie wojewodztwa znajdują się niedozwolone znaki!";
		}	
		
// e-mail:	
		$email_B = filter_var($email,FILTER_SANITIZE_EMAIL);			// usuwa zabronine znaki np. polskie
		
		if((filter_var($email_B,FILTER_VALIDATE_EMAIL) == false) || ($email_B != $email))
		{
			$formularz_OK = false;
			$_SESSION['blad_email'] = "Niepoprawny adres e-mail!";
		}
		
// telefon:

		if((strlen($telefon) != 9))				   // 9 cyfr dla polski
		{
			$formularz_OK = false;
			$_SESSION['blad_telefon'] = "Niepoprawny numer telefonu!";
		}

// checkbox - regulamin:
		if(isset($_POST['regulamin']) == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_regulamin'] = "Proszę zapoznać się z regulaminem i zaakceptować go!";
		}

// BOT - recaptcha:
		$sekret = "6Ld4BzgUAAAAAPg2tSQefE2NqzAUx-El0Opblhzz";
		$zapytaj_wuja = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		$odpowiedz_wuja = json_decode($zapytaj_wuja);
		
		if($odpowiedz_wuja->success == false)
		{
			$formularz_OK = false;
			$_SESSION['blad_recaptcha'] = "Potwierdz, że nie jesteś BOT'em!";
		}

// Zapamiętaj dane w formularzu
		$_SESSION['fr_login'] = $login;
		$_SESSION['fr_haslo'] = $haslo;
		$_SESSION['fr_haslo2'] = $haslo2;
		$_SESSION['fr_imie'] = $imie;
		$_SESSION['fr_nazwisko'] = $nazwisko;
		$_SESSION['fr_ulica'] = $ulica;
		$_SESSION['fr_nr_budynku'] = $nr_budynku;
		$_SESSION['fr_kod_pocztowy'] = $kod_pocztowy;
		$_SESSION['fr_miejscowosc'] = $miejscowosc;
		$_SESSION['fr_wojewodztwo'] = $wojewodztwo;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_telefon'] = $telefon;
		if(isset($_POST['regulamin']) == true) $_SESSION['fr_regulamin'] = true;
		
// Powielone dane:
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
				
// sprawdz czy jest taki email:
				$rezultat = $polaczenie -> query("SELECT ID_UZYTKOWNIKA FROM uzytkownik WHERE EMAIL = '$email'");
				if(!$rezultat) throw new Exception($polaczenie -> error);
				
				$ile_email = $rezultat -> num_rows;
				if($ile_email > 0)
				{
					$formularz_OK = false;
					$_SESSION['blad_email'] = "W bazie użytkowników znajduje się użytkownik o takim adresie e-mail!";
				}
				
// sprawdz czy jest taki login:	
				$rezultat2 = $polaczenie -> query("SELECT ID_UZYTKOWNIKA FROM uzytkownik WHERE LOGIN = '$login'");
				if(!$rezultat2) throw new Exception($polaczenie -> error);
				
				$ile_login = $rezultat2 -> num_rows;
				if($ile_login > 0)
				{
					$formularz_OK = false;
					$_SESSION['blad_login'] = "W bazie użytkowników znajduje się użytkownik o takim loginie!";
				}
				
// POPRAWNIE WYPEŁNIONY FORMULARZ !!! 
				if($formularz_OK == true)
				{
					$rezultat3 = $polaczenie -> query("INSERT INTO uzytkownik VALUES (NULL, '$login', '$haslo_hash', '$email')");
					if(!$rezultat3) throw new Exception($polaczenie -> error);
					
					$rezultat4 = $polaczenie -> query("SELECT * FROM uzytkownik WHERE LOGIN = '$login'");
					if(!$rezultat4) throw new Exception($polaczenie -> error);
					$nowy_uzytkownik = $rezultat4 -> fetch_assoc();
					$id_z_bazy = $nowy_uzytkownik['ID_UZYTKOWNIKA'];
					
					$rezultat5 = $polaczenie -> query("INSERT INTO adres VALUES ($id_z_bazy, '$imie', '$nazwisko', '$ulica', $nr_budynku,
																				'$kod_pocztowy', '$miejscowosc', '$wojewodztwo', $telefon)");
					if(!$rezultat5) throw new Exception($polaczenie -> error);
					else 
					{
						unset($rezultat3);
						$rezultat4->free();
						unset($rezultat5);
						
						echo "Twoje konto zostało utworzone. Zaloguj się by w pełni korzystać z serwisu ;)"; 
						header('Location:index.php');	
					}						
				}
				
				$rezultat->free();
				$rezultat2->free();
				unset($rezultat3);
				$rezultat4->free();
				unset($rezultat5);
				
				$polaczenie->close();
			
			}	
		}
		catch(Exception $wyjatek)
		{
			echo '<span style = "color:red;"> <b><u> Błąd serwera! Prosimy spróbować za jakiś czas. Przepraszamy za niedogodności. </span></b><br/><br/></u>';
			echo '<br/>Informacja developerska: '.$wyjatek.'<br/><br/>';
		}

	}
?>

<!DOCTYPE HTML>
<html lang = "pl">

<head>

	<meta charset = " utf-8 " />
	<meta http-equiv = " X-UA-Compatible " content = " IE = edge, chrome 1 " />
	<title> Sklep Internetowy - Rejestracja </title>

	<script src='https://www.google.com/recaptcha/api.js'></script>			

	<style>
		.blad
		{
			color : red;
			margin-top: 10px;
			margin-bottom: 10px;
		}
	</style>

</head>

<body>
    
	<form method = "post">
		
		<b> Dane logowania: </b> <p>
		
		Login: 		   <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_login'])) 
							{
								echo $_SESSION['fr_login'];
								unset($_SESSION['fr_login']);
							}
							?>"
							
						name = "login" /> <br/>
			<?php
				if(isset($_SESSION['blad_login'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_login'].'</div>';
					unset($_SESSION['blad_login']);
				}
			?>
		
		Hasło: 		   <br/><input type = "password" value = "<?php 
							
							if(isset($_SESSION['fr_haslo'])) 
							{
								echo $_SESSION['fr_haslo'];
								unset($_SESSION['fr_haslo']);
							}
							?>"
							
						name = "haslo" /> <br/>
			<?php
				if(isset($_SESSION['blad_haslo'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_haslo'].'</div>';
					unset($_SESSION['blad_haslo']);
				}
			?>
		
		Powtórz hasło: <br/><input type = "password" value = "<?php 
							
							if(isset($_SESSION['fr_haslo2'])) 
							{
								echo $_SESSION['fr_haslo2'];
								unset($_SESSION['fr_haslo2']);
							}
							?>"
							
						name = "haslo2" /> <br/><br/>
			<?php
				if(isset($_SESSION['blad_haslo2'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_haslo2'].'</div>';
					unset($_SESSION['blad_haslo2']);
				}
			?>
		<b> Dane do wysyłki: </b> <p> 
		
		Imię: 		  <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_imie'])) 
							{
								echo $_SESSION['fr_imie'];
								unset($_SESSION['fr_imie']);
							}
							?>"
							
						name = "imie" /> <br/>
			<?php
				if(isset($_SESSION['blad_imie'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_imie'].'</div>';
					unset($_SESSION['blad_imie']);
				}
			?>
		
		Nazwisko: 	  <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_nazwisko'])) 
							{
								echo $_SESSION['fr_nazwisko'];
								unset($_SESSION['fr_nazwisko']);
							}
							?>"
							
						name = "nazwisko" /> <br/>
			<?php
				if(isset($_SESSION['blad_nazwisko'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_nazwisko'].'</div>';
					unset($_SESSION['blad_nazwisko']);
				}
			?>
		
		Ulica: 		  <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_ulica'])) 
							{
								echo $_SESSION['fr_ulica'];
								unset($_SESSION['fr_ulica']);
							}
							?>"
							
						name = "ulica" /> <br/>
			<?php
				if(isset($_SESSION['blad_ulica'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_ulica'].'</div>';
					unset($_SESSION['blad_ulica']);
				}
			?>
		
		Numer lokalu: <br/><input type = "number" value = "<?php 
							
							if(isset($_SESSION['fr_nr_budynku'])) 
							{
								echo $_SESSION['fr_nr_budynku'];
								unset($_SESSION['fr_nr_budynku']);
							}
							?>"
							
						name = "nr_budynku" /> <br/>
			<?php
				if(isset($_SESSION['blad_numer_budynku'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_numer_budynku'].'</div>';
					unset($_SESSION['blad_numer_budynku']);
				}
			?>
		
		Kod pocztowy: <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_kod_pocztowy'])) 
							{
								echo $_SESSION['fr_kod_pocztowy'];
								unset($_SESSION['fr_kod_pocztowy']);
							}
							?>"
							
						name = "kod_pocztowy" /> <br/>
			<?php
				if(isset($_SESSION['blad_kod'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_kod'].'</div>';
					unset($_SESSION['blad_kod']);
				}
			?>
		
		Miejscowość:  <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_miejscowosc'])) 
							{
								echo $_SESSION['fr_miejscowosc'];
								unset($_SESSION['fr_miejscowosc']);
							}
							?>"
							
						name = "miejscowosc" /> <br/>
			<?php
				if(isset($_SESSION['blad_miejscowosc'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_miejscowosc'].'</div>';
					unset($_SESSION['blad_miejscowosc']);
				}
			?>
		
		Województwo:  <br/><input type = "text" value = "<?php 
							
							if(isset($_SESSION['fr_wojewodztwo'])) 
							{
								echo $_SESSION['fr_wojewodztwo'];
								unset($_SESSION['fr_wojewodztwo']);
							}
							?>"
							
						name = "wojewodztwo" /> <br/>
			<?php
				if(isset($_SESSION['blad_wojewodztwo'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_wojewodztwo'].'</div>';
					unset($_SESSION['blad_wojewodztwo']);
				}
			?>
		
		E-mail: 	  <br/><input type = "email" value = "<?php 
							
							if(isset($_SESSION['fr_email'])) 
							{
								echo $_SESSION['fr_email'];
								unset($_SESSION['fr_email']);
							}
							?>"
							
						name = "email" /> <br/>
			<?php
				if(isset($_SESSION['blad_email'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_email'].'</div>';
					unset($_SESSION['blad_email']);
				}
			?>
		
		Telefon:	  <br/><input type = "number" value = "<?php 
							
							if(isset($_SESSION['fr_telefon'])) 
							{
								echo $_SESSION['fr_telefon'];
								unset($_SESSION['fr_telefon']);
							}
							?>"
							
						name = "telefon" /> <br/>
			<?php
				if(isset($_SESSION['blad_telefon'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_telefon'].'</div>';
					unset($_SESSION['blad_telefon']);
				}
			?>

<!-- Regulamin: -->			
		<label>
			<br/> <input type = "checkbox" name = "regulamin"<?php
				
							if(isset($_SESSION['fr_regulamin'])) 
							{
								echo "checked";
								unset($_SESSION['fr_regulamin']);
							}
							?>
			
			/> Akceptuję regulamin
		</label>
			<?php
				if(isset($_SESSION['blad_regulamin'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_regulamin'].'</div>';
					unset($_SESSION['blad_regulamin']);
				}
			?>

<!-- Captcha: -->		
		<p><div class="g-recaptcha" data-sitekey="6Ld4BzgUAAAAAAaWQwcT6dQNrH_AdONdkMiNAdZt"></div>
			<?php
				if(isset($_SESSION['blad_recaptcha'])) 
				{
					echo '<div class = "blad">'.$_SESSION['blad_recaptcha'].'</div>';
					unset($_SESSION['blad_recaptcha']);
				}
			?>
		
		<br/> <input type = "submit" value = "Utwórz konto" />
		
	</form>
	
</body>
</html>	   

<?php
///////////////////////////////////////////FUNKCJE /////////////////////////////////////
	function SzukajCyfry(string $ciag)						// jezeli w ciagu znajduje sie cyfra zwraca false
	{
		$i = 0;
		for($i = 0; $i < strlen($ciag); $i++)
		{
			if(($ciag{$i} >= 0) && ($ciag{$i} <= 9)) return false;
		}
		return true;
	} 	
	
	
	function SprawdzKodPocztowy(string $ciag)
	{
		if(preg_match("/^([0-9]{2})(-[0-9]{3})?$/i",$ciag)) return true;
		else return false; 
	}
	
	
	function PolskiWzorzec(string $ciag)
	{
		$wzor = '/^[a-zA-ZąęćżźńłóśĄĆĘŁŃÓŚŹŻ\s]+$/';
		
		if(preg_match($wzor,$ciag) == true) return true;
		else return false;
	}	

	?>