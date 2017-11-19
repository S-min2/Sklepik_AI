<?php
	session_start();	
	
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

	
	if(isset($_POST['imie']))		// byle jaka zmienna - czy formularz przeslany?
	{
		$formularz_OK = true;
		
		$imie = $_POST['imie'];
		$nazwisko = $_POST['nazwisko'];
		$ulica = $_POST['ulica'];
		$nr_budynku = $_POST['nr_budynku'];
		$kod_pocztowy = $_POST['kod_pocztowy'];
		$miejscowosc = $_POST['miejscowosc'];
		$wojewodztwo = $_POST['wojewodztwo'];
		$email = $_POST['email'];
		$telefon = $_POST['telefon'];
	

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


// Zapamiętaj dane w formularzu
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
		
				
// POPRAWNIE WYPEŁNIONY FORMULARZ !!! 
		if($formularz_OK == true) 
		{
			$dane_wysylki['imie'] 		   =  $_POST['imie'];
			$dane_wysylki['nazwisko'] 	   =  $_POST['nazwisko']; 
			$dane_wysylki['ulica'] 	   =  $_POST['ulica'];
			$dane_wysylki['nr_budynku']   =  $_POST['nr_budynku'];
			$dane_wysylki['kod_pocztowy'] =  $_POST['kod_pocztowy']; 
			$dane_wysylki['miejscowosc']  =  $_POST['miejscowosc']; 
			$dane_wysylki['wojewodztwo']  =  $_POST['wojewodztwo'];
			$dane_wysylki['telefon']	   =  $_POST['telefon'];
			$dane_wysylki['email'] 	   =  $_POST['email'];
			
			$_SESSION['dane_wysylki'] = $adres;
			header("Location: podsumowanie.php");
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
	
					<br/> <input type = "submit" value = "Użyj tego adresu" />
</form>
</body>	