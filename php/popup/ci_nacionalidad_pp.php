<?php
class ci_nacionalidad_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_nacionalidad_pp -----------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_nacionalidad_pp(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()
			->consultar("SELECT
			id_nacionalidad,
			descripcion as id_nombre_nac
			FROM
			cidig.nacionalidad as t_n ORDER BY descripcion;"));
	}

	function evt__cd_nacionalidad_pp__seleccion($seleccion)
	{
	}

	function conf_evt__cd_nacionalidad_pp__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

}

?>