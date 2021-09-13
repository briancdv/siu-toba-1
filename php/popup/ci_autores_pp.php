<?php
class ci_autores_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_autores_pp ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
protected $s__filtro;
	function getlist($where=''){
		return toba::db()
			->consultar("SELECT t_a.id_autor, nombre , t_e.descripcion as id_estado_desc
							FROM curlib.autor as t_a INNER JOIN curlib.estado as t_e ON t_a.id_estado = t_e.id_estado
								WHERE t_a.id_estado =1 AND $where");

	}
	function conf__cd_autores_pp(libros_ei_cuadro $cuadro)
	{
		if (isset($this->s__filtro)){
			$filtro=$this->dep('fi_autor')->get_sql_where();
			$datos=$this->getlist($filtro);
			$cuadro->set_datos($datos);

		}else{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()
			->consultar("SELECT t_a.id_autor, nombre , t_e.descripcion as id_estado_desc
							FROM curlib.autor as t_a INNER JOIN curlib.estado as t_e ON t_a.id_estado = t_e.id_estado
								WHERE t_a.id_estado =1"));
				}
	}

	function evt__cd_autores_pp__seleccion($seleccion)
	{
	}

	function conf_evt__cd_autores_pp__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- fi_autor ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_autor(libros_ei_filtro $filtro)
	{
		$filtro->set_datos($this->s__filtro);
	}

	function evt__fi_autor__filtrar($datos)
	{
		$this->s__filtro=$datos;
	}

	function evt__fi_autor__cancelar()
	{
		unset($this->s__filtro);

}
}
?>