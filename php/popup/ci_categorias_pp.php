<?php
class ci_categorias_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_categorias_pp -------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_categorias_pp(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()->consultar("SELECT id_categoria, descripcion 
													FROM curlib.categoria 
														WHERE id_estado =1"));
	}

}

?>