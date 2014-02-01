<?php
class database_helper {
	public $database;
	public $server;
	public $db_user;
	public $db_pass;
	public $link;
	
	public function __construct(){
			$database = "linkpage";  // the name of the database.
			$server = "localhost";  // server to connect to.
			$db_user = "root";  // mysql username to access the database with.
			$db_pass = "";  // mysql password to access the database with.
			$link = mysql_connect($server, $db_user, $db_pass);
			mysql_select_db($database,$link);
		}

    public function close(){
        mysql_close($this->link);
    }

    //Übergebe die URL und den User(Falls vorhanden)
	public function insert_url($url, $user){
			$subquery = "(SELECT id FROM personen WHERE benutzer = '".$user."')";
            $query = "INSERT INTO seiten (url,sucher) VALUES ('".$url."',".$subquery.");";
			//$query = "INSERT INTO seiten (url) VALUES ('".$url."');";
			mysql_query($query);
    }
	//Füge ein neues Wort in die Datenbank ein!
	public function insert_word($word, $link){
			$query = "SELECT aufrufe FROM woerter WHERE wort = '".utf8_decode($word)."';";
			$qry = mysql_query($query);
			$num_rows = mysql_num_rows($qry); 
			if($num_rows == 0){
				$query = "INSERT INTO woerter (wort,link) VALUES ('".utf8_decode($word)."','".utf8_decode($link)."');";
				mysql_query($query);
			}
			else {
				//Nicht sicher ob das so funktioniert!
				$query = "UPDATE woerter SET aufrufe = aufrufe + 1 WHERE wort = '".utf8_decode($word)."'";
				mysql_query($query);
			}
    }
	//Frage ab ob ein Wort in der Datenbank existiert, wenn ja, dann update den Counter
	public function get_word($word){
            $query = "SELECT * FROM woerter WHERE wort = '".$word."';";
			$qry = mysql_query($query);
			//Wenn Ergebnis > 1 dann Ergebnis gefunden
			$num_rows = mysql_num_rows($qry); 
			if($num_rows > 0){
				//Update counter um 1, da Wort bereits existiert
				$query = "UPDATE woerter SET aufrufe = aufrufe + 1 WHERE wort = '".$word."';";
				mysql_query($query);
				//Gebe Ergebnis aus
				while ($row = mysql_fetch_assoc($qry)) {
					return $row['link'];
				}
			}
			//Ansonsten gebe leeren String zurück
			else {
				return "";
			}
    }
    
    public function get_words($tokens){
        //suche alle Referenzen
        $query = "SELECT * FROM woerter WHERE (";
        foreach ($tokens as $token) {
            $query .= "wort = '".$token."' OR ";
        }
        $query = substr($query, 0, count($query)-5).')';
        $qry = mysql_query($query);
        $num_rows = mysql_num_rows($qry);
        
        $serialize = array();
        if($num_rows > 0){
            while ($row = mysql_fetch_object($qry)) {
                $serialize[] = $row;
            }
        }
        
        //aufrufe von allen aktualisieren
        if($num_rows > 0){
            $query = "UPDATE woerter SET aufrufe = aufrufe + 1 WHERE (";
            foreach ($tokens as $token) {
                $query .= "wort = '".$token."' OR ";
            }
            $query = substr($query, 0, count($query)-5).')';
            
            $qry = mysql_query($query);
        }
        
        //json array zurückgeben
        return json_encode($serialize);
    }
    
	//Aktiviert ein Wort für die Suche
	public function word_activate($word) {
		$query = "UPDATE woerter SET anzeige = 1 WHERE wort = '".$word."';";
		mysql_query($query);
	}
	//Deaktiviert ein Wort für die Suche
	public function word_deactivate($word) {
		$query = "UPDATE woerter SET anzeige = 0 WHERE wort = '".$word."';";
		mysql_query($query);
	}
	//Macht einen normalen Nutzer zum Admin
	public function admin_activate($benutzer) {
		$query = "UPDATE personen SET isAdmin = 1 WHERE benutzer = '".$benutzer."';";
		mysql_query($query);
	}
	//Löscht einen Benutzer
	public function user_delete($benutzer) {
		$query = "DELETE FROM personen WHERE benutzer = '".$benutzer."';";
		mysql_query($query);
	}
	
	//Zeigt die meist gesuchten Wörter
	public function show_common_words() {
		$query = "SELECT wort,aufrufe FROM woerter ORDER BY aufrufe DESC LIMIT 10;";
		$qry = mysql_query($query);
		$result = "";
			while ($row = mysql_fetch_array($qry, MYSQL_NUM)) {
				//printf("%s, %s <br/>", $row[0], $row[1]);
				$result .= "$row[0], $row[1]";
				$result .= "<br/>";
			}
		return $result;
	}
	
	//Zeigt die aktivsten User
	public function show_active_users() {
		$query = "SELECT Personen.Benutzer, count(*) AS haeufigkeit
					FROM Seiten, Personen
					WHERE Seiten.sucher IS NOT NULL AND Seiten.sucher = Personen.id
					GROUP BY benutzer
					ORDER BY count(*) desc
					LIMIT 10;";
		$qry = mysql_query($query);
		$result = "";
			while ($row = mysql_fetch_array($qry, MYSQL_NUM)) {
				//printf("%s, %s <br/>", $row[0], $row[1]);
				$result .= "$row[0], $row[1]";
				$result .= "<br/>";
			}
		return $result;
	}
	
	//Zeigt meist betrachtete Internetseiten an
	public function show_most_viewed_url() {
		$query = " 	SELECT url, count(*) AS haeufigkeit
					 FROM Seiten
					 GROUP BY url
					 ORDER BY count(*) desc
					 LIMIT 10;";
		$qry = mysql_query($query);
		$result = "";
			while ($row = mysql_fetch_array($qry, MYSQL_NUM)) {
				//printf("%s, %s <br/>", $row[0], $row[1]);
				$result .= "$row[0], $row[1]";
				$result .= "<br/>";
			}
		return $result;
	}
	
	//Zeigt die letzten Seiten des Users an
	public function show_users_last_url($user) {
		$subquery = "(SELECT id FROM personen WHERE benutzer = '".$user."')";
		$query = " 	SELECT url
					 FROM Seiten
					 WHERE sucher=".$subquery."
					 ORDER BY viewDate desc
					 LIMIT 10;";
		$qry = mysql_query($query);
		$result = "";
			while ($row = mysql_fetch_array($qry, MYSQL_NUM)) {
				//printf("%s, %s <br/>", $row[0], $row[1]);
				$result .= "$row[0]";
				$result .= "<br/>";
			}
		return $result;
	}
}

?>
