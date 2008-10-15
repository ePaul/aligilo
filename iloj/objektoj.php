<?php

  /**
   * La objekto-klaso, superklaso por ĉiuj klasoj
   * de datumbazaj objektoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /*
   * La tabelnomoj ĉi tie ĉiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */

  /**
   * La superklaso de ĉiuj niaj klasoj
   * por objektoj en/el la datumbazo.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   */
class Objekto
{

    /**
     * La atributoj de la objekto, por enmeti en
     * aŭ elmeti el la datumbazo(n)
     */
    var $datoj = array();

    /* La nomo de la tabelo, en kiu povus troviĝi la objekto */
    var $tabelnomo;

    /**
     * prenas la enhavon de la objekto el la datumbazo.
     */
    function prenu_el_datumbazo($id="")
    {
        if ($id == "")
            $id = $this->datoj["ID"];
     
        $sql = datumbazdemando("*", $this->tabelnomo, "ID = '$id'");
        $rez = sql_faru($sql);
        $this->datoj = mysql_fetch_assoc( $rez );  
        mysql_free_result($rez);
    }


    /**
     * Konstruilo.
     *
     * Se $id == 0, kreas novan (malplenan) objekton
     * (la strukturon ĝi prenas el la datumbazo),
     * alikaze prenas la jam ekzistan objekton (kun
     * tiu identifikilo) el la datumbazo.
     *
     *  $id - la identifikilo (aŭ 0).
     *  $tn - la (abstrakta) nomo de la tabelo.
     */
    function Objekto($id, $tn)
    {

        $this->tabelnomo = $tn;
        if ($id == 0)
            {
                /* prenu nur la strukturon el la datumbazo */
                $sql = datumbazdemando("*", $tn, "", "",
                                       array("limit" => "1,1"));
                $rezulto = sql_faru($sql);
                for ($i = 0; $i < mysql_num_fields($rezulto); $i++)
                    {
                        $this->datoj[mysql_field_name($rezulto, $i)] = "";
                    }
                mysql_free_result($rezulto);
            }
        else
            {
                /* prenu tutan tabel-linion el la datumbazo */
                $this->prenu_el_datumbazo($id);
            }
    }

    /**
     * montras la objekton kiel
     * HTML-tabelo.
     * uzata por debugado
     */
    function montru()
    {
        echo "<table>";
        foreach($this->datoj AS $nomo => $valoro)
            {
                echo "<tr><td>$nomo</td><td>$valoro</td></tr>";
            }
        echo "</table>";
    }

    /**
     * Kopias el $_POST al la datoj
     * de tiu ĉi objekto (nur tiuj eroj,
     * kiuj jam ekzistas en la datoj, ricevas
     * novan valoron).
     *
     * TODO: Por kio oni bezonas la funkcion?
     *  -> ekzemple por la aliĝatkontrolo/partoprenkontrolo/aliĝilo.
     */
    function kopiu()
    {

        //TODO: Ĉi tie estas iomete  malsekura punkte, sed
        // mi ĝis nun ne trovis pli bonan solvon.
        foreach($_POST AS $nomo => $valoro)
            {
                if ( isset($this->datoj[$nomo]) )
                    {
                        // htmlspecialchars evitas ekzemple
                        // Javascript-injekton,
                        // la alia anstataŭado SQL-injekton.
                        $this->datoj[$nomo] =
                            htmlspecialchars(str_replace("'","`",$valoro),
                                             ENT_NOQUOTES);
                    }
            }
        $this->korektu_kopiitajn();
    }


    /**
     * funkcio vokita de kopiu() post la ŝarĝo de la datumoj.
     * Ĝi povas ŝanĝi datumojn, se necesas.
     *
     * Tiu funkcio faras nenion en Objekto, sed povas esti anstataŭita
     * en subklasoj.
     */
    function korektu_kopiitajn() {
    }


    /**
     * Aldonas objekton al la ĝusta tabelo
     * kaj prenas la ID de tie.
     */
    function kreu()
    {
        //  sql_faru("insert into {$this->tabelnomo} set id='0'");
        aldonu_al_datumbazo($this->tabelnomo, array("id"=>"0"));
        $this->datoj['ID'] = mysql_insert_id();
    }

    /**
     * aldonas la tutan objekton al la datumbazo,
     * kun nova identigilo kaj ĉiuj datoj.
     *
     * Tiu funkciu estu uzata por ĉiu objekto po maksimume unufoje,
     * kaj nur, kiam oni ne antaŭe uzis kreu() aŭ la konstruilon kun ID.
     *
     * (Alikaze la funkcio kreas kopion de la originala objekto en la
     *  datumbazo kun nova ID, kaj ŝanĝas tiun objekton al la kreita.)
     */
    function skribu_kreante()
    {
        $this->datoj['ID'] = 0;
        aldonu_al_datumbazo($this->tabelnomo, $this->datoj);
        $this->datoj['ID'] = mysql_insert_id();
        $this->prenu_el_datumbazo();
    }


    /**
     * aldonas la tutan objekton al la datumbazo,
     * inkluzive de identigilo kaj ĉiuj datoj.
     *
     * Tiu funkcio nur estu uzata, se la objekto ankoraŭ ne ekzistas en la
     * datumbazo.
     */
    function skribu_kreante_kun_ID() {
        aldonu_al_datumbazo($this->tabelnomo, $this->datoj);
        $this->prenu_el_datumbazo();
    }

    /**
     * Aŭ aldonas objekton al la datumbazo, aŭ ŝanĝas jam
     * ekzistantan datumbazan objekton, depende de tio, ĉu ID = 0.
     */
    function skribu_kreante_se_necesas() {
        if ($this->datoj['ID']) {
            $this->skribu();
        }
        else {
            $this->skribu_kreante();
        }
    }

    /**
     * Skribas la objekton al la tabelo,
     * anstataŭante la antaŭan valoron
     * de la atributoj tie.
     */
    function skribu()
    {
	// TODO: traduku!
        if (! EBLAS_SKRIBI)
            return "Datenbank darf nicht ge&auml;ndert werden";
  
        sql_faru($this->sql_eksport());

        // poste ni re-prenos la datojn el la datumbazo, por vidi, kio alvenis.
        $this->prenu_el_datumbazo();
    }

    /**
     * donas SQLan version de tiu objekto.
     *
     * @return sqlstring
     */
    function sql_eksport()
    {
        return datumbazsxangxo($this->tabelnomo,
                               $this->datoj,
                               array("ID" => $this->datoj["ID"]));
    }


    /**
     * donas tradukitan version de iu kampo de tiu cxi objekto.
     *
     * @param string $kamponomo 
     * @param string $lingvo la ISO-kodo de la lingvo.
     */
    function tradukita($kamponomo, $lingvo) {
        $teksto =
            traduku_datumbazeron($this->tabelnomo, $kamponomo,
                                 $this->datoj['ID'], $lingvo);
        if ($teksto)
            return $teksto;
        else
            return "(traduko mankas: (" . $this->datoj[$kamponomo] . "))";
    }




} // objekto




?>
