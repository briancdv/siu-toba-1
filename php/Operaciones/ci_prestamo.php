<?php
class  ci_prestamo  extends  libros_ci 
{ 
	protected $s__filtro = null;
    protected $s__where = '1=1';
	//----------------------------------------------------------------------------------- 
	//---- Configuraciones -------------------------------------------------------------- 
	//----------------------------------------------------------------------------------- 

	//Defino la relacion de la tablas
	function rel() 
	{  
		return $this->dep('d_prestamo'); 
	} 
	function  conf__pant_inicial ( toba_ei_pantalla  $pantalla ) 
	{ 
	} 

	function  conf__pant_edicion ( toba_ei_pantalla  $pantalla ) 
	{ 
	} 

	//----------------------------------------------------------------------------------- 
	//---- Eventos ---------------------------------------------------------------------- 
	//----------------------------------------------------------------------------------- 

	

	//----------------------------------------------------------------------------------- 
	//---- f_prestamo ------------------------------------------------------------------- 
	//----------------------------------------------------------------------------------- 

	

	//----------------------------------------------------------------------------------- 
	// ---- JAVASCRIPT ------------------------------------------- ------------------------ 
	//----------------------------------------------------------------------------------- 

	function  extender_objeto_js ( ) 
	{ 
		echo " 
		//---- Eventos --------------------------------------------- 
		
		{$this->objeto_js}.evt__agregar = function() 
		{ 
		} 
		
		{$this->objeto_js}.evt__volver_inicio = function() 
		{ 
		} 
		
		{$this->objeto_js}.evt__eliminar = function() 
		{ 
		} 
		
		{$this->objeto_js}.evt__guardar = function() 
		{ 
		} 
		" ; 
	} 

	//Realiza alta de prestamo
	function  evt__f_prestamo__alta ( $datos ) 
	{ 
		
		$this->rel()->tabla('prestamo')->nueva_fila($datos);
		$this->rel()->sincronizar();
		$this->rel()->tabla('prestamo')->resetear();
	} 
	//Realiza baja de prestamo
	function  evt__f_prestamo__baja ( ) 
	{ 
		$this->rel()->tabla('prestamo')->set();
		$this->rel()->sincronizar();
	}	

	//Realiza modificacion de prestamo
	function  evt__f_prestamo__modificacion ( $datos ) 
	{ 
		$this->rel()->tabla('prestamo')->set($datos);
		$this->rel()->sincronizar();
		$this->rel()->tabla('prestamo')->resetear();
	} 

	function  evt__f_prestamo__cancelar() 
	{ 
		$this->rel()->tabla('prestamo')->resetear();
	} 

	//-----------------------------------------------------------------------------------
	//---- fr_prestamo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fr_prestamo(libros_ei_filtro $filtro)
	{
		if(isset($this->s__filtro))	{
			$filtro->set_datos($this->s__filtro);
		}
	}

	function evt__fr_prestamo__filtrar($datos)
	{
		
		$this->s__filtro = $datos;
		//ei_arbol($this->dep('fr_prestamo')->get_sql_where());
		
	}

	function evt__fr_prestamo__cancelar()
	{
		//limpia la variable de sesion filtro

		unset($this->s__filtro);
		$this->s__where = '1=1';
	}

	//-----------------------------------------------------------------------------------
	//---- cd_prestamo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
//Carga de datos al cuadro del formulario de prestamo
	function get_list($where=''){
		$sql=("SELECT id_prestamo,curlib.persona.cuil_documento,curlib.libro.titulo,fecha_alta,plazo,fecha_venc
		FROM curlib.prestamo INNER JOIN curlib.persona ON  curlib.prestamo.persona_id = curlib.persona.id_persona
		INNER JOIN curlib.libro ON  curlib.prestamo.libro_id = curlib.libro.id_libro WHERE $where;");
		
		return toba::db()->consultar($sql);
	}
	
	function conf__cd_prestamo(libros_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)) {
			$filtro=$this->dep('fr_prestamo')->get_sql_where();
			$datos=$this->get_list($filtro);
			$cuadro->set_datos($datos);
   
		 } else {
   
			$sql=("SELECT id_prestamo,curlib.persona.cuil_documento,curlib.libro.titulo,fecha_alta,plazo,fecha_venc
		FROM curlib.prestamo INNER JOIN curlib.persona ON  curlib.prestamo.persona_id = curlib.persona.id_persona
		INNER JOIN curlib.libro ON  curlib.prestamo.libro_id = curlib.libro.id_libro");
	
		$datos= toba::db()->consultar($sql);
		$cuadro->set_datos($datos);
   
		 }
   
	  }
		

	function evt__cd_prestamo__seleccion($seleccion)
	{
		
		$this->rel()->cargar($seleccion);
		$this->rel()->tabla('prestamo')->set_cursor(0);
		$this->set_pantalla('pant_edicion');
	}
	function conf__f_prestamo( libros_ei_formulario $form) 
	{ 
		
		
			
			$form->set_datos($this->rel()->tabla('prestamo')->get());
			
	
			
	
		
	} 
	function conf_evt__cd_prestamo__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf()
	{
	}

	function post_configurar()
	{
	}

	function post_eventos()
	{
	}

}
?>