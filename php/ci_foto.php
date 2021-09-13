<?php
class ci_foto extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- form_foto --------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function rel(){
		return $this->dep('datos'); 
	}
	function conf__form_foto(libros_ei_formulario $form)
	{
		if ($this->rel()->tabla('foto')->get_cantidad_filas() > 0) {
	
			$form->set_datos($this->rel()->tabla('foto')->get());
			} else {
				$this->pantalla()->eliminar_evento('eliminar');
			}
	}

	function evt__form_foto__alta($datos)
	{
		//delcaro el nombre de la variable a guardar
		$foto_guardar = "";
		//declaro la variable a trabajar sobre el archivo temporarl
		$foto_tmp = "";
		//declaro la variable con el arreglo de la foto del upload
		$imagen_a_tratar = ($datos['foto']);

		//declaro la ruta donde voy a guardar las fotos
		$ruta_final = 'img/imagenes/';

		//obtengo la ruta inicial del archivo
		$this->s__path_inicial = toba::proyecto()->get_www($ruta_final);

		//declaro la ruta inicial del archivo
		$ruta_inicial = $this->s__path_inicial['path']; 

		$path = $ruta_inicial;
		
		//pregunto si existe el fichero donde voy a guardar los archivos
		if (!file_exists($path)){
			//si no existe la ruta, la creo y doy permisos de adm
			mkdir($path);
			//doy peromisos de adm
			chmod($path, 0777);
		}
			//guardo el nombre de la imagen
		$nombre_img = basename($datos['foto']['name']);
			//guardo el nombre del archivo temporal
		$foto_tmp = $datos['foto']['tmp_name'];

			//creo un numero aletario para el nombre del archivo
		$num_ram= mt_rand(0,10000);



		$foto_guardar = $ruta_final . $nombre_img;
		//separo la ruta a partir del punto
		$arreglo = explode(".",$foto_guardar);
		//concateno el numero aleatorio y lo guardo en $foto_guardar
		$foto_guardar = $arreglo[0] . $num_ram . "." . $arreglo[1];


			//pregunto si cargo, no es necesario el if
		if (move_uploaded_file($foto_tmp, $foto_guardar)) {
			echo 'cargo';				

			$datos["foto"]=$foto_guardar;
			$this->dep('datos')->nueva_fila($datos);
			$this->dep('datos')->sincronizar();
			$this->dep('datos')->resetear();
			$this->set_pantalla('pant_inicial');
		}else{
			echo 'no cargo';
		}
	}	
	

	

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		$datos=toba::db()->consultar("SELECT id_foto, nombre,foto FROM curlib.foto;");
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->rel()->tabla('foto')->cargar($seleccion);
		
		$this->set_pantalla('pant_edicion');
	}

}
?>