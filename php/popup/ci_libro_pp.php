<?php
class ci_libro_pp extends libros_ci
{
	protected $s__datos_filtro;
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_inicial(toba_ei_pantalla $pantalla)
	{
	}

	function libro_disponibles()
	{
		$dts=toba::db()->consultar("SELECT libro_id
		FROM curlib.prestamo as t_p
		where ((t_p.devolucion is null) OR t_p.devolucion = 'No' ) 
		
	");		
		$longitud=count($dts);
		for($i=0; $i<$longitud;$i++)
		{

			$datos=$dts[$i];
			
			$id=$datos['libro_id'];
		
			
			}
		}
	
	//-----------------------------------------------------------------------------------
	//---- cd_libro_pp ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_list($mientras='')
	{
		$sql = "SELECT
		t_l.id_libro,
		t_l.titulo,
		t_l.resumen,
		t_a.nombre as id_autor_nombre,
		t_e.nombre as id_editorial_nombre,
		t_l.estante,
		t_es.descripcion as id_estado_nombre,
		t_ad.descripcion as id_adquisicion_nombre,
		t_l.isbn,
		t_l.ejemplar,
		t_l.anio,
		t_g.descripcion as id_genero_nombre

		FROM
		curlib.libro as t_l LEFT OUTER JOIN cidig.estado as t_es ON (t_l.id_estado = t_es.id_estado),
		curlib.autor as t_a,
		curlib.editorial as t_e,
		curlib.adquisicion as t_ad,
		curlib.genero as t_g
		WHERE 
			t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero AND t_l.id_estado= 1	AND $mientras		
			
	ORDER BY titulo";
		
		
		return toba::db('libros')->consultar($sql);
	}
	function conf__cd_libro_pp(libros_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro=$this->dep('fi_libros')->get_sql_where();
			$cuadro->set_datos($this->get_list($filtro));
		} else {
		$cuadro->desactivar_modo_clave_segura();	
		
		$rs = toba::db()->consultar("SELECT
		t_l.id_libro,
		t_l.titulo,
		t_l.resumen,
		t_a.nombre as id_autor_nombre,
		t_e.nombre as id_editorial_nombre,
		t_l.estante,
		t_es.descripcion as id_estado_nombre,
		t_ad.descripcion as id_adquisicion_nombre,
		t_l.isbn,
		t_l.ejemplar,
		t_l.anio,
		t_g.descripcion as id_genero_nombre
		

		FROM
		curlib.libro as t_l LEFT OUTER JOIN cidig.estado as t_es ON (t_l.id_estado = t_es.id_estado),
		curlib.autor as t_a,
		curlib.editorial as t_e,
		curlib.adquisicion as t_ad,
		curlib.genero as t_g
		WHERE 
			t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero AND t_l.id_estado= 1			
			
	ORDER BY titulo");
		if(count($rs) > 0){
				$cuadro->set_datos($rs);
		}
	}
	}

	function evt__cd_libro_pp__seleccion($seleccion)
	{
	}

	function conf_evt__cd_libro_pp__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- fi_libros --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_libros(libros_ei_filtro $filtro)
	{
		if(isset($this->s__datos_filtro)){
			$filtro->set_datos($this->s__datos_filtro);
		}
		
	}

	function evt__fi_libros__filtrar($datos)
	{
		$this->s__datos_filtro=$datos;
	}

	function evt__fi_libros__cancelar()
	{
		unset($this->s__datos_filtro);
	}

}
?>