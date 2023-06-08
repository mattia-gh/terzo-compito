<?php session_start(); ?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
      <style>
      td, tr{
        text-align: center;
      }
      </style>
      <title>elenco scommesse</title>
    </head>
<body>
<form method="POST" action="<?php $_SERVER['PHP_SELF']?>">
<p>FILTRA PER SPORT:
<select name="sports">
  <option value="" selected disabled hidden>SELEZIONA</option>
  <option value="CALCIO">CALCIO</option>
  <option value="BASKET">BASKET</option>
  <option value="TENNIS">TENNIS</option>
</select>
<input type="submit" value="Invio" name="invio">
<input type="submit" value="Torna alla lista" name="invio">
<input type="submit" value="Aggiungi scommesse" name="invio">
<input type="submit" value="Elimina scommesse" name="invio">
<input type="submit" value="Scommetti" name="invio">
</p>

<table style="border-collapse: collapse; border: 3px solid;"
       border="1"
       cellspacing="3"
	     cellpadding="5"
       summary="ELENCO EVENTI">
<caption style="color: olive; font-style: oblique; font-weight: bold">ELENCO EVENTI</caption>

<thead>
 <tr>
  <th style="width: 20%; border: 3px solid;">SPORT</th>
  <th style="border: 3px solid;">EVENTO</th>
  <th style="border: 3px solid;">LUOGO</th>
  <th style="border: 3px solid;">DATA e ORA</th>
  <th style="border: 3px solid;">ESITO</th>
 </tr>
</thead>

<tbody>
<?php

$xmlString = "";
foreach ( file("sport.xml") as $node ) {
	$xmlString .= trim($node);
}

$doc = new DOMDocument();
if (!$doc->loadXML($xmlString)) {
  die ("Error mentre si andava parsando il documento\n");
}


if (isset($_SESSION['sport'])) {
  $scelta=$_SESSION['sport'];
}

if (isset($_POST['invio'])) {
    if (($_POST['invio'])=="Invio") {
      if (empty($_POST['sports'])) {
        echo "<p style=\"color: red;\">SCEGLI LO SPORT !!!</p>";
      }
      else {
        $scelta=$_POST['sports'];
        $_SESSION['sport']=$_POST['sports'];
      }
    }
    if ($_POST['invio']=="Torna alla lista") {
      unset($_SESSION['sport']);
      header("Location: sport.php");
    }
    if ($_POST['invio']=="Aggiungi scommesse") {
      $records2 = $doc->documentElement->childNodes;
      for ($i=0; $i<$records2->length; $i++) {
        if(isset($_POST["segno".$i])) {
          $record2 = $records2->item($i);
          $sport2 = $record2->firstChild;
          $evento2 = $sport2->nextSibling;
        	$eventoName2 = $evento2->textContent;
          $_SESSION['carrello'][]=$eventoName2.": " .$_POST["segno".$i];
        }
      }
    }
    if ($_POST['invio']=="Scommetti" && !empty($_SESSION['carrello'])) {
      echo "<p style=\"color: blue;\">SCOMMESSA EFFETTUATA !!!</p>";
      unset($_SESSION['carrello']);
    }
    if ($_POST['invio']=="Elimina scommesse" && !empty($_SESSION['carrello'])) {
      unset($_SESSION['carrello']);
    }
}

if (isset($_SESSION['carrello'])) {
  echo "<strong>ELENCO SCOMMESSE AGGIUNTE: </strong><br />\n";
  foreach ($_SESSION['carrello'] as $k=>$v) {
    echo "[" .$k ."] " .$v ."<br />";
  }
}
else {
  echo "- nessuna scommessa aggiunta -<br />";
}


$records = $doc->documentElement->childNodes;

for ($i=0; $i<$records->length; $i++) {
  $record = $records->item($i);

	$sport = $record->firstChild;
	$sportName = $sport->textContent;

	$evento = $sport->nextSibling;
	$eventoName = $evento->textContent;

  $luogo = $evento->nextSibling;
  $luogoName = $luogo->textContent;

  $data = $luogo->nextSibling;
  $dataValue = $data->textContent;

	$ora = $record->lastChild;
	$oraValue = $ora->textContent;

  if (isset($scelta)) {
    if (strpos($sportName, $scelta)!== false) {
      if ($scelta=="CALCIO" || $scelta=="BASKET") {
        print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center; 25px;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"X\">X</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2</td></tr></tbody></table></td></tr>\n";
      }
      if ($scelta=="TENNIS") {
        print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center; 25px;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2</td></tr></tbody></table></td></tr>\n";
      }
    }
  }
  else {
    if (strpos($sportName, "CALCIO")!== false || strpos($sportName, "BASKET")!== false) {
      print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td style=\"border: 3px solid;\"><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center; 25px;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"X\">X</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2</td></tr></tbody></table></td></tr>\n";
    }
    if (strpos($sportName, "TENNIS")!== false) {
      print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td style=\"border: 3px solid;\"><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center; 25px;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2</td></tr></tbody></table></td></tr>\n";
    }
  }
}
//print_r($_SESSION);
?>
</tbody>
</table>
</form>

</body></html>
