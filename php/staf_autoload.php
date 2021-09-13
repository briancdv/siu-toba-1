<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class staf_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); }
	}

	static $clases = array(
		'staf_ci' => 'extension_toba/componentes/staf_ci.php',
		'staf_cn' => 'extension_toba/componentes/staf_cn.php',
		'staf_datos_relacion' => 'extension_toba/componentes/staf_datos_relacion.php',
		'staf_datos_tabla' => 'extension_toba/componentes/staf_datos_tabla.php',
		'staf_ei_arbol' => 'extension_toba/componentes/staf_ei_arbol.php',
		'staf_ei_archivos' => 'extension_toba/componentes/staf_ei_archivos.php',
		'staf_ei_calendario' => 'extension_toba/componentes/staf_ei_calendario.php',
		'staf_ei_codigo' => 'extension_toba/componentes/staf_ei_codigo.php',
		'staf_ei_cuadro' => 'extension_toba/componentes/staf_ei_cuadro.php',
		'staf_ei_esquema' => 'extension_toba/componentes/staf_ei_esquema.php',
		'staf_ei_filtro' => 'extension_toba/componentes/staf_ei_filtro.php',
		'staf_ei_formulario' => 'extension_toba/componentes/staf_ei_formulario.php',
		'staf_ei_formulario_ml' => 'extension_toba/componentes/staf_ei_formulario_ml.php',
		'staf_ei_grafico' => 'extension_toba/componentes/staf_ei_grafico.php',
		'staf_ei_mapa' => 'extension_toba/componentes/staf_ei_mapa.php',
		'staf_servicio_web' => 'extension_toba/componentes/staf_servicio_web.php',
		'staf_comando' => 'extension_toba/staf_comando.php',
		'staf_modelo' => 'extension_toba/staf_modelo.php',
	);
}
?>