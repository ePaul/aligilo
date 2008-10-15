<?php

/**
 * Dokumento por montri la liston de tradukendaj ĉenoj
 * en arba formo ("dosieroj"). Ni uzas la bibliotekon
 * el {@link http://www.treeview.net/} por tio.
 *
 *
 * @author Marcelino Alves Martins (originalo),
 *          adaptoj de Paul Ebermann (lastaj ŝanĝoj), teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright (c) http://www.treeview.net - detalojn rigardu en
 *            la fontoteksto aŭ la kreita paĝo. De la
 *            {@link http://www.treeview.net/} menciita adreso
 *            eblas preni aktualan version.
 */


?><!--
     (Please keep all copyright notices.)
     This frameset document includes the FolderTree script.
     Script found at: http://www.treeview.net
     Author: Marcelino Alves Martins

     Instructions:
     - Do not make any changes to this file outside the style tag.
	 - Through the style tag you can change the colors and types
	   of fonts to the particular needs of your site. 
	 - A predefined block has been made for stylish people with
	   black backgrounds.
-->


<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<style>
body {
	margin: 5px;
	padding: 0;
}

td {
	white-space: nowrap;
}
</style>

<!-- NO CHANGES PAST THIS LINE -->


<!-- Code for browser detection -->
<script src="ua.js"></script>

<!-- Infrastructure code for the tree -->
<script type="text/javascript" src="ftiens4.js"></script>

<!-- Execution of the code that actually builds the specific tree.
     The variable foldersTree creates its structure with calls to
	 gFld, insFld, and insDoc -->
<script type="text/javascript" src="chenlisto_datumoj.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=<?= $montru ?>&random=<?= $random ?>"></script>

</head>

<body>

<!-- By making any changes to this code you are violating your user agreement.
     Corporate users or any others that want to remove the link should check 
	 the online FAQ for instructions on how to obtain a version without the link -->
<!-- Removing this link will make the script stop from working -->
<div style="/* position:absolute; top:0; left:0; */ display: none"><table border=0><tr><td><font size=-2><a style="font-size:7pt;text-decoration:none;color:silver" href=http://www.treeview.net/treemenu/userhelp.asp target=_top>Tree Menu Help</a></font></td></table></div>

<!-- Build the browser's objects and display default view of the 
     tree. -->
<script type="text/javascript">
initializeDocument()
if (window.dosierujoj) {
	for (var i = dosierujoj.length - 1; i >= 0; i--) {
		clickOnNode(dosierujoj[i].id)
	}
}
</script>
<noscript>
A tree for site navigation will open here if you enable JavaScript in your browser.
</noscript>
</html>
