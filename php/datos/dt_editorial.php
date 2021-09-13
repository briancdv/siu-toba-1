<?php
class dt_editorial extends libros_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_editorial, nombre FROM editorial
		WHERE id_estado=1
		 ORDER BY nombre";
		return toba::db('libros')->consultar($sql);
	}
	function get_listado($filtro=array())
	{
		$where = array();
		if (isset($filtro['nombre'])) {
			$where[] = "nombre ILIKE ".quote("%{$filtro['nombre']}%");
		}
		if (isset($filtro['id_estado'])) {
			$where[] = "id_estado = ".quote($filtro['id_estado']);
		}
		$sql = "SELECT
			t_e.id_editorial,
			t_e.nombre,
			t_e.domicilio,
			t_e.persona_contacto,
			t_e.telefonos,
			t_e1.descripcion as id_estado_nombre
		FROM
			editorial as t_e	LEFT OUTER JOIN estado as t_e1 ON (t_e.id_estado = t_e1.id_estado)
		ORDER BY nombre";
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
		return toba::db('libros')->consultar($sql);
	}

}
?>