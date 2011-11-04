<?php

  /**
   * Instalilo por la programo - parto por plenigi kelkajn tabelojn per
   * komencaj datumoj.
   *
   * Depende de INSTALA_MODUSO ni nur printas la SQL-ordonojn por krei la
   * datumojn, aux jam sendas ilin al la datumbazo.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage instalilo
   * @copyright 2008,2010 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */




  /**
   * importas csv-dosieron al freŝe kreita datumbazo.
   *
   * La unua linio estas forĵetota (ĉar ĝi kutime enhavas kamponomojn),
   * la aliajn ni importas.
   *
   * Atentu: La CSV-dosieroj estu tiuj kreitaj de nia CSV-exportaj funkcioj,
   * kaj povas difektiĝi, se ; estas en la kolumnoj. Do unue rigardu la
   * dosierojn antaŭ simple doni ĝin al la funkcio.
   *
   * @param string $dosiernomo nomo de la dosiero.
   * @param string $tabelnomo (abstrakta) nomo de la tabelo.
   * @param array  $kamponomoj listo de la nomo de la kampoj,
   *                en la sinsekvo trovebla en la dosiero.
   */
function importu_csv($dosiernomo, $tabelnomo, $kamponomoj) {
    $dos = fopen($GLOBALS['datumdosierujo'] . $dosiernomo,
                 "r");

    $titoloj = fgetcsv($dos, 250, ';');

    $aldonilo = new SqlAldonilo($kamponomoj, $tabelnomo);

    while($linio = fgetcsv($dos, 250, ';')) {
        $aldonilo->aldonu_linion($linio);
    }
    $aldonilo->faru();
    //    var_export($aldonilo);
}


/**
 * Klaso por aldoni amase valorojn al datumbaztabelo.
 *
 * Tipa uzo estas la jena:
 *<code>
 *    $aldonilo = new SqlAldonilo($kamponomoj, $tabelnomo);
 *
 *    while($linio = ...) {
 *        $aldonilo->aldonu_linion($linio);
 *    }
 *    $aldonilo->faru();
 *</code>
 * 
 * @author Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage instalilo
 * @copyright 2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */
class SqlAldonilo {

    /**
     * komenco de la SQL-ordono.
     * @var sqlstring
     */
    var $komenca_sql;

    /**
     * nombro de kampoj - tiel estu la nombro de enmetaĵoj.
     * @var int
     */
    var $kamponombro;
    
    /**
     * la ĝis nun kolektitaj elementoj por aldoni en
     *  la sekva voko de {@link faru()}
     * @var array
     */
    var $kolektitajxoj;

    /**
     * takso de la grandeco de la SQL-ordono kreita, se oni nun vokus faru().
     * @var int
     */
    var $grandeco;

    /**
     * maksimuma grandeco de SQL-ordono. Antaŭ superi tiun numeron,
     * aŭtomate vokiĝas faru().
     * @var int
     */
    var $limo;

    /**
     * kion ni metas inter la unuopaj linioj.
     * @var string
     */
    var $interliniajxo = ",\n    "; 


    /**
     * konstruilo.
     * @param array $kamponomoj listo de kampoj de la tabelo,
     *          en la sama sinsekvo ili poste aperu en la linioj.
     * @param string $tabelnomo (abstrakta) nomo de la datumbaztabelo
     *      tabelo.
     * @param int $limo  maksimuma grandeco de SQL-ordono en bajtoj.
     *      Ni kreos plurajn ordonojn, po maksimume tiom granda.
     *      (Se unuopa linio estas jam tro granda, ĝi tamen restos
     *       ne-dividita.)
     */
    function SqlAldonilo($kamponomoj, $tabelnomo, $limo=1000)
    {
        $this->kamponombro = count($kamponomoj);
        $this->limo = $limo;
        $this->komenca_sql =
            "INSERT INTO `" . traduku_tabelnomon($tabelnomo) . "`\n" .
            "    (`" . implode('`, `', $kamponomoj) . "`) \n" .
            "  VALUES \n    ";
        $this->reset();
    }
    
    /**
     * forgesas ĉiujn aldonitajn kolumnojn (kaj ties longecon).
     * @access private
     */
    function reset() {
        $this->kolektitajxoj = array();
        $this->grandeco =
            strlen($this->komenca_sql)
            - strlen($this->interliniajxo);
    }

    /**
     * aldonas unu linion al la tabelo.
     * @param array $valoroj la valoroj en la sama sinsekvo
     *              kiel la kamponomoj de la konstruilo.
     */
    function aldonu_linion($valoroj) {
        if (!is_array($valoroj)) {
            $valoroj = func_get_args();
        }
        if (count($valoroj) > $this->kamponombro) {
            $valoroj = array_slice($valoroj, 0, $this->kamponombro);
        }
        else if (count($valoroj) < $this->kamponombro) {
            $valoroj = array_pad($valoroj, $this->kamponombro, null);
        }


        $nova_linio =
            "(" . implode(', ',  array_map('sql_quote', $valoroj)) . ")";

        $len = strlen($nova_linio) + strlen($this->interliniajxo);

        if ($this->limo < $len + $this->grandeco ) {
            $this->faru();
        }
        
        $this->kolektitajxoj []= $nova_linio;
        //        $this->kopioj []= $nova_linio;
        $this->grandeco += $len;
    }

    /**
     * eldonas la restantajn liniojn, kiuj ankoraŭ
     * estas en la memoro.
     * 
     * Faras nenion, se tiaj ne estas.
     */
    function faru() {
        if (count($this->kolektitajxoj)) {
            $sql =
                $this->komenca_sql .
                implode($this->interliniajxo, $this->kolektitajxoj) .
                " ;\n";


            faru_SQL($sql);
            
            $this->reset();
        }
    }

}  // class SqlAldonilo


    /**
     * kondicxoj
     */
function kreu_bazajn_kondicxojn() {

    $aldonilo = new SQLAldonilo(array('ID', 'entajpanto', 'nomo',
                                      'priskribo', 'kondicxoteksto'),
                                'kondicxoj');
    $aldonilo->aldonu_linion(1, 1, "dulita c^ambro",
                             "mendo kaj ricevo de dulita c^ambro",
                             "havas_dulitan_cxambron");
    $aldonilo->aldonu_linion(2, 1, "unulita c^ambro",
                             "mendo kaj ricevo de unulita c^ambro",
                             "havas_unulitan_cxambron");
    $aldonilo->aldonu_linion(3, 1, "invitletero (sub 30)",
                             "sendo de invitletero al sub-30-jarulo",
                             "invitletero_sub30");
    $aldonilo->aldonu_linion(4, 1, "invitletero (ekde 30)",
                             "sendo de invitletero al homo ekde 30 jaroj",
                             "invitletero_ekde30");
    $aldonilo->aldonu_linion(5, 1, "surloka alig^o",
                             "ppanto alig^as nur surloke au^ ne "
                             .  "antau^pagas antau^e",
                             "surloka_aligxo");
    $aldonilo->aldonu_linion(6, 1, "mang^kupono",
                             "ppanto mendis aparte mang^kuponon kaj pro " .
                             "tio devos krompagi",
                             "mangxkupona_krompago");
    // Atentu: la valoro (7) de "cxiuj" estas uzata kelkloke.
    $aldonilo->aldonu_linion(7, 1, "c^iuj",
                             "tiu kondic^o validas c^iam", "true");
    $aldonilo->aldonu_linion(8, 1, "TEJO-membro",
                             "Membro de TEJO – au^ jam antau^e, au^ ig^as surloke",
                             'eno.tejo_membro_kontrolita en { "j", "i" }');
    $aldonilo->aldonu_linion(9, 1, "junulargastejulo",
                             "Log^ado en junulargastejo",
                             'eno.domotipo = "J"');
    $aldonilo->aldonu_linion(10, 1, "amaslog^ejulo",
                             "Log^ado en amaslog^ejo",
                             'eno.domotipo = "M"');
                            
    $aldonilo->aldonu_linion(11, 1, "neniu",
                             "tiu kondic^o validas neniam", "false");
    $aldonilo->faru();
}


function kreu_simplan_kotizosistemon() {

    /**
     * kategorisistemoj
     */

    faru_SQL(datumbazaldono('landokategorisistemoj',
                          array('ID' => 1,
                                'nomo' => "triviala",
                                'entajpanto' => 1,
                                'priskribo' =>
                                "Simpla landkategorisistemo kreita de la ".
                                "instalilo, konsistanta el nur unu kategorio.")
                            ));
    faru_SQL(datumbazaldono('agxkategorisistemoj',
                          array('ID' => 1,
                                'nomo' => "triviala",
                                'entajpanto' => 1,
                                'priskribo' =>
                                "Simpla ag^kategorisistemo kreita de la ".
                                "instalilo, konsistanta el nur unu kategorio.")
                            ));
    faru_SQL(datumbazaldono('aligxkategorisistemoj',
                          array('ID' => 1,
                                'nomo' => "triviala",
                                'entajpanto' => 1,
                                'priskribo' =>
                                "Simpla alig^kategorisistemo kreita de la ".
                                "instalilo, konsistanta el nur unu kategorio.")
                            ));
    faru_SQL(datumbazaldono('logxkategorisistemoj',
                          array('ID' => 1,
                                'nomo' => "triviala",
                                'entajpanto' => 1,
                                'priskribo' =>
                                "Simpla log^kategorisistemo kreita de la " .
                                "instalilo, konsistanta el nur unu kategorio.")
                            ));

    /*
     * kaj nun ĉiu sistemo ricevas po unu kategorion, al kiu apartenu
     * ĉiuj partoprenantoj. 
     */

    faru_SQL(datumbazaldono('landokategorioj',
                           array('ID' => 1,
                                 'nomo' => 'X',
                                 'priskribo' => "c^iuj landoj",
                                 'sistemoID' => 1)));

    $rez = sql_faru(datumbazdemando('ID', 'landoj'));
    $aldonilo = new SqlAldonilo(array('sistemoID', 'landoID', 'kategorioID'),
                                'kategorioj_de_landoj');
    while ($linio = mysql_fetch_assoc($rez)) {
        $aldonilo->aldonu_linion(array(1, $linio['ID'], 1));
    }
    $aldonilo->faru();


    faru_SQL(datumbazaldono('agxkategorioj',
                            array('ID' => 1,
                                  'nomo' => "X",
                                  'priskribo' => "c^iuj ag^oj",
                                  'sistemoID' => 1,
                                  'limagxo' => 300)));
    faru_SQL(datumbazaldono('aligxkategorioj',
                            array('ID' => 1,
                                  'nomo' => "c^iam",
                                  'priskribo' => "c^iuj alig^tempoj",
                                  'sistemoID' => 1,
                                  'limdato' => -20,
                                  'nomo_lokalingve' => "")));
    faru_SQL(datumbazaldono('logxkategorioj',
                            array('ID' => 1,
                                  'nomo' => "c^iuj",
                                  'priskribo' => "c^iuj log^manieroj",
                                  'sistemoID' => 1,
                                  'kondicxo' => 7 /* 7 = cxiuj */
                                  )));

    /**
     * malaliĝkondiĉoj ...
     */

    faru_SQL(datumbazaldono('malaligxkondicxsistemoj',
                          array('ID' => 3,
                                'nomo' => "triviala",
                                'priskribo' =>
                                "Simpla malalig^kondicxosistemo kreita de la ".
                                "instalilo por la triviala alig^kategorisistemo.",
                                'aligxkategorisistemo' => 1)));

    faru_SQL(datumbazaldono('malaligxkondicxoj',
                            array('sistemo' => 1,
                                  'aligxkategorio' => 1,
                                  'kondicxtipo' => 1)));

    /**
     * kotizosistemo
     */

    faru_SQL(datumbazaldono('kotizosistemoj',
                            array('ID' => 1,
                                  'nomo' => 'triviala',
                                  'priskribo' => "simpla kotizosistemo, por"
                                  .              " havi ion por komenci.",
                                  'entajpanto' => 1 /* instalilo */,
                                  'aligxkategorisistemo' => 1,
                                  'landokategorisistemo' => 1,
                                  'agxkategorisistemo'   => 1,
                                  'logxkategorisistemo'  => 1,
                                  'parttempdivisoro'     => 1.0,
                                  'malaligxkondicxsistemo' => 1)));

    faru_SQL(datumbazaldono('kotizotabeleroj',
                            array('kotizosistemo' => 1,
                                  'aligxkategorio' => 1,
                                  'landokategorio' => 1,
                                  'agxkategorio' => 1,
                                  'logxkategorio' => 1,
                                  'kotizo' => 39.0)));

    faru_SQL(datumbazaldono('minimumaj_antauxpagoj',
                            array('kotizosistemo' => 1,
                                  'landokategorio' => 1,
                                  'oficiala_antauxpago' => 17.0,
                                  'interna_antauxpago' => 15.0)));

    /**
     * ankoraux unu pli utila logxkategorisistemo kun du kategorioj
     */

    faru_SQL(datumbazaldono('logxkategorisistemoj',
                            array('ID' => 2,
                                  'nomo' => "J/M",
                                  'entajpanto' => 1,
                                  'priskribo' => "Simpla log^kategorisistemo "
                                  . "kreita de la instalilo, kun kategorioj "
                                  . "junulargastejo kaj memzorgantejo")
                            ));

    faru_SQL(datumbazaldono('logxkategorioj',
                            array('ID' => 2,
                                  'nomo' => "junulargastejo",
                                  'priskribo' => "Loĝado en Junulargastejo, kun plena manĝado.",
                                  'sistemoID' => 2,
                                  'kondicxo' => 9 /* 9 = junulargastejulo */
                                  )));
    faru_SQL(datumbazaldono('logxkategorioj',
                            array('ID' => 3,
                                  'nomo' => "memzorgantejo",
                                  'priskribo' => "Spaco por matraco en la amasloĝejo, sen manĝado (krom la silvestra bufedo).",
                                  'sistemoID' => 2,
                                  'kondicxo' => 10 /* 10 = memzorgantejulo */
                                  )));


}


/**
 * kreas uzanto-konton por la instalilo.
 * Tiu estas uzata por tiuj kategorisistemoj,
 * kiuj bezonas uzanto-id.
 */
function kreu_instalilan_entajpanton() {
    // TODO: provizore neniuj rajtoj.
    faru_SQL(datumbazaldono('entajpantoj',
                           array('ID' => 1,
                                 'nomo' => "instalilo",
                                 'kodvorto' => 'TODO!',
                                 )));
}



/**
 * importas tabelon (nu, ne vere, sed ŝajnigas tion.)
 */
function importu_tabelon($dosiernomo, $tabelnomo, $kamponomoj)
{
    eoecho("<h2>" . $tabelnomo . "</h2>\n");
    echo "<pre>\n";
    importu_csv($dosiernomo, $tabelnomo, $kamponomoj);
    echo "</pre>\n";
}




/**
 * 
 */


$prafix = "..";
require_once($prafix . "/iloj/iloj.php");



function faru_SQL($sql) {
  echo $sql . "\n";
  if (INSTALA_MODUSO) {
    eoecho ("faranta ...");
    flush();
    sql_faru($sql);
    eoecho("farita!\n");
  }
}


malfermu_datumaro();

$GLOBALS['datumdosierujo'] = $prafix . "/instalilo/datumoj/";


HtmlKapo("speciala");

eoecho("<h2>Instalila Entajpanto</h2><pre>\n");

kreu_instalilan_entajpanton();

eoecho("</pre>\n<h2>Kondic^oj</h2>\n<pre>");

kreu_bazajn_kondicxojn();

eoecho( "</pre>");

importu_tabelon("landolisto-is-nur-eo.csv", "landoj",
                array("ID", "nomo", "kodo"));
// importu_tabelon("krompagotipoj.csv", "krompagotipoj",
//                 array("ID", "nomo", "mallongigo", "entajpanto",
//                       "priskribo", "kondicxo", "uzebla", "lauxnokte"));
importu_tabelon("malaligxkondicxotipoj.csv", "malaligxkondicxotipoj",
                array("ID", "nomo", "mallongigo", "priskribo", "funkcio",
                      "parametro", "uzebla"));


eoecho("\n<h2>Simpla kotizosistemo</h2><pre>\n");

kreu_simplan_kotizosistemon();

eoecho("</pre>");

echo "<p>";
ligu("./#instalilo", "Reen al la instalilo-superrigardo");
echo "</p>";


HtmlFino();
