<?php

  /**
   * ebligas kreadon kaj redaktadon de krompagotipoj.
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');

  session_start();
  malfermu_datumaro();


kontrolu_rajton("teknikumi");


HtmlKapo();

// TODO


HtmlFino();


?>