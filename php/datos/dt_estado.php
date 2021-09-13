<?php
class dt_estado extends libros_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_e.id_estado,
			t_e.descripcion,
			t_e.mod_prog
		FROM
			cidig.estado as t_e
		ORDER BY descripcion";
		return toba::db('libros')->consultar($sql);
	}

	
	function get_descripciones_estado()
	{
		$sql="SELECT
			id_estado,
			descripcion as id_estado_nombre
			FROM
			cidig.estado as t_es";
		return toba::db('libros')->consultar($sql);
	}

		function get_descripciones()
		{
			$sql = "SELECT id_estado, descripcion as id_estado_nombre FROM cidig.estado ORDER BY descripcion";
			return toba::db('libros')->consultar($sql);
		}





}
?>