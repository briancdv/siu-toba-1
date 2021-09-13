<?php
class contexto_ejecucion extends toba_contexto_ejecucion
{
	function conf__inicial()
	{
	   toba::acciones_js()->encolar("var js = document.createElement('script');
                                    js.async;
                                    js.src = 'https://www.googletagmanager.com/gtag/js?id=UA-114205604-4';
                                    var js2 = document.createElement('script');
                                    js2.text = 'window.dataLayer = window.dataLayer || [];'+
                                               'function gtag(){dataLayer.push(arguments);}'+
                                               'gtag(\'js\', new Date());'+
                                               'gtag(\'config\', \'UA-114205604-7\');';
                                                  
                                    document.getElementsByTagName('head')[0].appendChild(js);
                                    document.getElementsByTagName('head')[0].appendChild(js2);
                                    "
                                   );
	}

}

?>