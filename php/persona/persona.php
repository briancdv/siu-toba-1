<?php
class persona extends libros_ci
{
//	-------Reporte persona
	protected $s__filtro= null;
	protected $s__id=0;
	protected $s__prestamo_filtro;
	//-----------------------------------------------------------------------------------
	//---- fi_persona -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_persona($id){
		$rs = toba::db()->consultar("
			SELECT persona_id FROM curlib.prestamo WHERE persona_id = ".$id);
			$valor=0;
		if(count($rs) > 0 ){
			$valor = $rs[0]['persona_id'];
		}

		return $valor;

	}
	function conf__fi_persona(libros_ei_filtro $filtro)
	{
		
		if(isset($this->s__filtro)){
			$filtro->set_datos($this->s__filtro);
	
		}
	}

	function evt__fi_persona__filtrar($datos)
	{
		
		$this->s__filtro=$datos;
	}

	function evt__fi_persona__cancelar()
	{
		unset($this->s__filtro);
	}

	//-----------------------------------------------------------------------------------
	//---- cd_persona -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_list($where=''){
		$sql=("SELECT p.id_persona, p.cuil_documento, p_n.apyn as id_nombre_persona,email,e.descripcion as id_estado_nombre,p_n.ciudad_nac,t_n.descripcion as id_nombre_nac
		FROM cidig.persona as p  JOIN cidig.persona_natural as p_n ON (p_n.id_persona = p.id_persona),
		cidig.estado as e,
		cidig.nacionalidad as t_n
		WHERE  p.id_estado = e.id_estado AND e.id_estado = 1 AND p.id_persona_tipo= 1 AND p.id_nacionalidad_d = t_n.id_nacionalidad   AND $where
		limit 20;");
		
		return toba::db()->consultar($sql);
	}
	function conf__cd_persona(libros_ei_cuadro $cuadro)
	{
		                                                                                                                              
		if(isset($this->s__filtro)){
			$filtro=$this->dep('fi_persona')->get_sql_where();
		
			$datos=$this->get_list($filtro);
			$cuadro->set_datos($datos);
			

		}else{
			$cuadro->desactivar_modo_clave_segura();
		$rs = toba::db()->consultar("SELECT p.id_persona, p.cuil_documento, p_n.apyn as id_nombre_persona,email,e.descripcion as id_estado_nombre,p_n.ciudad_nac,t_n.descripcion as id_nombre_nac
		FROM cidig.persona as p  JOIN cidig.persona_natural as p_n ON (p_n.id_persona = p.id_persona),
		cidig.estado as e,
		cidig.nacionalidad as t_n
		WHERE  p.id_estado = e.id_estado AND e.id_estado = 1 AND p.id_persona_tipo= 1 AND p.id_nacionalidad_d = t_n.id_nacionalidad  
		limit 20");
		if(count($rs) > 0){
			 $cuadro->set_datos($rs);

		}
	}

}
	function evt__cd_persona__seleccion($seleccion)
	{
		if($this->get_persona($seleccion['id_persona']) == 0){
			$this->pantalla()->agregar_notificacion('No tiene prestamos realizados','error');

		}else{
		$this->set_pantalla('pant_cuadro_prestamo');
		$this->s__id=$seleccion['id_persona'];
		
		}
	}

	function conf_evt__cd_persona__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_prestamo --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_list_prestamo($where=''){
		$id=$this->s__id;
		$sql=("SELECT id_prestamo,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
		FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
		cidig.persona  as t_pe ,
		cidig.persona_natural as t_p_n
		where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND t_pe.id_persona = $id  AND $where;");
		return toba::db()->consultar($sql);
	}
	function conf__cuadro_prestamo(libros_ei_cuadro $cuadro)
	{
		$id=$this->s__id;
	
		$cuadro->desactivar_modo_clave_segura();
		if(isset($this->s__prestamo_filtro)){
			$filtro=$this->dep('fi_prestamo')->get_sql_where();
			
			$cuadro->set_datos($this->get_list_prestamo($filtro));
		}else{

		
		$rs = toba::db()->consultar("SELECT  id_prestamo,t_pe.cuil_documento,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
		FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
		cidig.persona  as t_pe ,
		cidig.persona_natural as t_p_n
		where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND  t_pe.id_persona = $id ");
		$cuadro->set_datos($rs);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__cancelar = function()
		{
		}
		";
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__cancelar()
	{
		$this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- fi_prestamo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_prestamo(libros_ei_filtro $filtro)
	{
		if(isset($this->s__prestamo_filtro)){
			$filtro->set_datos($this->s__prestamo_filtro);
		}
	}

	function evt__fi_prestamo__filtrar($datos)
	{
		$this->s__prestamo_filtro=$datos;
	}

	function evt__fi_prestamo__cancelar()
	{
		unset($this->s__prestamo_filtro);
	}

}
?>