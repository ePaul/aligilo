/**
 * Jxavo-skripto vokita el cxiu pagxo.
 * @package aligilo
 * @subpackage iloj
 * @author Martin Sawitzki, Paul Ebermann
 * @since revizo 201 (2008-09-01)
 * @version $Id: objekto_sercxilo.php 221 2008-09-11 12:30:58Z epaul $
 * @copyright 2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */
 


/**
 *
 */
function marku(linio)
{
  linio.malnovaKlaso = linio.className;
  linio.className = 'markita';
  return true;
}

function malmarku(linio)
{
  linio.className = linio.malnovaKlaso;
}



// TODO: SetPointer ist aus dem PHPMyAdmin entnommen 
function setPointer(theRow, thePointerColor) 
{ 
  if (typeof(theRow.style) == 'undefined' || typeof(theRow.cells) == 'undefined') { 
    return false; 
  } 
 
  var row_cells_cnt           = theRow.cells.length; 
  for (var c = 0; c < row_cells_cnt; c++) { 
    theRow.cells[c].bgColor = thePointerColor; 
  } 
 
  return true; 
} // end of the 'setPointer()' function 


/**
 * TODO: Cxu ankoraux uzata? Se jes, traduku, se ne, forigu!
 *
 * setzt die ID der in der Auswahlliste im linken Menü markierten
 * Person in das versteckte Feld "kune" des Formulars "peter", bevor dieses
 * abgeschickt wird.
 */

function reindamit() {
  //   var formularo = window.top.frames["is-aligilo-menuo"].elektu;
  //   var listo = window.top.frames["is-aligilo-menuo"].elektu.partoprenantoidento.options;
  //   var listo = formularo.partoprenantoidento.options;
  var listo = window.top.frames["is-aligilo-menuo"].document.
    forms["elektu"].elements["partoprenantoidento"].options;


  for ( var i = 0; i < listo.length; i++ ) 
    { 
      if ( listo[ i ].selected== true ) 
        { 
          window.top.frames["anzeige"].document.forms["peter"].
            elements["kune"].value=listo[i].value;
        } 
    }  
}



/**
 * Kasxas aux malkasxas iun blokon (kun identifikilo), se
 * la elektilo kun nomo estas elektita. Uzu kun {@link jes_ne_bokso()}
 * en iloj_html.php.
 */
function malkasxu(nomo, identifikilo)
{
  var elektiloj = document.getElementsByName(nomo);
  var kasxindajxo = document.getElementById(identifikilo);
  if(elektiloj[1].checked)
    {
      kasxindajxo.style.display = 'block';
    }
  else
    {
      kasxindajxo.style.display = 'none';
    }
}




function doSelect ( idx ) 
{ 
  var listo = window.top.frames["is-aligilo-menuo"].document.forms["elektu"].elements["partoprenantoidento"].options;

  for ( var i = 0; i < listo.length; i++ ) 
    { 
      if ( listo[ i ].value == idx ) 
        { 
          listo[ i ].selected = true; 
        } 
      else 
        { 
          listo[ i ].selected = false; 
        } 
    } 

  window.top.frames["is-aligilo-menuo"].location.hash="elektilo-anker";

} 
 
