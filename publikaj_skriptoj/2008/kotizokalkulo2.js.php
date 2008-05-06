<?php

header("Content-Type: text/javascript");



?>


  window.onload = function() {
		var elementoj = document.getElementsByName('pagokvanto');
		for(var i = 0 ; i < elementoj.length; i++)
		{
			elementoj[i].onchange = sxangxu_kotizon;
		}
		sxangxu_kotizon();
	}

	function sxangxu_kotizon()
	{
//		alert("sxangxu_kotizon(), this=" + this);
		var kvanto;
		if (this.tagName && this.tagName.toLowerCase() == 'select')
		{
//			alert("this.tagName: " + this.tagName);
			kvanto = this.value;
		}
		else
		{
//			alert("this.tagName: " + this.tagName);
			var elementoj = document.getElementsByName('pagokvanto');
			for(var i = 0 ; i < elementoj.length; i++)
			{
//				alert("e[i].tagName: " + elementoj[i].tagName);
				if (elementoj[i].tagName.toLowerCase() == 'select')
				{
					kvanto = elementoj[i].value;
				}
			}
		}
		switch(kvanto)
		{
		case 'ne':
			document.getElementById('kotizonun').style.display = 'none';
			document.getElementById('kotizosurloke').style.display = 'block';
			document.getElementById('restassurloke').style.display = 'none';
			document.getElementById('kotizokalkulo').className = 'videbla';
		break;
		case 'cxio':
			document.getElementById('kotizonun').style.display = 'block';
			document.getElementById('kotizosurloke').style.display = 'none';
			document.getElementById('restassurloke').style.display = 'none';
			document.getElementById('kotizokalkulo').className = 'videbla';
			
		break;
		case 'antaux':
			document.getElementById('kotizonun').style.display = 'block';
			document.getElementById('kotizosurloke').style.display = 'none';
			document.getElementById('restassurloke').style.display = 'block';
			document.getElementById('kotizokalkulo').className = 'duona';
		break;
		default:
//			alert("ne funkcias:" + kvanto);
		}
	}
