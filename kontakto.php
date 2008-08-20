<?php

function metu_retadreson($adreso) {
    list($konto, $servilo) = explode('@', $adreso);
    echo "<span class='retadreso'>" . $konto .
        " (ĉe) " . $servilo . "</span>";
}

echo "<?xml version='1.0' encoding='utf-8'?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang='eo' lang='eo'>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'></meta>
<title>Renkontiĝo-Administrilo - kontaktpaĝo</title>
<script src="retadresoj.js" language="Javascript" type="text/javascript" defer="defer">
</script>
</head>
<body>
  <h1>Renkontiĝo-Administrilo - kontaktpaĝo</h1>
<h2>Respondeculo pri la retpaĝoj</h2>
<p>Por la retpaĝoj respondecas Paŭlo Ebermann.
Li estas kontaktebla per <?php metu_retadreson("epaul@users.berlios.de"); ?>.
</p>

</body>
</html>