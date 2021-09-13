<?php
class ci_generos_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_generos_pp ----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_generos_pp(libros_ei_cuadro $cuadro)
	{
		
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()->consultar("SELECT id_genero, descripcion  FROM curlib.genero; "));
	}

	function evt__cuadro__seleccion($seleccion)
	{
	}

	function conf_evt__cuadro__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

}
?>