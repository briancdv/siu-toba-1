<?php
class ci_persona_abm extends libros_ci
{
	//------------- ABM Persona
	//---- Cuadro -----------------------------------------------------------------------
	protected $s__filtro= null;
	function get_list($where=''){
		$sql=("SELECT t_p.id_persona, t_p_t.descripcion as id_descr_tipo, cuil_tipo, t_p.cuil_documento, t_p_n.apyn as id_nombre_persona, cuil_digito, 
		cuil, id_cond_fiscal, email, t_e.descripcion as id_estado_nombre, t_t_d.descripcion as id_nombre_tipo_doc, t_n.descripcion as id_nombre_nac, 
		control_doc
   FROM cidig.persona as t_p LEFT OUTER JOIN cidig.estado as t_e ON (t_p.id_estado = t_e.id_estado),
   cidig.persona_tipo as t_p_t,
   cidig.nacionalidad as t_n,
   cidig.tipo_documento as t_t_d,
   cidig.persona_natural as t_p_n
	WHERE 
	t_p_t.id_persona_tipo = t_p.id_persona_tipo AND t_p.id_nacionalidad_d = t_n.id_nacionalidad AND t_p.id_tipo_documento= t_t_d.id_tipo_documento AND t_p_n.id_persona = t_p.id_persona AND $where
   limit 20");
		
		return toba::db()->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if(isset($this->s__filtro)){
			$filtro=$this->dep('f_persona')->get_sql_where();
			$datos=$this->get_list($filtro);

			$cuadro->set_datos($datos);
			

		}else{
			
		$sql="SELECT t_p.id_persona, t_p_t.descripcion as id_descr_tipo, cuil_tipo, t_p.cuil_documento, t_p_n.apyn as id_nombre_persona, cuil_digito, 
		cuil, id_cond_fiscal, email, t_e.descripcion as id_estado_nombre, t_t_d.descripcion as id_nombre_tipo_doc, t_n.descripcion as id_nombre_nac, 
		control_doc
   FROM cidig.persona as t_p LEFT OUTER JOIN cidig.estado as t_e ON (t_p.id_estado = t_e.id_estado),
   cidig.persona_tipo as t_p_t,
   cidig.nacionalidad as t_n,
   cidig.tipo_documento as t_t_d,
   cidig.persona_natural as t_p_n
	WHERE 
	t_p_t.id_persona_tipo = t_p.id_persona_tipo AND t_p.id_nacionalidad_d = t_n.id_nacionalidad AND t_p.id_tipo_documento= t_t_d.id_tipo_documento AND t_p_n.id_persona = t_p.id_persona
   limit 20";
		$datos=toba::db()->consultar($sql);
		$cuadro->set_datos($datos);
	}
	}
	function evt__cuadro__seleccion($datos)
	{
		$this->set_pantalla('pant_edicion');
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->get());
		}
	}
	function get_persona_id($cuil='')
	{
		$rs = toba::db()->consultar("SELECT  id_persona FROM cidig.persona  WHERE cuil_documento  = '$cuil'");
		$valor = '';

		if(count($rs) > 0 ){
			$valor = $rs[0]['id_persona'];
		}

		return $valor;
	}
	function evt__formulario__alta($datos)
	{
		$cuil_doc=$datos['cuil_documento'];
		if($this->get_persona_id($cuil_doc) == ''){
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
		$this->set_pantalla('pant_inicial');
		}else
		{
			$this->set_pantalla('pant_edicion');
			$this->informar_msg('La persona ya existe','error');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__formulario__cancelar()
	{
		
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf_evt__cuadro__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- f_persona --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__f_persona(libros_ei_filtro $filtro)
	{
		if(isset($this->s__filtro)){
			$filtro->set_datos($this->s__filtro);}
	}

	function evt__f_persona__filtrar($datos)
	{
		$this->s__filtro=$datos;
	}

	function evt__f_persona__cancelar()
	{
		unset($this->s__filtro);
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
		}
		
		{$this->objeto_js}.evt__cancelar = function()
		{
		}
		";
	}

}
?>