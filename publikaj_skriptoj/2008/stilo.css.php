<?php

header("Content-Type: text/css");

echo "\n\n\n\n\n\n"; // por ke la lininumeroj en la erarmesagxoj denove estu gxustaj.

?>

/* ni prenu la stilfolion de la IS-pagxaro. */

@import "http://ttt.esperanto.de/wordpress-test/wp-content/themes/internacia-seminario/style.css";


/*
 * poste adaptoj por la aligxilo.
 */

#lingvosxangxiloj {
	text-align: center;
}

#lingvosxangxiloj ul {
	list-style-type: none; 
	margin: 0em; 
	padding: 0em;
/*	text-indent: 0em; */
}

#lingvosxangxiloj ul li {
/*	margin: 0em;
	padding: 0em;
	text-indent: 0em; */
}

#lingvolisto-simpla li {
display: inline;
}



#enhavtabelero{
	text-align: left;
	padding-left: 1em;
	padding-right: 1em;
}

#enhavtabelero th {
	/** en la aligxilo */
	text-align: center;
	font-family: "Courier New", Courier, monospace;
	font-size: large;
	color: #3399FF;
	font-weight: bold;
}

.kotizotabelo {
	margin-left: auto;
	margin-right: auto;
}
.kotizotabelo td {
	text-align:center;
}
.kotizotabelo thead th {
	padding-right: 1ex;
	padding-left: 1ex;
	font-family: "Verdana", "Arial", "Helvetica", sans-serif;
}

td.lingvo a {
    display:block;
    text-align: center;
    margin: auto;
   font-size: 120%;
}


#aligxilo_tabelo {
	width: 699px;
	border-style: none;
}



#aligxilo_tabelo td {
text-align:left;
}

#aligxilo_tabelo td[align="center"] {
text-align: center;
}


#kotizokalkulo {
	text-align: center;
	vertical-align: middle;
}
#kotizokalkulo.nevidebla {
	color: black;
}


.videbla .kotizocifero, .nevidebla .kotizocifero  {
	font-size: 70pt;
}
.duona .kotizocifero {
	font-size: 40pt;
}

td div {
text-align: center;
}

.triona .kotizocifero {
	font-size: 25pt;
}

.duona div , .triona div 
{
	margin-top: 0px;
	margin-bottom: 0px;
}

.maldekstrafluo {
	float:left;
	margin: 0.8em;
}

.dekstrafluo {
	float:right;
	margin: 0.8em;
}


.mankas {
  background-color: yellow;
}


#aligxilo_tabelo th {
	padding-left: 1ex;
}

#aligxilo_tabelo select {
width: auto;
}

/*
#aligxilo_tabelo input[type="text"] {
    width: 45ex;
}
*/

/** la butonoj sub la aligxileroj */


#aligxilo_tabelo button {
/*	background-color: black;
 border-style: none; */
}
#aligxilo_tabelo button img {
	width: 100px;
	height: 40px;
	border-style: none;
}



#aligxilo_tabelo td.maldekstrabutono {
	text-align: left;
}
#aligxilo_tabelo td.dekstrabutono {
	text-align: right;
}



h1 {
	margin-top: 0px;
	margin-bottom:0px;
	font-size: xx-large;
	font-weight: normal;
	text-align: center;
}

/* ul { text-align: left; } */

.grava {
	font-size:large;

}

/** ------ **/

