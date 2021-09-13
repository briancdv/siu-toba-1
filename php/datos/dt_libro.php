<?php
class dt_libro extends libros_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_l.id_libro,
			t_l.titulo,
			t_l.resumen,
			t_a.nombre as id_autor_nombre,
			t_e.nombre as id_editorial_nombre,
			t_l.estante,
			t_e1.descripcion as id_estado_nombre
		FROM
			curlib.libro as t_l	LEFT OUTER JOIN estado as t_e1 ON (t_l.id_estado = t_e1.id_estado),
			curlib.autor as t_a,
			curlib.editorial as t_e
		WHERE
				t_l.id_autor = t_a.id_autor
			AND  t_l.id_editorial = t_e.id_editorial
		ORDER BY titulo";
		return toba::db('libros')->consultar($sql);
	}



	function get_list($mientras='')
	{
		$sql = "SELECT
			t_l.id_libro,
			t_l.titulo,
			t_l.resumen,
			t_a.nombre as id_autor_nombre,
			t_e.nombre as id_editorial_nombre,
			t_l.estante,
			t_l.id_estado
		FROM
		curlib.libro as t_l	LEFT OUTER JOIN curlib.autor as t_a ON (t_l.id_autor = t_a.id_autor)
			LEFT OUTER JOIN curlib.editorial as t_e ON (t_l.id_editorial = t_e.id_editorial) 
			WHERE $mientras ";
		
		
		return toba::db('libros')->consultar($sql);
	}
	function get_titulo_libro($id=0)
	{
		$rs = toba::db()->consultar("SELECT titulo  FROM curlib.libro WHERE id_libro = ".$id);
	

		if(count($rs) > 0 ){
			$valor = $rs[0]['titulo'];
		}

		return $valor;
	}
    


}
?>