<?php

////////////////////////////// FUNKCJE INDEXU ////////////////////////////
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
				echo "<h2>".$wiersz['MODEL']."</h2>";
				echo "<h3> Cena: ".$wiersz['CENA']." zł</h2>";
				
				$index = $wiersz['MODEL'];
				foreach(PobierzZdjeciaProduktu($index) as $zdjecie)
				{
					echo "<a rel ='lightbox[$index]' href='FOTY/$zdjecie'>";		// <a href="images/image-2.jpg" data-lightbox="roadtrip">Image #2</a>
					echo "<img src=FOTY/mini/".$zdjecie.">";
					echo "</a>";
				}
				
				echo "<h4> Parametry: ".$wiersz['PARAMETRY']."</h4>";
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
			$sciezka = "FOTY/".$nazwa;	 
			if(file_exists($sciezka))
			{
				$zdjecia[] = $nazwa;
			}
		}
		
		return $zdjecia;
	}
	
///////////////////// FUNKCJE REJESTRACJI ////////////////////
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