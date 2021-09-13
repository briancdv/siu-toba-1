<?php
class consulta_persona{
    function get_persona(){
        $sql=" SELECT id_persona, id_persona_tipo, cuil_tipo, cuil_documento, cuil_digito, 
        cuil, id_cond_fiscal, email, id_estado, id_tipo_documento, id_nacionalidad_d, 
        control_doc 
   FROM cidig.persona
   limit 10;
        ";
        return toba::db()->consultar($sql);
    }
    function get_id(){
        $sql=" SELECT  id_persona
   FROM cidig.persona limit 10;
        ";
        return toba::db()->consultar($sql);
    }

    function get_cuil($id=0)
	{
		$rs = toba::db()->consultar("SELECT cuil_documento  FROM cidig.persona WHERE id_persona = ".$id);

		if(count($rs) > 0 ){
			$valor = $rs[0]['cuil_documento'];
		}

		return $valor;
	}
    function get_descripciones_estado()
	{
		$sql="SELECT
			id_estado,
			descripcion as id_estado_nombre
			FROM
			cidig.estado as t_es ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}
	function get_descripciones_sexo()
	{
		$sql="SELECT
			id_sexo,
			descripcion as id_sexo_nombre
			FROM
			cidig.sexo as t_es ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}
	function get_descripciones_cfiscal()
	{
		$sql="SELECT
			id_cond_fiscal,
			descripcion as id_cf_nombre
			FROM
			cidig.cond_fiscal as t_c_f ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}
    function get_descripciones_tipo()
	{
		$sql="SELECT
			id_persona_tipo,
			descripcion as id_descr_tipo
			FROM
			cidig.persona_tipo as t_p ORDER BY descripcion; ";
		return toba::db()->consultar($sql);
	}
    function get_descripciones_nac()
	{
		$sql="SELECT
			id_nacionalidad,
			descripcion as id_nombre_nac
			FROM
			cidig.nacionalidad as t_n ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}
	function get_descripciones_t_doc()
	{
		$sql="SELECT
			id_tipo_documento,
			descripcion as id_nombre_tipo_doc
			FROM
			cidig.tipo_documento as t_t_d ORDER BY descripcion ";
		return toba::db()->consultar($sql);
	}
}
?>