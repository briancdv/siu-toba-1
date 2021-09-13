<?php
class ci_persona_pp extends libros_ci
{
	protected $s__filtro= null;
	
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- fi_persona_pp ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_persona_pp(libros_ei_filtro $filtro)
	{
		if(isset($this->s__filtro)){
			$filtro->set_datos($this->s__filtro);}
	}

	function evt__fi_persona_pp__filtrar($datos)
	{
		
		$this->s__filtro=$datos;
	}

	function evt__fi_persona_pp__cancelar()
	{
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- cd_persona_pp ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_list($where=''){
		$sql=("SELECT p.id_persona, p.cuil_documento, p_n.apyn, p.control_doc
		FROM cidig.persona as p LEFT OUTER JOIN cidig.estado as e ON (p.id_estado = e.id_estado),
		cidig.persona_natural as p_n
		WHERE e.id_estado = 1 AND p_n.id_persona = p.id_persona AND p.id_persona_tipo = 1 AND $where
		limit 20 ;");
		
		return toba::db()->consultar($sql);
	}
	function conf__cd_persona_pp(libros_ei_cuadro $cuadro)
	{
		if(isset($this->s__filtro)){
			$filtro=$this->dep('fi_persona_pp')->get_sql_where();
			$datos=$this->get_list($filtro);

			$cuadro->set_datos($datos);
			

		}
		else{
			$sql=" SELECT p.id_persona, p.cuil_documento, p_n.apyn, p.control_doc
			FROM cidig.persona as p LEFT OUTER JOIN cidig.estado as e ON (p.id_estado = e.id_estado),
			cidig.persona_natural as p_n
			WHERE  p_n.id_persona = p.id_persona AND e.id_estado = 1 AND p.id_persona_tipo= 1
			limit 20 ;";
			$cuadro->desactivar_modo_clave_segura();
		$rs = toba::db()->consultar($sql);
		if(count($rs) > 0){
			 $cuadro->set_datos($rs);
		}
	}
	 
	}

	function evt__cd_persona_pp__seleccion($seleccion)
	{
	}

	function conf_evt__cd_persona_pp__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__agregar = function()
		{
		}";
		
	}


	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__cancelar()
	{
	}

	function evt__agregar()
	{
		
	}

}
?>