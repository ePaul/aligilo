<?php

@header("Content-Type: text/css; charset=ISO-8859-1");

echo "\n\n\n\n\n\n"; // por ke la lininumeroj en la erarmesagxoj denove estu gxustaj.

?>

@import "/layout/styly.css";

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
	min-width: 699px;
	border-style: none;
}

table {
    margin-left: auto;
    margin-right: auto;
}

#aligxilo_tabelo td, td {
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
visibility: hidden;
}



#kotizokalkulo.nevidebla p {
display:none;
}


.kotizocifero  {
	font-size: 40pt;
    margin-top: 5pt;
    margin-bottom: 5pt;
    white-space: nowrap;
}

.euxrovaloro {
	font-size: 30pt;
    margin-top: 5pt;
    margin-bottom: 5pt;
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

.elekteblo {
    white-space: nowrap;
    margin-right: 1ex;
}


.mankas {
  background-color: yellow;
}

.nepra {
    border: thin solid yellow;
}

td[colspan="4"] {
 padding:0.5ex;
}


.inta_pasxo {
    white-space: nowrap;
     background-color: lightgray;
     color: black;
     border: thin solid blue;
     margin: 0.5ex;
     padding:0.5ex;
     text-decoration: underline;
}
.aktuala_pasxo {
    white-space: nowrap;
     background-color: blue;
     color: white;
     border: thin solid blue;
     margin: 0.5ex;
     padding:0.5ex;
}

.onta_pasxo {
    white-space: nowrap;
     background-color: lightgray;
     color: black;
     border: thin solid blue;
     margin: 0.5ex;
     padding:0.5ex;
    
}


#aligxilo_tabelo th {
	padding-left: 1ex;
	vertical-align:top;
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

/* mangxmendoj */

.mangxmendilo {
    font-size: 70%;
}

#aligxilo_tabelo .mangxmendilo td {
    text-align: center;
}

#aligxilo_tabelo table.mangxmendilo th {
    font-weight: normal;
}

#aligxilo_tabelo textarea
{
 width: auto;
 height: auto;

}



/** ------ **/

.kontroltabelo td, .kontroltabelo th {
   vertical-align:top;
}

.kontroltabelo th.titolo {
    padding-top: 1em;
}