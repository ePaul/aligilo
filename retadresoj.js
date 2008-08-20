/**
 * retadreso-korektilo (por ebligi komfortan klakadon,
 * dum robotoj dauxre havas problemojn kolekti ilin.)
 */

// alert("retadresoj.js");

function korektu_retadresojn() {
    //  alert("korektu_retadresojn()");
  var adreskampoj = document.getElementsByTagName("span");
  for (var i = 0; i < adreskampoj.length; i++) {
    kampo = adreskampoj[i];
    if (0 <= kampo.className.indexOf("retadreso")) {
      korektu_adreson(kampo);
    }
  }
}

window.onload = korektu_retadresojn;

  function korektu_adreson(kampo){
      //      alert("korektu_adreson():" + kampo);
      kampo.normalize();
      adreso = kampo.firstChild.data;
      var splitter = /^([^ ()]+) \([^\)]+\) (.*)$/;
      teile = adreso.match(splitter);
      adreso = teile[1] + "@" + teile[2];
      ligo = document.createElement("a");
      ligo.setAttribute("href", "mailto:" + adreso);
      ligo.appendChild(document.createTextNode(adreso));
      kampo.replaceChild(ligo, kampo.firstChild);
  }
