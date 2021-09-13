<?php
class dt_persona extends libros_datos_tabla
{

function get_cuil($id=0)
	{
		$rs = toba::db()->consultar("SELECT cuil_documento  FROM cidig.persona WHERE id_persona = ".$id);

		if(count($rs) > 0 ){
			$valor = $rs[0]['cuil_documento'];
		}

		return $valor;
    }

	
}
?>