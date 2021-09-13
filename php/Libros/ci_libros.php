<?php
class ci_libros extends libros_ci
{
	// Reporte Libros
	//---- Cuadro -----------------------------------------------------------------------
	protected $s__datos_filtro;

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
			t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero	AND $mientras		
			
	ORDER BY titulo ";
		
		
		return toba::db('libros')->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		
		if (isset($this->s__datos_filtro)) {
			$filtro=$this->dep('fi_libro')->get_sql_where();
			$cuadro->set_datos($this->get_list($filtro));
		} else {
			$sql="SELECT
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
				t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero			
				
		ORDER BY titulo ";
			$datos=toba::db()->consultar($sql);
			$cuadro->set_datos($datos);
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			
			$form->set_datos($this->dep('datos')->tabla('libro')->get());
		}
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
	//---- fi_libro ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_libro(libros_ei_filtro $filtro)
	{
		
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__fi_libro__filtrar($datos)
	{
		
		$this->s__datos_filtro =$datos;
	}

	function evt__fi_libro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

}
?>