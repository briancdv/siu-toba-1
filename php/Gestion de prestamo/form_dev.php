<?php
class form_dev extends libros_ei_formulario
{
	function generar_layout()
	{
		$this->generar_html_ef('id_prestamo');
		$this->generar_html_ef('persona_id');
		$this->generar_html_ef('libro_id');
		$this->generar_html_ef('fecha_alta');
		$this->generar_html_ef('plazo');
		$this->generar_html_ef('fecha_venc');
		$this->generar_html_ef('devolucion');
		$this->generar_html_ef('sepa');
		$this->generar_html_ef('fecha_devolucion');
		$this->generar_html_ef('dias_retraso');
	
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__dias_retraso__procesar = function(es_inicial)
		{
		           
			var f1 = this.ef('fecha_venc').get_estado();
				var f2 = this.ef('fecha_devolucion').get_estado();
				var aFecha1 = f1.split('/');
				var aFecha2 = f2.split('/');
				var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]);
				var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]);
				var dif = fFecha2 - fFecha1;
				var dias = Math.floor(dif / (1000 * 60 * 60 * 24));
				if(dias > 0)
				{
				this.ef('dias_retraso').set_estado(dias)
				}else{
					this.ef('dias_retraso').set_estado(0)
				}
		}
		       
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__devolucion__procesar = function(es_inicial)
		{
			if (this.ef('devolucion').get_estado() == 'No')
			
				this.mostrar_bloque(false);

				
			else

			this.mostrar_bloque(true);
			var f =new Date();
			fecha= f.toLocaleDateString();
			this.ef('fecha_devolucion').set_estado(fecha);	
					
		}
		{$this->objeto_js}.mostrar_bloque= function(visible)
			{
				
					this.ef('sepa').mostrar(visible);
					this.ef('fecha_devolucion').mostrar(visible);
					this.ef('dias_retraso').mostrar(visible);
				
				
			}
		
		
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__fecha_devolucion__procesar = function(es_inicial)
		{
			
		}
		//---- Procesamiento de EFs --------------------------------
		
		{$this->objeto_js}.evt__sepa__procesar = function(es_inicial)
		{
		}
		";
	}




}
?>