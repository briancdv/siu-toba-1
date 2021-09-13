<?php
class ci_persona_tipo_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_tipo_pp -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_tipo_pp(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()
			->consultar("SELECT id_persona_tipo, descripcion AS id_tipo_desc
							FROM cidig.persona_tipo;"));
	}

	function evt__cd_tipo_pp__seleccion($seleccion)
	{
	}

	function conf_evt__cd_tipo_pp__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

}

?>