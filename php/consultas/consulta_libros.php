<?php
class consulta_libros{
    function get_datos_libros(){
        $sql=" SELECT id_libro, titulo, resumen, id_autor, id_editorial, estante, estado
        FROM curlib.libro;";
        return toba::db('curlib')->consultar($sql);
    }
    function get_datos_autor (){
        $sql="SELECT  autor , id_autor, nombre, estado
        FROM curlib.autor";
      return toba::db()->consultar($sql);

    }
    function get_datos_editorial (){
        $sql="SELECT id_editorial, nombre, domicilio, persona_contacto, telefono, 
        estado
   FROM curlib.editorial;";
      return toba::db()->consultar($sql);

    }
    function eliminar_libro($id){
        $sql="UPDATE curlib.libro
        SET  estado=0
      WHERE id_libro = $id";
    }
    function get_estado_libro(){
      $sql="SELECT id_estado
			t_es.descripcion as id_estado_nombre
			FROM
			curlib.estado as t_es";
    }
   
	static function get_titulo_libro($id=0)
	{
		$rs = toba::db()->consultar("SELECT titulo  FROM curlib.libro WHERE id_libro = ".$id);
		$valor = "No se pudo identificar el Id. libro: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['titulo'];
		}

		return $valor;
	}
  function get_descripciones_adq()
	{
		$sql="SELECT
      id_adquisicion,
			descripcion as id_adquisicion_nombre
			FROM
			curlib.adquisicion as t_ad ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}  
}

?>