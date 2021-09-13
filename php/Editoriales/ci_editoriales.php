<?php
class ci_editoriales extends libros_ci
{
	protected $s__filtro = null;
	protected $s__where = '1=1';

	function rel(){
		return $this->dep('rel_editorial');
	}

	function evt__Nuevo()
	{
		$this->rel()->resetear();
		$this->set_pantalla('pant_edicion');
	}


	function evt__cancelar()
	{
		$this->rel()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	//---- Cuadro - Editoriales
	//------------------------------------
	function conf__cd_editoriales(libros_ei_cuadro $cuadro)
	{
		$sql = "SELECT ed.*,
					CASE WHEN ed.id_estado >= 1
						THEN ee.descripcion
							END AS id_estado_descr
				FROM 
					curlib.editorial AS ed
					LEFT JOIN curlib.estado AS ee ON ee.id_estado = ed.id_estado
				WHERE ".$this->s__where;

		$cuadro->set_datos(toba::db()->consultar($sql));
	}

	function evt__cd_editoriales__seleccion($seleccion)
	{
		$this->rel()->tabla('editorial')->cargar($seleccion);
		$this->rel()->tabla('editorial')->set_cursor(0);
       	$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- fi_editoriales ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_editoriales(libros_ei_filtro $filtro)
	{
		$filtro->set_datos($this->s__filtro);
	}

	function evt__fi_editoriales__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->s__where = $this->dep('fi_editoriales')->get_sql_where();
	}

	function evt__fi_editoriales__cancelar()
	{
		$this->s__filtro = null;
       	$this->s__where = '1=1';
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------




}
?>