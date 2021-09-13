<?php

class php_popups 
{

	function get_editoriales_pp($id = 0)
	{
		$rs = toba::db()->consultar("SELECT nombre FROM curlib.editorial WHERE id_editorial = ".$id);
		$valor = "No se pudo identificar el Id. Editorial: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['nombre'];
		}

		return $valor;
	}

	static function get_autores_pp($id=0)
	{
		$rs = toba::db()->consultar("SELECT nombre FROM curlib.autor WHERE id_autor = ".$id);
		$valor = "No se pudo identificar el Id. Autor: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['nombre'];
		}

		return $valor;

	}

	static function get_generos_pp($id = 0)
	{
		$rs = toba::db()->consultar("SELECT descripcion FROM curlib.genero WHERE id_genero =".$id);
		$valor = "No se pudo identificar el Id. Genero: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['descripcion'];
		}

		return $valor;
	}

	function get_categorias_pp($id = 0)
	{
		$rs = toba::db()->consultar("SELECT descripcion FROM curlib.categoria WHERE id_categoria =".$id);
		$valor = "No se pudo identificar el Id. Categoria: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['descripcion'];
		}

		return $valor;
	}

	static function get_persona_pp($id=0)
	{
		$rs = toba::db()->consultar("SELECT cuil_documento  FROM cidig.persona WHERE id_persona = ".$id);
		$valor = "No se pudo identificar el Id. persona: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['cuil_documento'];
		}

		return $valor;
	}
	static function get_titulo_libro_pp($id=0)
	{
		$rs = toba::db()->consultar("SELECT titulo  FROM curlib.libro WHERE id_libro = ".$id);
		$valor = "No se pudo identificar el Id. libro: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['titulo'];
		}

		return $valor;
	}
	static function get_persona_tipo_pp($id=0)
	{
		$rs = toba::db()->consultar("SELECT descripcion  FROM cidig.persona_tipo WHERE id_persona_tipo = ".$id);
		$valor = "No se pudo identificar el Id.Persona tipo: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['descripcion'];
		}

		return $valor;
	}
	static function get_nacionalidad_pp($id=0)
	{
$rs = toba::db()->consultar("SELECT descripcion  FROM cidig.nacionalidad WHERE id_nacionalidad = ".$id);
		$valor = "No se pudo identificar el id Nacionalidad: ".$id;

		if(count($rs) > 0 ){
			$valor = $rs[0]['descripcion'];
		}		

		return $valor;
	}
	
}


?>
