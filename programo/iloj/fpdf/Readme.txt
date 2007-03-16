#!/usr/bin/less

La dosierojn fpdf.php kaj updf.php mi kopiis el
la phpmyadmin/libraries/fpdf-dosierujo.

Sxangxoj de mi:
* en ufpdf.php
  alia loko de fpdf.php
* en fpdf.php
-		$l+=isset($cw[ord($c)])?$cw[ord($c)]:0;
+		$l+=isset($cw[($c)])?$cw[($c)]:0;

En la subdosierujo 'tiparoj' estas tiuj tiparoj,
kiujn ni uzas en niaj programoj:

* TEMPO kaj TEMPOD (neunikode)
* FreeSans kaj FreeSansBold (unikode)


Sube estas la originala teksto de la README-dosiero.

    Pauxlo
-------------

The official site for fdpf is http://www.fpdf.org/

This directory contains some files from the fpdf 1.51 distribution.

