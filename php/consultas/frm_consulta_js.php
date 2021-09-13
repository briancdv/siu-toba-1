<?php
class frm_consulta_js extends staf_ei_formulario
{
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__id_prioridad__procesar = function(es_inicial)
		{
		    var prioridad = this.ef('id_prioridad').get_estado();

		//---------- VALIDA LA URGENCIA, MODIFICA LOS COLORES -------------
		                document.getElementById('ef_form_258000208_frm_expnota').style.color='white';
		                document.getElementById('ef_form_258000208_frm_expnota').style.fontSize='xx-large';
		                document.getElementById('ef_form_258000208_frm_expnota').style.textAlign='center';
		                document.getElementById('nodo_ef_form_258000208_frm_expnota').style.marginLeft='0px';
		                document.getElementById('nodo_ef_form_258000208_frm_expnota').style.width = '400px';
		                document.getElementById('nodo_ef_form_258000208_frm_expnota').style.height = '40px';

		             if (prioridad == 2)
		             {
		                  document.getElementById('nodo_ef_form_258000208_frm_expnota').style.backgroundColor='#FF0000';
		                  this.ef('nota').set_estado('U R G E N T E');
			 }
		             if (prioridad == 1)
		             {
		                 document.getElementById('nodo_ef_form_258000208_frm_expnota').style.backgroundColor='#666666';
		                 this.ef('nota').set_estado('C O M U N');
		             }
		                 return true;
		}
		";
	}
}
?>