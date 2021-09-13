<?php
class ci_libro extends libros_ci
{
	//---- ABM Libros
	protected $s__datos_filtro;
	protected $s__path_inicial;
	//---- Cuadro -----------------------------------------------------------------------


	function get_list($mientras = '')
	{
		$sql = "SELECT
		t_l.id_libro,
		t_l.titulo,
		t_l.resumen,
		t_a.nombre as id_autor_nombre,
		t_e.nombre as id_editorial_nombre,
		t_l.estante,
		t_es.descripcion as id_estado_nombre,
		t_ad.descripcion as id_adquisicion_nombre,
		t_l.isbn,
		t_l.ejemplar,
		t_l.anio,
		t_g.descripcion as id_genero_nombre,
		t_l.foto

		FROM
		curlib.libro as t_l LEFT OUTER JOIN cidig.estado as t_es ON (t_l.id_estado = t_es.id_estado),
		curlib.autor as t_a,
		curlib.editorial as t_e,
		curlib.adquisicion as t_ad,
		curlib.genero as t_g
		WHERE 
			t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero	AND $mientras		
			
	ORDER BY titulo";


		return toba::db('libros')->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro = $this->dep('fi_libro')->get_sql_where();
			$cuadro->set_datos($this->get_list($filtro));
		} else {
			$cuadro->desactivar_modo_clave_segura();
			$sql = "SELECT
				t_l.id_libro,
				t_l.titulo,
				t_l.resumen,
				t_a.nombre as id_autor_nombre,
				t_e.nombre as id_editorial_nombre,
				t_l.estante,
				t_es.descripcion as id_estado_nombre,
				t_ad.descripcion as id_adquisicion_nombre,
				t_l.isbn,
				t_l.ejemplar,
				t_l.anio,
				t_g.descripcion as id_genero_nombre,
				t_l.foto

				FROM
				curlib.libro as t_l LEFT OUTER JOIN cidig.estado as t_es ON (t_l.id_estado = t_es.id_estado),
				curlib.autor as t_a,
				curlib.editorial as t_e,
				curlib.adquisicion as t_ad,
				curlib.genero as t_g
				WHERE 
					t_l.id_autor = t_a.id_autor AND t_l.id_editorial = t_e.id_editorial AND t_l.adquicision_id = t_ad.id_adquisicion AND t_l.id_genero = t_g.id_genero			
					
			ORDER BY titulo";
			$datos = toba::db()->consultar($sql);
			$cuadro->set_datos($datos);
		}
	}


	function evt__cuadro__seleccion($datos)
	{

		$this->dep('datos')->cargar($datos);
		$this->dep('datos')->tabla('libro')->set_cursor(0);

		$this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$datos = $this->dep('datos')->tabla('libro')->get();
			$img = $datos['foto'];
			$form->ef('imagen')->set_estado("<img src= '$img' width='100px' height='100px'  >");
			$form->set_datos($datos);
		}
	}
	//se busca la existencia de un libro a traves del titulo
	function get_libro_t($titulo = '')
	{

		$rs = toba::db()->consultar("SELECT  id_libro FROM curlib.libro AS l WHERE l.titulo  = '$titulo'");
		$valor = 0;

		if (count($rs) > 0) {
			$valor = $rs[0]['id_libro'];
		}

		return $valor;
	}

	function conexion()
	{
		if($_SERVER['SERVER_NAME'] == "desarrollo.ciudaddecorrientes.gov.ar")
		{
			$valor=true;
			return $valor;
		}
		elseif($_SERVER['SERVER_NAME'] == "localhost"){ 
			$valor=false;
			return $valor;
		}
	}
	function conexion_ssh()
	{
		$sftp= null;
		if(!($conexion_ssh = ssh2_connect('192.168.10.200',22)))
		{
			toba::notificacion()->vaciar();
			toba::notificacion()->set_titulo('Biblioteca');
			toba::notificacion()->agregar('ATENCION: Ha fallado la conexion SSH con el servidor de Desarrollo.<br> Las imágenes no se descargaran apropiadamente.');
		}
		else{ 
			ssh2_auth_password($conexion_ssh, 'root', 'roda1950');
			$sftp = ssh2_sftp($conexion_ssh);
		}
		return $sftp;
	}
	function evt__formulario__alta($datos)
	{	
		$nombre_img = "";
		//delcaro el nombre de la variable a guardar
		$foto_guardar = "";
		//declaro la variable a trabajar sobre el archivo temporarl
		$foto_tmp = "";
		//declaro la variable con el arreglo de la foto del upload
		$imagen_a_tratar = ($datos['imagen']['name']);

		if ($imagen_a_tratar !== null) {

			//declaro la ruta donde voy a guardar las fotos
		
			$ruta_final="/var/www/documentos/libros/";
			//obtengo la ruta inicial del archivo
			
			if ($this->conexion()!=true)
		{
			$sftp=$this->conexion_ssh();
			//pregunto si existe el fichero donde voy a guardar los archivos
			if (!file_exists('ssh2.sftp://'.$sftp.$ruta_final)) {
				//si no existe la ruta, la creo y doy permisos de adm
				mkdir('ssh2.sftp://'.$sftp.$ruta_final);
				//doy peromisos de adm
				chmod('ssh2.sftp://'.$sftp.$ruta_final, 0777);
			}
			//guardo el nombre de la imagen
			$nombre_img = basename($datos['imagen']['name']);

			//guardo el nombre del archivo temporal
			$foto_tmp = $datos['imagen']['tmp_name'];

			//creo un numero aletario para el nombre del archivo
			$num_ram = mt_rand(0, 10000);
			
			$foto_guardar = $ruta_final . $nombre_img;

			//separo la ruta a partir del punto
			$arreglo = explode(".", $foto_guardar);

			//concateno el numero aleatorio y lo guardo en $foto_guardar
			$foto_guardar = $arreglo[0] . $num_ram . "." . $arreglo[1];
			$datos['foto'] = $foto_guardar;
			
			//pregunto si cargo, no es necesario el if
			if (move_uploaded_file($foto_tmp, 'ssh2.sftp://'.$sftp.$foto_guardar)) {
				echo 'cargo'; 
				$this->informar_msg('Se cargo la imagen', 'info');
			} else {
				echo 'no cargo';
				$this->informar_msg('No se puede cargar la imagen', 'error');

			
			}
		}
		 else{
			
		}
	
	
		$this->dep('datos')->tabla('libro')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->dep('datos')->resetear();
		$this->informar_msg('Datos se creo exitosamente ', 'info');
		$this->set_pantalla('pant_seleccion');

	}
}
	function hola(){
		echo "hola";
		echo "prueba";
		echo "prueba";
	}
	function evt__formulario__modificacion($datos)
	{


		//delcaro el nombre de la variable a guardar
		$foto_guardar = "";
		//declaro la variable a trabajar sobre el archivo temporarl
		$foto_tmp = "";
		//declaro la variable con el arreglo de la foto del upload
		$imagen_a_tratar = ($datos['imagen']);
		$id = $datos['id_libro'];
		$img_base = toba::db()->consultar("SELECT foto FROM curlib.libro as t_l WHERE t_l.id_libro = $id ");
		
		
		if ($imagen_a_tratar !== null) {
			//declaro la ruta donde voy a guardar las fotos
			$ruta_final = 'img/imagenes/';

			//obtengo la ruta inicial del archivo
			$this->s__path_inicial = toba::proyecto()->get_www($ruta_final);

			//declaro la ruta inicial del archivo
			$ruta_inicial = $this->s__path_inicial['path'];
			//D:\toba_2.7.4/proyectos/libros/www/img/imagenes/
			$path = $ruta_inicial;
			$var = explode("/", $path);
			
			//pregunto si existe el fichero donde voy a guardar los archivos
			if (!file_exists($path)) {
				//si no existe la ruta, la creo y doy permisos de adm
				mkdir($path);
				//doy peromisos de adm
				chmod($path, 0777);
			}
			//guardo el nombre de la imagen
			$nombre_img = basename($datos['imagen']['name']);
			//guardo el nombre del archivo temporal

			$foto_tmp = $datos['imagen']['tmp_name'];

			//creo un numero aletario para el nombre del archivo
			$num_ram = mt_rand(0, 10000);


			$ruta_final="/var/www/documentos/libros/";
			$foto_guardar = $ruta_final . $nombre_img;

			//separo la ruta a partir del punto
			$arreglo = explode(".", $foto_guardar);
			
			//concateno el numero aleatorio y lo guardo en $foto_guardar
			$foto_guardar = $arreglo[0] . $num_ram . "." . $arreglo[1];
			//pregunto si cargo, no es necesario el if
			$sftp=$this->conexion_ssh();
			if (move_uploaded_file($foto_tmp,'ssh2.sftp://'.$sftp.$foto_guardar)) {
				$datos["foto"] = $foto_guardar;
			
				
				
				 $ruta_corta = $var[0] . "/" . $var[1] . "/" . $var[2] . "/" . $var[3] . "/";
				 $ruta=$ruta_corta. $img_base[0]["foto"];
				//Compruebo si existe la foto
				if (file_exists( $ruta)) {
					if ($img_base[0]["foto"] !== $imagen_a_tratar && $img_base[0]["foto"] !== "img/imagenes/sinimagen.png" ) {
					
						unlink($ruta_corta . $img_base[0]["foto"]);
					}
				} 
				
				
			} else {
				
				$this->informar_msg("error", 'info');
			}
		}else if( $img_base[0]['foto'] === null){
			$datos['foto'] = '/var/www/documentos/libros/sinimagen.png';
		}
		$this->dep('datos')->tabla('libro')->set($datos);
		$this->dep('datos')->sincronizar();
		$this->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__formulario__baja()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	function evt__formulario__cancelar()
	{
		$this->resetear();
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf_evt__cuadro__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__agregar = function()
		{
		}
		
		{$this->objeto_js}.evt__cancelar = function()
		{
		}
		";
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->resetear();
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- fi_libro ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_libro(libros_ei_filtro $filtro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__fi_libro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__fi_libro__cancelar()
	{
		unset($this->s__datos_filtro);
	}
}
