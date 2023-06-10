Componente: Mattia Bove
Indirizzo repository: https://github.com/mattia-gh/terzo-compito.git

Per l'esercizio ho preso spunto da "temperature.2.xml" in "lweb-part10-XML2.pars2".

Nell'esercizio si vuole sperimentare l'uso di XML con DOM. Ho usato dtd.

Nel compito ho voluto simulare una "ipotetica" pagina di un sito di scommesse.
All' apertura di "sport.php" vengono elencati una serie di eventi(calcio, basket e tennis) ottenuti da "sport.xml".
Attraverso una selezione è possibile filtrare gli eventi per sport(pulsante Filtra). Poi è possibile
tornare alla lista originale o aggiungere alla "schedina"(tabella a destra) le scommesse selezionate
tramite radio button premendo il bottone Aggiungi Scommesse. L'evento e il segno vengono inseriti in $_SESSION['carrello'] mentre la
rispettiva quota in $_SESSION['quota']. Una volta visualizzate le scommesse nella tabella di destra è possibile o rimuoverle tramite 
checkbox e premendo il bottone Elimina Scommesse(in fondo tabella dx), o scommettere.
Per scommettere bisogna selezionare un importo(viene aggiornata la vincita), inserire un nome utente(alert se non vengono inseriti) e 
premere il bottone Scommetti(in fondo tabella dx).
Una volta premuto il bottone i dati(utente, evento segno e quota, vincita) vengono inseriti in "scommesse.xml" e viene stampato un messaggio di conferma in blu.