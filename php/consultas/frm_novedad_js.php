<?php
class frm_novedad_js extends staf_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
        $perfil = toba::perfil_de_datos()->posee_dimension('gestion','staf');
        if($perfil == 1)
        {
            $p = 'gestion';
        }elseif($perfil == ''){
            $p = 'procuracion';
        }
        echo "

		function perfil()
		{
		   var p = ".$p.";
		   return p;
		}
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__n_expediente__procesar = function(es_inicial)
		{
		    if(this.ef('n_expediente').get_estado())
		    {console.log(perfil);
		        var p = perfil();
		        if(p == 1)
		        { //-- si el perfil de datos del usuario es gestion le oculta el campo procurador --//
                    this.ef('procurador').ocultar();
                    document.getElementById('procurador').style.display = 'none';
		        }
		        if(p == ''){
		            this.ef('procurador').mostrar();
                    document.getElementById('procurador').style.display = ' ';
		        }
		    }
		}
		";
	}

}

?>