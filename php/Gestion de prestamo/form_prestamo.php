<?php
class form_prestamo extends libros_ei_formulario
{
	function generar_layout()
	{
		$this->generar_html_ef('id_prestamo');
		$this->generar_html_ef('persona_id');
		$this->generar_html_ef('libro_id');
		$this->generar_html_ef('fecha_alta');
		$this->generar_html_ef('plazo');
		$this->generar_html_ef('fecha_venc');
		$this->generar_html_ef('apyn');
		
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__plazo__procesar = function(es_inicial)
		{ 
			
			if(!es_inicial)
			{
				if(this.ef('plazo').get_estado() != '' && this.ef('fecha_alta').get_estado() != '') 
				{
					dts = new Array;
					dts['plazo'] = this.ef('plazo').get_estado();
					dts['fecha_alta'] = this.ef('fecha_alta').get_estado();
					this.controlador.ajax('get_calcula_vto', dts, this, this.datos_vto);
				} 
			}    
			
		}
		{$this->objeto_js}.datos_vto = function(rs)
		{ 
			if(rs != '') 
			{
				this.ef('fecha_venc').set_estado(rs);    
			}
		}
		{$this->objeto_js}.evt__fecha_venc__procesar = function(rs)
		{
			
		}
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__fecha_alta__procesar = function(es_inicial)
		{
			
			
		}
		";
	}


}
?>