<?php
$from; $to;

function haku() {

if( isset($_GET["from"]) && isset($_GET["to"]) ) {
    $reitti =$_GET["reitti"];
    if($reitti == "walking"){
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$_GET["from"]."&destination=".$_GET["to"]."&mode=walking";

        }
        else{
        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$_GET["from"]."&destination=".$_GET["to"]."&mode=driving";

            }
    $html = file_get_contents($url);
    $html = json_decode($html, true);


    $status = $html['status'];
    if ($status == "OK"){


    $GLOBALS['from'] = $html['routes'][0]['legs'][0]['start_address'];
    $GLOBALS['to'] = $html['routes'][0]['legs'][0]['end_address'];
    $duration = $html['routes'][0]['legs'][0]['duration']['text'];

    $duration = preg_replace(array('/\bhours\b/','/\bhour\b/','/\bmins\b/','/\bmin\b/','/\bdays\b/','/\bday\b/'),array('tuntia','tunti','minuuttia','minuutti','päivää','päivä'),$duration);

    $distance = $html['routes'][0]['legs'][0]['distance']['text'];

    $distance2 = $html['routes'][0]['legs'][0]['distance']['value'];
    $duration2 = $html['routes'][0]['legs'][0]['duration']['value'];

    $aika = round(($distance2/1000)/(($duration2/60)/60));

    if($reitti == "walking") $reitti2 = "Kävelen";
    if($reitti == "driving") $reitti2 = "Autolla";

    //echo "Valittu: ". $_GET["reitti"] ." reitti<br>";
    echo "<br>Reitti osoiteesta: ". $GLOBALS['from'] . "<br />";
    echo "osoiteeseen: ". $GLOBALS['to'] . " <br>". $reitti2 ." kestää ".$duration." ja matkan pituus on " . $distance;
    echo "<br>Laskenta: eli tunnissa pitäisi ajaa noin ". $aika. "km";
    echo "<br><a href='https://www.google.com/maps/dir/?api=1&origin=". $GLOBALS['from'] ."&destination=". $GLOBALS['to'] ."&travelmode=". $reitti ."' target='_blank'>Avaa reitti google mapissä</a>";


    }
    else{
        echo("Jotain meni väärin...");

    }
    if (isset($GLOBALS['from']) && isset($GLOBALS['to'])){


      if(isset($_COOKIE['his'])) {
       $i = 1;
      foreach ($_COOKIE['his'] as $his => $value) {
       $i++;
      }
        SetCookie( "his[".$i."]",$GLOBALS['from'] ." --> ".$GLOBALS['to']." <b><a href='https://www.google.com/maps/dir/?api=1&origin=". $GLOBALS['from'] ."&destination=". $GLOBALS['to'] ."&travelmode=". $reitti ."' target='_blank'>".$distance."</a></b>");
       }
       else { SetCookie( "his[1]", $GLOBALS['from'] ." --> ".$GLOBALS['to']." <b><a href='https://www.google.com/maps/dir/?api=1&origin=". $GLOBALS['from'] ."&destination=". $GLOBALS['to'] ."&travelmode=". $reitti ."' target='_blank'>".$distance."</a></b>");}

    }
 }
}

function historia(){


     if (isset($_COOKIE['his'])) {
      foreach ($_COOKIE['his'] as $hiss => $value) {
          echo  $value ." <br>";
      }
     }

}

function lahdekoodi($file)
{
	header("Content-Disposition: attachment; filename=".basename($file).";");

	@readfile($file);
	exit(0);
}

if( isset($_POST["lahdekoodi"]) ) { lahdekoodi("index.php");}
if( isset($_POST["clear"]) ) {
  if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
    header("Refresh:0");
  }
}
?>

<!DOCTYPE html>
<html>
  <head>

    <title>Reitti haku</title>

  </head>
  <body>
    <p><b>Laske reitin kesto ja pituus</b></p>


    <form action="<?php $_PHP_SELF ?>" method="get">

      <input type="text" id="from" name="from" required="required" placeholder="Mistä, esim: lahti" size="30" />
      <br>

      <input type="text" id="to" name="to" required="required" placeholder="Mihin, esim: helsinki" size="30" />
      <br>

      <input type="radio" name="reitti" value="driving" checked> Autolla<br>
      <input type="radio" name="reitti" value="walking"> Kävelen<br>


      <input type="submit" />
      <input type="reset" />
    </form><br>
    <?php haku(); ?>
    <br><br>Haku historia: <br><?php historia(); ?><br>
    <form action="<?php $_PHP_SELF ?>" method="post">
    <input type="submit" name="clear" value="Tyhjennä" />
</form><br>
<form action="<?php $_PHP_SELF ?>" method="post">
    <input type="submit" name="lahdekoodi" value="Lataa lähdekoodi" />
  </body>
</html>
<?php
phpinfo();
?>
