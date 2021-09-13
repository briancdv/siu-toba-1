<?php
class ci_editoriales_pp extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_editoriales_pp ------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_editoriales_pp(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$cuadro->set_datos(toba::db()
			->consultar("SELECT id_editorial, nombre, domicilio, persona_contacto, telefonos
							FROM curlib.editorial 
								WHERE id_estado =1"));
	}

}

?>