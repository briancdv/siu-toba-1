<?php
class abm_paraAndrea extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
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
	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');

	}

	function evt__cancelar()
	{
		$this->set_pantalla('pant_inicial');
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar();
		$this->set_pantalla('pant_inicial');
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_paraAndrea ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_paraAndrea(libros_ei_cuadro $cuadro)
	{
		if(isset($this->s__filtro)){
			$filtro=$this->dep('f_persona')->get_sql_where();
			$datos=$this->get_list($filtro);

			$cuadro->set_datos($datos);
			

		}else{
			$cuadro->desactivar_modo_clave_segura();	
		$sql="SELECT apyn,
fe_nac,

ciudad_nac,
provincia_nac 
from cidig.persona_natural 


limit 10";
		$datos=toba::db()->consultar($sql);
		$cuadro->set_datos($datos);
	}
}
	function evt__cuadro_paraAndrea__seleccion($datos)
	{
		
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	function conf_evt__cuadro_paraAndrea__seleccion(toba_evento_usuario $evento, $fila)
	{

	}

}
?>