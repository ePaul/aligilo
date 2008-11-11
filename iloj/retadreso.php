<?php

  /**
   * Kasxiloj por retposxtadresoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2003-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */




  /**
   * Por kontrauxspama kasxado de retadresoj ...
   */
class Kasxilo {

  ////////////// internaj Variabloj //////////////////

  /** interna kalkulilo, kiu kalkulas
   *  cxiun retadreson en la pagxo, por
   *  ke cxiu havu apartan numeron.
   */
  var $numero = 1;

  /** simbolo, kiun ni uzas en la
   * sen-jxavoskripta versio por @.
   */
  var $cxe_simbolo;

  /**
   * Defauxlta servilo - uzata, kiam
   * oni ne donis servilnomon.
   */
  var $def_servilo;

  ////////////////// Konstruilo /////////////////////////

  /**
   * Konstruilo.
   *
   * @param string $simbolo - la anstatauxo por "@" en la
   *                           sen-jxavaskripta versio.
   *            Defauxlto estas "(&#265;e)"
   * @param string $servilo - la uzota servilo. La defauxlto dependas
   *                           de la aktuala servilo.
   */
  function Kasxilo($simbolo = "(&#265;e)", $servilo = null)
  {
      if (!$servilo) {
          $servilo = $_SERVER['SERVER_NAME'];
      }

	$this->cxe_simbolo = $simbolo;
	$this->def_servilo = $servilo;
  }


  ///////// La cxefa funkcio  ///////////////////

      /**
       * kreas kaj redonas HTML-kodon por kasxita ligo al $retadreso.
       * @param string $retadreso aux kompleta retposxtadreso inkluzive
       *               de domajnnomo, aux nur la parto antaux @ - tiam
       *               la domajnnomo estos aldonita.
       * @return htmlstring 
       */
  function liguAlInterne($retadreso)
  {
	$adresoj = $this->analizu_adresojn($retadreso);
	$rezulto .= '<span id="retadreso' . $this->numero .'">';
	$sekvaj = FALSE;
	foreach($adresoj as $adreso)
	  {
		if ($sekvaj)
		  {
			/* nur antaux la dua kaj postaj retadreso aldonu komon */
			$rezulto .= ", " ;
		  }
		$rezulto .= $adreso['konto'] . " " . $this->cxe_simbolo . " " . $adreso["servilo"];
		$sekvaj = TRUE;
	  }
	$rezulto .= '</span>';
	$rezulto .= $this->kreu_jxavoskripton($adresoj);
	$this->numero += count($adresoj);
	return $rezulto;
  }

  /**
   * Kreas HTML-ligilon al la retadreso,
   * kiu estas kasxita per Jxavoskripto.
   */
  function liguAl($retadreso)
  {
	echo $this->liguAlInterne($retadreso);
  } // fino de liguAl

  ///////////////// helpaj funkcioj /////////////////////

  /**
   * bla
   * @return array array(konto => ..., servilo => ...)
   */
  function analizuAdreson($adreso)
  {
	list($konto, $servilo) = split("@", $adreso);
	if ($servilo == "")
	  { 
		$servilo = $this->def_servilo; 
	  }
	return array("konto" => $konto, "servilo" => $servilo);
  }


  /**
   * apartigas konto(j)n kaj servilnomo(j)n el retadreso(j)
   * kaj metas en la nomitaj variablojn (kiuj estos array-oj).
   */
  function analizu_adresojn($retadreso)
  {
	$adresoj = split(" *, *",$retadreso);
	$neu = array();
	for ($i = 0; $i < count($adresoj); $i++)
	  {
		$neu[$i] = $this->analizuAdreson($adresoj[$i]);
	  }
	return $neu;
  }

  /**
   * Tiu cxi funkcio redonas la Jxavoskripton, kiu estas printita
   * nur unufoje en la pagxo (cxe la unua retadreso).
   * Gxi normale estas vokita nur unufoje (de kreu_jxavoskripton()).
   */
  function komenca_jxavoskripto()
  {
	$rezulto = <<<SKRIPTFINO
<!-- Adreskasxilo de Pauxlo
	  --><script language="JavaScript" charset="US-ASCII" type="text/javascript" src="http://www.esperanto.de/dej/retadresoj.js"></script>
SKRIPTFINO;
	return $rezulto;
  }


  /**
   * Kreas jxavoskripton por fari la retadreson alklakebla ...
   */
  function kreu_jxavoskripton($adresoj)
  {
	if ($this->numero == 1) /* nur je la unua fojo ni bezonas la korekt-metodojn. */
	  {
		$rezulto = $this->komenca_jxavoskripto();
	  }
	if (count($adresoj) == 1)
	  {
		$rezulto .= <<<FINO
<script language="JavaScript" type="text/javascript">
		  konto[{$this->numero}] = /* jen la konto-nomo */ "{$adresoj[0]['konto']}";
		servilo[{$this->numero}] = /* jen la servilo */ "{$adresoj[0]['servilo']}";
		korektuAdreson({$this->numero});
</script>
FINO;
	  }
	else
	  {
		$rezulto .= '<script language="JavaScript" type="text/javascript">';
		$i = 0;
		foreach($adresoj as $adreso)
		  {
			$rezulto .= '   konto[' .
			  ( $this->numero+$i ) .
			  '] = /* jen la konto-nomo */ "'
			  .
			  $adreso [ 'konto' ]
			  .
			  '";   servilo['
			  . ($this->numero+$i) .
			  '] = /* jen la servilo */ "'
			  . $adreso [ 'servilo' ]
			  .
			  '";';
			$i += 1;
		  }
		$rezulto .= "korektuPlurajnAdresojn(" . $this->numero . ", " . count($adresoj) .
		  ");\n</script>\n";
	  }
	return $rezulto;
  }  // fino de metodo kreu_jxavoskripton


  /**
   * transformas tekston kun aperoj de {{...}}
   * tiel, ke anstatauxe aperas kasxita ligo al ...
   * @param string $teksto
   * @return string
   */
  function transformu_tekston($teksto)
  {
	$rezulto = "";
	$indekso = 0;
	while(true)
	  {
		if (DEBUG)
		  {
			$rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
		  }
		$pos = strpos($teksto, "{{", $indekso);
		if (DEBUG)
		  {
			$rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
		  }
		if ($pos === FALSE)
		  {
			if (DEBUG)
			  {
				$rezulto .= "<!-- Ende, pos === false -->";
			  }
			// ne plu aperas "{{";
			$rezulto .= substr($teksto, $indekso);
			break;
		  }
		$rezulto .= substr($teksto, $indekso, $pos-$indekso);
		if (DEBUG)
		  {
			$rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
		  }
		$fino = strpos($teksto, "}}", $indekso);
		if (DEBUG)
		  {
			$rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
		  }
		if ($fino === false)
		  {
			if (DEBUG)
			  {
				$rezulto .= "<!-- Ende, fino === false -->";
			  }
			// ne okazu!
			$rezulto .= "<strong>ERARO</strong>"
			  . substr($teksto, $pos+2);
			break;
		  }
		$adreso = substr($teksto, $pos+2, $fino - ($pos+2));
		$rezulto .= $this->liguAlInterne($adreso);
		if (DEBUG)
		  {
			$rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
		  }
		$indekso = $fino + 2;
	  }
	return $rezulto;
  }


}  // fino de class Kasxilo




?>
