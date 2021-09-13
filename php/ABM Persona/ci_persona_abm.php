<?php
class ci_persona_abm extends libros_ci
{
	//------------- ABM Persona
	//---- Cuadro -----------------------------------------------------------------------
	protected $s__filtro= null;
	function get_list($where=''){
		$sql=("SELECT
		
		t_p.id_persona,
		t_pn.apyn,
		t_pt.descripcion as id_descr_tipo,
		t_p.cuil_tipo,
		t_p.cuil_documento,
		t_p.cuil_digito,
		t_p.cuil,
		t_c.descripcion as id_cf_nombre,
		t_p.email,
		t_e.descripcion as id_estado_nombre,
		t_d.descripcion as id_nombre_tipo_doc,
		t_n.descripcion as id_nombre_nac,
		t_p.control_doc,
		t_pn.Ciudad_nac as id_ciudad_nombre,
		t_s.descripcion as id_sexo_nombre
			
	FROM
		cidig.persona as t_p INNER JOIN cidig.nacionalidad as t_n ON (t_p.id_nacionalidad_d = t_n.id_nacionalidad)
		INNER JOIN cidig.persona_tipo as t_pt ON (t_p.id_persona_tipo = t_pt.id_persona_tipo)
		LEFT OUTER JOIN cidig.cond_fiscal as t_c ON (t_p.id_cond_fiscal = t_c.id_cond_fiscal)
		INNER JOIN cidig.estado as t_e ON (t_p.id_estado = t_e.id_estado)
		INNER JOIN cidig.tipo_documento as t_d ON (t_p.id_tipo_documento = t_d.id_tipo_documento)
		INNER JOIN cidig.persona_natural as t_pn ON (t_p.id_persona = t_pn.id_persona)
		LEFT JOIN cidig.sexo as t_s ON (t_s.id_sexo = t_pn.id_sexo)
		WHERE $where
		LIMIT 10;");
		
		return toba::db()->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if(isset($this->s__filtro)){
			$filtro=$this->dep('f_persona')->get_sql_where();
			$datos=$this->get_list($filtro);

			$cuadro->set_datos($datos);
			

		}else{
			$cuadro->desactivar_modo_clave_segura();	
		$sql="SELECT
		
		t_p.id_persona,
		t_pn.apyn,
		t_pt.descripcion as id_descr_tipo,
		t_p.cuil_tipo,
		t_p.cuil_documento,
		t_p.cuil_digito,
		t_p.cuil,
		t_c.descripcion as id_cf_nombre,
		t_p.email,
		t_e.descripcion as id_estado_nombre,
		t_d.descripcion as id_nombre_tipo_doc,
		t_n.descripcion as id_nombre_nac,
		t_p.control_doc,
		t_pn.Ciudad_nac as id_ciudad_nombre,
		t_s.descripcion as id_sexo_nombre
			
	FROM
		cidig.persona as t_p INNER JOIN cidig.nacionalidad as t_n ON (t_p.id_nacionalidad_d = t_n.id_nacionalidad)
		INNER JOIN cidig.persona_tipo as t_pt ON (t_p.id_persona_tipo = t_pt.id_persona_tipo)
		LEFT OUTER JOIN cidig.cond_fiscal as t_c ON (t_p.id_cond_fiscal = t_c.id_cond_fiscal)
		INNER JOIN cidig.estado as t_e ON (t_p.id_estado = t_e.id_estado)
		INNER JOIN cidig.tipo_documento as t_d ON (t_p.id_tipo_documento = t_d.id_tipo_documento)
		INNER JOIN cidig.persona_natural as t_pn ON (t_p.id_persona = t_pn.id_persona)
		LEFT JOIN cidig.sexo as t_s ON (t_s.id_sexo = t_pn.id_sexo)
	
		LIMIT 100;";
		$datos=toba::db()->consultar($sql);
		$cuadro->set_datos($datos);
	}
	}
	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	
		
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		/*if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('persona')->get());
			
		}*/

		if($this->dep('datos')->tabla('persona')->get_cantidad_filas() > 0)
		{
			//ei_arbol($this->dep('datos')->tabla('persona_natural')->get());
			if($this->dep('datos')->tabla('persona')->get_columna('id_persona_tipo') == 1){ #- Caso Persona Natural
				$datos = $this->dep('datos')->tabla('persona')->get() + $this->dep('datos')->tabla('persona_natural')->get();
				list($apellido, $nombre) = explode(',',$datos['apyn']);
				$datos['apellido'] = trim($apellido);
				$datos['nombre'] = trim($nombre);  
				
			}			
			$form->set_datos($datos);
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
		$datos['apyn'] = trim(strtoupper($datos['apellido'])).", ".trim(ucwords(strtolower($datos['nombre'])));
		//$this->dep('datos')->tabla('persona')->set($datos);
		//$this->dep('datos')->tabla('persona_natural')->set($datos);
		$cursor=$this->dep('datos')->tabla('persona')->nueva_fila($datos);	
			
		$this->dep('datos')->tabla('persona')->set_cursor($cursor);
		$this->dep('datos')->tabla('persona_natural')->nueva_fila($datos);	
		$this->dep('datos')->sincronizar();
		$this->resetear();
		$this->informar_msg('La persona se agrego correctamente','info');
		$this->set_pantalla('pant_inicial');
		
		}else
		{
		
			$this->set_pantalla('pant_edicion');
			$this->informar_msg('La persona ya existe','error');
		}
	}

	function evt__formulario__modificacion($datos)
	{
		$datos['apyn'] = trim(strtoupper($datos['apellido'])).", ".trim(ucwords(strtolower($datos['nombre'])));

		
		$this->dep('datos')->tabla('persona')->set($datos);
		$this->dep('datos')->tabla('persona_natural')->set($datos);
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