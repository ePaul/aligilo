<?php

  /**
   * ebligas kreadon, redaktadon kaj elprovadon de kotizosistemo.
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');

  session_start();
  malfermu_datumaro();

// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");


HtmlKapo();

// TODO


HtmlFino();


?>