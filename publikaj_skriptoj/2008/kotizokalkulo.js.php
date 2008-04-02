<?php

header("Content-Type: text/javascript");

// truko, por ke la lininumeroj en la erarmesagxoj denove estu gxustaj.
// la vokojn algxustigu, kiam vi sxangxas ion en la HTML-kodo.
function aldonu_liniojn($nombro)
{
    echo str_repeat("\n", $nombro);
}

aldonu_liniojn(12);
?>
/** kotizo-kalkulado por la 51a IS (en la aligxilo) */



   var komencodato = new Date(2007, 12, 27);
var aligxtempokategorio = '<?php
// TODO: sxangxu por sekva jaro
if (time() <= mktime(23, 59, 59, 8, 27, 2007)) // fino de 27a de auxgusto
    echo "tre_frua";
 else if (time() <= mktime(23, 59, 59, 10, 31)) // fino de oktobro
     echo "frua";
 else/*  if (time() <= mktime(23, 59,59, 12, 20)) // fino de 20a de decembro*/
     echo "kutima";
 ?>'; <?php aldonu_liniojn(9); ?>


	window.onload = function() {
		var elektiloj = document.getElementsByTagName("select");
		for (var e = 0; e < elektiloj.length; e++) {
			elektiloj[e].onchange = rekalkulu_kotizon;
		}
		rekalkulu_kotizon();
	};

function rekalkulu_kotizon()
{
//	alert("rekalkulos kotizon ...");
	var kotizo = kalkulu_kotizon(aligxtempokategorio);
	var kotizocxelo = document.getElementById('kotizokalkulo');
   var kotizokampo = document.getElementById('kotizocifero');
	while(kotizokampo.firstChild)
		kotizokampo.removeChild(kotizokampo.firstChild);
	if (! kotizo)
	{
		kotizocxelo.className = 'nevidebla';
		return;
	}
	else
	{
		kotizocxelo.className = 'videbla';
		kotizokampo.appendChild(document.createTextNode(kotizo +
									/* ' ' + euro */ " \u20AC"));
	}
}

function donuSelectLauxNomo(nomo)
{
	var listo = document.getElementsByName(nomo);
	for(var i = 0; i < listo.length; i++)
	{
		if (listo[i].tagName.toLowerCase() == 'select')
		{
			return listo[i];
		}
	}
	return null;
}

function eltrovu_partoprentempon()
{
	var de_kampo = donuSelectLauxNomo('de');
	var gxis_kampo = donuSelectLauxNomo('gxis');
	return (kreu_daton(gxis_kampo.value).getTime() -
	        kreu_daton(de_kampo.value).getTime()) / (1000 * 60 * 60 * 24);
}

function kreu_daton(cxeno)
{
   // 01234567890
	//  jjjj-mm-dd
	return new Date(cxeno.substring(0,4), // jaro
						 cxeno.substring(5,7), // monato
						 cxeno.substring(8,10) // tago
					   );
}


function kalkulu_kotizon(tempokategorio)
{
	var partoprentagoj = eltrovu_partoprentempon();
	var baza_kotizo = kalkulu_bazan_kotizon(tempokategorio);
	if (!baza_kotizo)
	{
		return null;
	}

	if (partoprentagoj == 0)
	{
		return 20;
	}
	if (partoprentagoj < 0)
	{
		return "???";
	}
	if (partoprentagoj < 7)
	{
		// auf halbe Euros runden
		return Math.floor(baza_kotizo/3.0 * partoprentagoj)/2.0;
	}
	else
		return baza_kotizo;
}

function eltrovu_landokategorion()
{
	var landokampo = donuSelectLauxNomo('lando');
//	alert("landokampo: " + landokampo);
	var landoIndex = landokampo.selectedIndex;
	if (landoIndex < 0)
		return null;
//	alert("landoIndex: " + landoIndex + ", Typ " + typeof(landoIndex));
	var landonomo = landokampo.options[landoIndex].firstChild.data;

//	alert("landonomo: " + landonomo + ", Typ " + typeof(landonomo));
	return landonomo.charAt(landonomo.length - 2);
}

function eltrovu_naskigxdaton()
{
	var jarokampo = donuSelectLauxNomo('jaro');
	var monatokampo = donuSelectLauxNomo('monato');
	var tagokampo = donuSelectLauxNomo('tago');
	if ((jarokampo.value != "-#-#-") &&
		  (monatokampo.value != "-#-#-") &&
		  (tagokampo.value != "-#-#-"))
	{
//		alert("jaro: " + jarokampo.value + ", monato: " + monatokampo.value +
//			   ", tago: " + tagokampo.value + ".")
		return new Date(jarokampo.value*1, monatokampo.value*1, tagokampo.value*1);
	}
	return null;

}


function eltrovu_logxtipon()
{
	return donuSelectLauxNomo('domotipo').value;
}


function kalkulu_bazan_kotizon(tempokategorio)
{
	var lando = eltrovu_landokategorion();
	if (lando != 'A' && lando != 'B' && lando != 'C')
	{
//		alert("Lando: " + lando);
		return null;
	}
	var naskigxdato = eltrovu_naskigxdaton();
	if(!naskigxdato)
		return null;
	var logxtipo = eltrovu_logxtipon();
	
	var agxo = (komencodato.getTime() - naskigxdato.getTime()) / (1000 * 60 * 60 * 24 * 365);

//	alert("Lando: " + lando + ", naskigxdato: " + naskigxdato +
//			", logxtipo: " + logxtipo + ", agxo: " + agxo + ".");

	if (logxtipo == 'J')
	{
        switch(tempokategorio)
            {
            case 'tre_frua':
                if (agxo < 18)
                    {
                        switch(lando)
                            {
                            case 'A': return 100;
                            case 'B': return 80;
                            case 'C': return 70;
                            }
                    }
                else if (agxo < 22)
                    {
                        switch(lando)
                            {
                            case 'A': return 140;
                            case 'B': return 120;
                            case 'C': return 100;
                            }
                    }
                else if (agxo < 27)
                    {
                        switch(lando)
                            {
                            case 'A': return 175;
                            case 'B': return 145;
                            case 'C': return 125;
                            }
                    }
                else if (agxo < 36)
                    {
                        switch(lando)
                            {
                            case 'A': return 230;
                            case 'B': return 190;
                            case 'C': return 170;
                            }
                    }
                else // super 35
                    {
                        switch(lando)
                            {
                            case 'A': return 250; 
                            case 'B': return 210;
                            case 'C': return 185;
                            }
                    }
                // fino 'tre_frua'
                break;
            case 'frua':
                if (agxo < 18)
                    {
                        switch(lando)
                            {
                            case 'A': return 110;
                            case 'B': return 95;
                            case 'C': return 80;
                            }
                    }
                else if (agxo < 22)
                    {
                        switch(lando)
                            {
                            case 'A': return 150;
                            case 'B': return 130;
                            case 'C': return 115;
                            }
                    }
                else if (agxo < 27)
                    {
                        switch(lando)
                            {
                            case 'A': return 185;
                            case 'B': return 155;
                            case 'C': return 140;
                            }
                    }
                else if (agxo < 36)
                    {
                        switch(lando)
                            {
                            case 'A': return 240;
                            case 'B': return 200;
                            case 'C': return 180;
                            }
                    }
                else
                    {
                        switch(lando)
                            {
                            case 'A': return 260; 
                            case 'B': return 220;
                            case 'C': return 195;
                            }
                    }
                // fino 'frua'
            case 'kutima':
                // malfruaj plenpagantoj
                if (agxo < 18)
                    {
                        switch(lando)
                            {
                            case 'A': return 130;
                            case 'B': return 110;
                            case 'C': return 95;
                            }
                    }
                else if (agxo < 22)
                    {
                        switch(lando)
                            {
                            case 'A': return 185;
                            case 'B': return 155;
                            case 'C': return 140;
                            }
                    }
                else if (agxo < 27)
                    {
                        switch(lando)
                            {
                            case 'A': return 215;
                            case 'B': return 185;
                            case 'C': return 160;
                            }
                    }
                else if (agxo < 36)
                    {
                        switch(lando)
                            {
                            case 'A': return 270;
                            case 'B': return 230;
                            case 'C': return 200;
                            }
                    }
                else
                    {
                        switch(lando)
                            {
                            case 'A': return 290; 
                            case 'B': return 250;
                            case 'C': return 220;
                            }
                    }
            }
	}
	else // memzorgantoj
        {
            switch(tempokategorio)
                {
                case 'tre_frua':
                    if (agxo < 18)
                        {
                            switch(lando)
                                {
                                case 'A': return 15;
                                case 'B': return 8;
                                case 'C': return 5;
                                }
                        }
                    else if (agxo < 22)
                        {
                            switch(lando)
                                {
                                case 'A': return 35;
                                case 'B': return 20;
                                case 'C': return 10;
                                }
                        }
                    else if (agxo < 27)
                        {
                            switch(lando)
                                {
                                case 'A': return 50;
                                case 'B': return 30;
                                case 'C': return 15;
                                }
                        }
                    else if (agxo < 36)
                        {
                            switch(lando)
                                {
                                case 'A': return 60;
                                case 'B': return 40;
                                case 'C': return 20;
                                }
                        }
                    else
                        {
                            switch(lando)
                                {
                                case 'A': return 75; 
                                case 'B': return 50;
                                case 'C': return 25;
                                }
                        }
                    break;
                    // fino 'tre frua'
                case 'frua':
                    if (agxo < 18)
                        {
                            switch(lando)
                                {
                                case 'A': return 20;
                                case 'B': return 15;
                                case 'C': return 10;
                                }
                        }
                    else if (agxo < 22)
                        {
                            switch(lando)
                                {
                                case 'A': return 40;
                                case 'B': return 25;
                                case 'C': return 17;
                                }
                        }
                    else if (agxo < 27)
                        {
                            switch(lando)
                                {
                                case 'A': return 55;
                                case 'B': return 40;
                                case 'C': return 22;
                                }
                        }
                    else if (agxo < 36)
                        {
                            switch(lando)
                                {
                                case 'A': return 70;
                                case 'B': return 45;
                                case 'C': return 28;
                                }
                        }
                    else
                        {
                            switch(lando)
                                {
                                case 'A': return 80; 
                                case 'B': return 50;
                                case 'C': return 33;
                                }
                        }
                    // fino 'frua'
                case 'kutima':
                    // malfruaj plenpagantoj
                    if (agxo < 18)
                        {
                            switch(lando)
                                {
                                case 'A': return 25;
                                case 'B': return 19;
                                case 'C': return 12;
                                }
                        }
                    else if (agxo < 22)
                        {
                            switch(lando)
                                {
                                case 'A': return 45;
                                case 'B': return 33;
                                case 'C': return 20;
                                }
                        }
                    else if (agxo < 27)
                        {
                            switch(lando)
                                {
                                case 'A': return 65;
                                case 'B': return 45;
                                case 'C': return 25;
                                }
                        }
                    else if (agxo < 36)
                        {
                            switch(lando)
                                {
                                case 'A': return 80;
                                case 'B': return 60;
                                case 'C': return 33;
                                }
                        }
                    else
                        {
                            switch(lando)
                                {
                                case 'A': return 95; 
                                case 'B': return 65;
                                case 'C': return 38;
                                }
                        }
                }
	}
	return -2;
}