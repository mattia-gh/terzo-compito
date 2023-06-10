<?php session_start(); ?>
<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
      <script type="text/javascript">
      function visualizzaQuota(q, tot) {
      if (q==0) {
        document.getElementById("quota").innerHTML="-";
      }
      else {
        document.getElementById("quota").innerHTML = (q*tot).toFixed(2) + "&euro;";
      }
      }
      </script>

      <script src="jQuery\jquery-3.7.0.min.js"></script>
      <script>
      $(document).ready(function(){
           $("#scomm").click(function() {
             if (!($("#imp").val())) {
               alert("Inserire importo !");
             }
             if (!($("#ut").val())) {
               alert("Inserire nome utente !");
             }
             return;
           });
       });
      </script>


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
<input type="submit" value="Filtra" name="invio">
<input type="submit" value="Torna alla lista" name="invio">
<input type="submit" value="Aggiungi scommesse" name="invio">
</p>

<?php

$xmlString = "";
foreach ( file("sport.xml") as $node ) {
	$xmlString .= trim($node);
}

$doc = new DOMDocument();
if (!$doc->loadXML($xmlString)) {
  die ("Error mentre si andava parsando il documento\n");
}

$xmlString2 = "";
foreach ( file("scommesse.xml") as $node2 ) {
	$xmlString2 .= trim($node2);
}

$doc2 = new DOMDocument();
if (!$doc2->loadXML($xmlString2)) {
  die ("Error mentre si andava parsando il documento\n");
}

if (isset($_SESSION['sport'])) {
  $scelta=$_SESSION['sport'];
}

if (isset($_POST['invio'])) {
    if (($_POST['invio'])=="Filtra") {
      if (!empty($_POST['sports'])) {
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
          $luogo2 = $evento2->nextSibling;
          $data2 = $luogo2->nextSibling;
          $ora2 = $data2->nextSibling;
          $quota1b = $ora2->nextSibling;
          $quota1bValue=$quota1b->textContent;
          $quotaXb = $quota1b->nextSibling;
          $quotaXbValue=$quotaXb->textContent;
          $quota2b = $record2->lastChild;
          $quota2bValue=$quota2b->textContent;
          $trovato=0;
          if (isset($_SESSION['carrello'])) {
            foreach ($_SESSION['carrello'] as $k=>$v) {
              if(strpos($v, $eventoName2)!== false) {
                $trovato=1;
              }
            }
          }
          if ($trovato==0) {
            $_SESSION['carrello'][]=$eventoName2.": " .$_POST["segno".$i];
            if ($_POST["segno".$i]=="1") {
              $_SESSION['quota'][]=$quota1bValue;
            }
            if ($_POST["segno".$i]=="X") {
              $_SESSION['quota'][]=$quotaXbValue;
            }
            if ($_POST["segno".$i]=="2") {
            $_SESSION['quota'][]=$quota2bValue;
            }
          }
       }
     }
   }

    if ($_POST['invio']=="Scommetti" && !empty($_SESSION['carrello']) && !empty($_POST['utente']) && !empty($_POST['importo'])) {
      $output="<p style=\"color: blue;\">SCOMMESSA EFFETTUATA(dati inseriti in \"scommesse.xml\") !!!</p>";
      $root= $doc2->documentElement;
      $rec = $doc2->createElement('record');
      $utente = $doc2->createElement('utente');
      $utenteNome = $doc2->createTextNode($_POST['utente']);
      $utente->appendChild($utenteNome);
      $rec->appendChild($utente);
      $root->appendChild($rec);
      $vincita=1;
      foreach ($_SESSION['carrello'] as $k => $v) {
          $ev_segn_qt = $doc2->createElement('ev_segn_qt');
          $ev_segn = $doc2->createElement('evento_segno');
          $q = $doc2->createElement('quota');
          $ev_segnNome = $doc2->createTextNode($v);
          $qValue = $doc2->createTextNode($_SESSION['quota'][$k]);
          $ev_segn->appendChild($ev_segnNome);
          $q->appendChild($qValue);
          $ev_segn_qt->appendChild($ev_segn);
          $ev_segn_qt->appendChild($q);
          $rec->appendChild($ev_segn_qt);
          $root->appendChild($rec);
          $vincita=$vincita*$_SESSION['quota'][$k];
          $doc2->save('scommesse.xml');
        }
        $w= $doc2->createElement('vincita');
        $wValue = $doc2->createTextNode($vincita*$_POST['importo']);
        $w->appendChild($wValue);
        $rec->appendChild($w);
        $root->appendChild($rec);
        $doc2->save('scommesse.xml');
        unset($_SESSION['carrello']);
        unset($_SESSION['quota']);
    }
    if ($_POST['invio']=="Elimina scommesse" && !empty($_SESSION['carrello']) && !empty($_POST['scommessa'])) {
      foreach ($_SESSION['carrello'] as $k => $v) {
        foreach ($_POST['scommessa'] as $key => $value) {
          if ($v==$value) {
            unset($_SESSION['carrello'][$k]);
            unset($_SESSION['quota'][$k]);
          }
        }
      }
    }
}
?>

<?php if (isset($output)) {echo $output;} ?>
<table style="width:100%;"><caption style="color: olive; font-style: oblique; font-weight: bold; text-align:left;">ELENCO EVENTI</caption>
<tr>
<td style="width:70%;">
<table style="border-collapse: collapse; border: 3px solid; width:100%;"
       border="1"
       cellspacing="3"
	     cellpadding="5"
       summary="ELENCO EVENTI">


<thead>
 <tr>
  <th style="border: 3px solid;">SPORT</th>
  <th style="border: 3px solid;">EVENTO</th>
  <th style="border: 3px solid;">LUOGO</th>
  <th style="border: 3px solid;">DATA e ORA</th>
  <th style="border: 3px solid;">ESITO</th>
 </tr>
</thead>

<tbody>
<?php

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

	$ora = $data->nextSibling;
	$oraValue = $ora->textContent;

  $segno1 = $ora->nextSibling;
  $segno1Value = $segno1->textContent;

	$segnoX = $segno1->nextSibling;
	$segnoXValue = $segnoX->textContent;

  $segno2 = $record->lastChild;
	$segno2Value = $segno2->textContent;

  if (isset($scelta)) {
    if (strpos($sportName, $scelta)!== false) {
      if ($scelta=="CALCIO" || $scelta=="BASKET") {
        print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1<br />$segno1Value</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"X\">X<br />$segnoXValue</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2<br />$segno2Value</td></tr></tbody></table></td></tr>\n";
      }
      if ($scelta=="TENNIS") {
        print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1<br />$segno1Value</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2<br />$segno2Value</td></tr></tbody></table></td></tr>\n";
      }
    }
  }
  else {
    if (strpos($sportName, "CALCIO")!== false || strpos($sportName, "BASKET")!== false) {
      print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1<br />$segno1Value</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"X\">X<br />$segnoXValue</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2<br />$segno2Value</td></tr></tbody></table></td></tr>\n";
    }
    if (strpos($sportName, "TENNIS")!== false) {
      print "<tr style=\"border: 3px solid;\"><td style=\"border: 3px solid;\">$sportName</td><td style=\"border: 3px solid;\">$eventoName</td><td style=\"border: 3px solid;\">$luogoName</td><td style=\"border: 3px solid;\">$dataValue - $oraValue</td><td><table style=\"border-collapse: collapse;\" align=\"center\" border=\"1\"><thead><tr style=\"padding: 25px;\"><td style=\"text-align: center;\" colspan=\"3\">SEGNO</td></tr></thead><tbody><tr style=\"padding: 25px;\"><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"1\">1<br />$segno1Value</td><td style=\"padding: 25px;\"><input type=\"radio\" name=\"segno$i\" value=\"2\">2<br />$segno2Value</td></tr></tbody></table></td></tr>\n";
    }
  }
}
/*print_r($_SESSION);
echo "<br />";
print_r($_POST);*/
?>
</tbody>
</table>
</td>

<td style="width:30%;" valign="top">
<table style="border-collapse: collapse; border: 3px solid; width:100%;"
       border="1"
       cellspacing="3"
	     cellpadding="5"
       summary="ELENCO SCOMMESSE">

<thead>
 <tr>
  <th colspan="2" style="border: 3px solid;">ELENCO SCOMMESSE</th>
  <th colspan="1" style="border: 3px solid;">QUOTA</th>
 </tr>
</thead>
<tbody>
  <?php
  if (!empty($_SESSION['carrello'])) {
    foreach ($_SESSION['carrello'] as $k=>$v) {
      echo "<tr style=\"border: 1px solid;\"><td style=\"border: 1px solid;\">" ."<input type=\"checkbox\" value=\"$v\" name=\"scommessa[]\">" ."</td>" ."<td style=\"border: 1px solid;\">" .$v ."</td><td  style=\"border: 1px solid;\">" .$_SESSION['quota'][$k] ."</td></tr>";
    }
    if (isset($_SESSION['quota'])) {
      $tot=1;
      foreach($_SESSION['quota'] as $k => $v) {
        $tot=$tot*$v;
      }
    }
    echo "<tfoot><tr><td colspan=\"2\" style=\"border: 1px solid;\">IMPORTO</td><td style=\"border: 1px solid;\"><input id=\"imp\" type=\"number\" name=\"importo\" min=\"1\" onchange=\"visualizzaQuota(this.value, $tot)\"></td></tr>
          <tr><td colspan=\"2\" style=\"border: 1px solid;\">NOME UTENTE</td><td style=\"border: 1px solid;\"><input id=\"ut\" type=\"text\" name=\"utente\"></td></tr>
          <tr><td colspan=\"2\" style=\"border: 1px solid;\">VINCITA POTENZIALE</td><td style=\"border: 1px solid;\"><p id=\"quota\"></p></td></tr>
          <tr><td colspan=\"3\" style=\"border: 1px solid;\"><input type=\"submit\" value=\"Elimina scommesse\" name=\"invio\"> <input id=\"scomm\" type=\"submit\" value=\"Scommetti\" name=\"invio\"></td></tr>
          </tfoot>";
  }
  else {
    echo "<tr  style=\"border: 1px solid;\"><td style=\"border: 1px solid;\">-</td><td style=\"border: 1px solid;\">nessuna scommessa aggiunta</td><td style=\"border: 1px solid;\">-</td></tr>";
  }
  ?>
</tbody>
</table>
</td>
</tr>
</table>


</form>

</body></html>
