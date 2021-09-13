<?php
class ci_prestamo extends libros_ci
{
	//-------------------Reporte Prestamo
	protected $s__datos_filtro;
	protected $s__moroso;
	//---- Cuadro -----------------------------------------------------------------------
	function actualizar_dias_retraso(){
		$dts=toba::db()->consultar("SELECT id_prestamo,fecha_venc ,devolucion ,DATE_PART('day',current_date::timestamp- fecha_venc::timestamp) as retraso FROM curlib.prestamo where (devolucion ='No') AND fecha_venc < current_date ");
		
		$longitud=count($dts);
		for($i=0; $i<$longitud;$i++){	
			$datos=$dts[$i];
			
			$id=$datos['id_prestamo'];
			$dias=abs($datos['retraso']);
			

			
				toba::db()->consultar("UPDATE curlib.prestamo SET dias_retraso=$dias where id_prestamo= $id");
			}
		}
		
			
	function get_list($where=''){
		
		$sql=("SELECT id_prestamo,t_pe.cuil_documento,t_p_n.apyn,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
		FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
		cidig.persona  as t_pe ,
		cidig.persona_natural as t_p_n
		where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND $where;");
		return toba::db()->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		toba::db()->consultar("UPDATE curlib.prestamo  SET devolucion='No' where (devolucion is null)");
		$this->actualizar_dias_retraso();
	
		if (isset($this->s__datos_filtro)) {
			$filtro=$this->dep('fil_prestamo')->get_sql_where();
			
			$cuadro->set_datos($this->get_list($filtro));
		}elseif (isset($this->s__moroso)) {
			
			$cuadro->set_datos($this->s__moroso);
		}
		 else {
			$sql=("SELECT id_prestamo,t_pe.cuil_documento,t_p_n.apyn,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
			FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
			cidig.persona  as t_pe ,
			cidig.persona_natural as t_p_n
			where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona);");
		$datos=toba::db()->consultar($sql);
			$cuadro->set_datos($datos);
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		
		$this->dep('datos')->cargar($datos);
	}

	

	//-----------------------------------------------------------------------------------
	//---- fi_prestamo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_edicion(toba_ei_pantalla $pantalla)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf_evt__cuadro__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- fil_prestamo -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fil_prestamo(libros_ei_filtro $filtro)
	{
		if(isset($this->s__datos_filtro)){
			$filtro->set_datos($this->s__datos_filtro);
				}
	}

	function evt__fil_prestamo__filtrar($datos)
	{
		
			$this->s__datos_filtro=$datos;
		
			
	}

	function evt__fil_prestamo__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	function evt__cuadro__ordenar($columna, $sentido)
	{
	}

	function evt__fil_prestamo__prestamoVenc()
	{
		
	
		$sql="SELECT id_prestamo,t_pe.cuil_documento,t_p_n.apyn,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
		FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
		cidig.persona  as t_pe ,
		cidig.persona_natural as t_p_n
		where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND(t_p.devolucion ='No') AND t_p.fecha_venc < current_date ";
		$datos =toba::db()->consultar($sql);
		
		if(count($datos) > 0 ){
			return	$this->s__moroso =$datos;
		}else{
			
		 $this->informar_msg('No hay morosos en el dia de la fecha','info');

	
		
}

}	
			
	

}
?>