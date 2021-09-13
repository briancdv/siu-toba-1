<?php
/**
 * Esta clase fue y será generada automáticamente. NO EDITAR A MANO.
 * @ignore
 */
class libros_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
		'libros_ci' => 'extension_toba/componentes/libros_ci.php',
		'libros_cn' => 'extension_toba/componentes/libros_cn.php',
		'libros_datos_relacion' => 'extension_toba/componentes/libros_datos_relacion.php',
		'libros_datos_tabla' => 'extension_toba/componentes/libros_datos_tabla.php',
		'libros_ei_arbol' => 'extension_toba/componentes/libros_ei_arbol.php',
		'libros_ei_archivos' => 'extension_toba/componentes/libros_ei_archivos.php',
		'libros_ei_calendario' => 'extension_toba/componentes/libros_ei_calendario.php',
		'libros_ei_codigo' => 'extension_toba/componentes/libros_ei_codigo.php',
		'libros_ei_cuadro' => 'extension_toba/componentes/libros_ei_cuadro.php',
		'libros_ei_esquema' => 'extension_toba/componentes/libros_ei_esquema.php',
		'libros_ei_filtro' => 'extension_toba/componentes/libros_ei_filtro.php',
		'libros_ei_firma' => 'extension_toba/componentes/libros_ei_firma.php',
		'libros_ei_formulario' => 'extension_toba/componentes/libros_ei_formulario.php',
		'libros_ei_formulario_ml' => 'extension_toba/componentes/libros_ei_formulario_ml.php',
		'libros_ei_grafico' => 'extension_toba/componentes/libros_ei_grafico.php',
		'libros_ei_mapa' => 'extension_toba/componentes/libros_ei_mapa.php',
		'libros_servicio_web' => 'extension_toba/componentes/libros_servicio_web.php',
		'libros_comando' => 'extension_toba/libros_comando.php',
		'libros_modelo' => 'extension_toba/libros_modelo.php',
	);
}
?>