<?php
class ci_gestion_de_prestamo extends libros_ci
{
	//-----------Gestion de Prestamo
	#------ ajax para calcular la fecha de vencimiento -------
	function ajax__get_calcula_vto($dts, toba_ajax_respuesta $respuesta)
	{      
		$rs = null;
		$fe = date($dts['fecha_alta']);
		$year = substr($fe,6,4); 
		$month = substr($fe,3,2); 
		$day = substr($fe,0,2); 
		$fecha_aux = $year."-".$month."-".$day;
		$fecha_final = strtotime('+'.$dts['plazo'].'day', strtotime($fecha_aux)); 
		$rs = date('d-m-Y', $fecha_final);  
		$respuesta->set($rs);
	}

	
	
	protected $s__datos_filtro;

	function rel() 
	{  
		return $this->dep('datos'); 
	} 

	//---- Filtro -----------------------------------------------------------------------

	

	//---- Cuadro -----------------------------------------------------------------------
	function get_list($where=''){
		$sql=("SELECT id_prestamo,t_pe.cuil_documento,t_p_n.apyn ,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
		FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
		cidig.persona  as t_pe ,
		cidig.persona_natural as t_p_n
		where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND devolucion ='No' AND $where;");
		return toba::db()->consultar($sql);
	}
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) {
			$filtro=$this->dep('fi_prestamo')->get_sql_where();
			
			$cuadro->set_datos($this->get_list($filtro));
		} else {
			$sql=("SELECT id_prestamo,t_pe.cuil_documento,t_p_n.apyn,t_l.titulo,t_l.ejemplar ,t_l.isbn,fecha_alta,plazo,fecha_venc,devolucion,fecha_devolucion,dias_retraso
			FROM curlib.prestamo as t_p LEFT OUTER JOIN curlib.libro as t_l ON (t_p.libro_id = t_l.id_libro),
			cidig.persona  as t_pe ,
			cidig.persona_natural as t_p_n
			where (t_p.persona_id = t_pe.id_persona) AND (t_p_n.id_persona = t_pe.id_persona) AND devolucion ='No' ");
		$datos=toba::db()->consultar($sql);
			$cuadro->set_datos($datos);
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		
		$id_prestamo=$datos['id_prestamo'];
		
		$rs =toba::db()->consultar("SELECT libro_id
			FROM curlib.prestamo
			where id_prestamo =$id_prestamo;");
		$libro_id=($rs[0]['libro_id']);
	
		$this->rel()->tabla('libro')->cargar(array('id_libro'=>	$libro_id));
  		$this->rel()->tabla('prestamo')->cargar(array('id_prestamo'=>$id_prestamo));
		
		$this->set_pantalla('pant_edicion'); 
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		

		if ($this->rel()->tabla('prestamo')->get_cantidad_filas() > 0) {
	
		$form->set_datos($this->rel()->tabla('prestamo')->get());
		} else {
			$this->pantalla()->eliminar_evento('eliminar');
			
		}
		
	}

	

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	
	function evt__volver()
	{
		$this->resetear();
	}


	function evt__guardar()
	{
		$this->rel()->sincronizar();
		$this->resetear();
		
		//$this->dep('datos')->sincronizar();
		//$this->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- fi_prestamo ------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__fi_prestamo__filtrar($datos)
	{
		
		
		$this->s__datos_filtro=$datos;
		
	}

	function evt__fi_prestamo__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	function conf__fi_prestamo(libros_ei_filtro $filtro)
	{
		if(isset($this->s__datos_filtro)){
			$filtro->set_datos($this->s__datos_filtro);
				}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf_evt__cuadro__seleccion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	
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
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__guardar = function()
		{
		}
		";
	}


		
	
		function es_moroso($id=0){
			$rs =toba::db()->consultar("SELECT id_prestamo
			FROM curlib.prestamo as t_p
			where persona_id = $id AND ((t_p.devolucion is null) OR t_p.devolucion = 'No' ) AND t_p.fecha_venc < current_date;");
			$valor=0;
			if(count($rs) > 0 ){
				$valor = $rs[0]['id_prestamo'];
			}
	
			return $valor;
	
		}
		/*function stock_libros($id=0){
			$rs =toba::db()->consultar("SELECT cantidad
			FROM curlib.libro 
			where id_libro = $id ;");
			$valor=0;
			if(count($rs) > 0 ){
				$valor = $rs[0]['cantidad'];
			}
	
			return $valor;
	
		}
		*/
		function cantidad_lib_prestado($id=0){
			$rs =toba::db()->consultar("SELECT count(id_prestamo)
			FROM curlib.prestamo 
			 where persona_id = $id AND (devolucion is null OR devolucion = 'No' )  ");
				if(count($rs) > 0 ){
					$valor = $rs[0]['count'];
				}
		
				return $valor;
			

			
		}
		
	function evt__formulario__alta($datos)
	{
		
		//PARA OBTENER VALOR DE ARRAY $DATOS
		$id_persona =$datos['persona_id'];
		$id_libro=$datos['libro_id'];
		$cant_libros=$this->cantidad_lib_prestado($id_persona);
		
		
		$this->cantidad_lib_prestado($id_persona);
		
		if ($cant_libros >= 3){ 
			
			$this->informar_msg('No se puede realizar el prestamo ','error');
		}
		
		elseif ($this->es_moroso($id_persona)!=0){ 
			// Mensaje nivel formulario 
			$this->pantalla()->agregar_notificacion('No se puede realizar el prestamo porque es moroso','error');
		//	$this->informar_msg('No se puede realizar el prestamo porque es moroso','error');
		}
		/*elseif ($this->stock_libros($id_libro) == 0){ 
			
			$this->informar_msg('No se puede realizar el prestamo no hay libro disponible','error');
		}
		*/
		else
		{
			//PARA AGREGAR UN NUEVO VALOR A MI ARRAY $DATOS 
			$datos['devolucion']="No";
			$dia= date("w",strtotime($datos['fecha_venc']));
			if ($dia == 0)
			{
				$datos['plazo']=$datos['plazo'] + 1;
				$fe = $datos['fecha_alta'];
				$year = substr($fe,0,4); 
				$month = substr($fe,5,2); 
				$day = substr($fe,8,2); 
				$fecha_aux = $year."-".$month."-".$day;
				$fecha_final = strtotime('+'.$datos['plazo'].'day', strtotime($fecha_aux)); 
				$rs = date('Y-m-d', $fecha_final);
				$datos['fecha_venc']=$rs;
				$this->informar_msg('Se agrega 1 dias mas plazo por vencer un domingo','info');
			}
			if ($dia == 6)
			{
				$datos['plazo']=$datos['plazo'] + 2;
				$fe = $datos['fecha_alta'];
				$year = substr($fe,0,4); 
				$month = substr($fe,5,2); 
				$day = substr($fe,8,2); 
				$fecha_aux = $year."-".$month."-".$day;
				
				$fecha_final = strtotime('+'.$datos['plazo'].'day', strtotime($fecha_aux)); 
				$rs = date('Y-m-d', $fecha_final);
				$datos['fecha_venc']=$rs;
				$this->informar_msg('Se agrega 2 dias mas plazo por vencer un sabado','info');
			}
			
			$datos['fecha_devolucion']=null;
			$datos['dias_retraso']=null;
			//$libro=toba::db()->consultar("SELECT * FROM curlib.libro WHERE id_libro = $id_libro");
		
			
			$this->rel()->tabla('libro')->cargar(array('id_libro'=>$id_libro));
			$dt_l=$this->rel()->tabla('libro')->get_filas();
        	$dt_l[0]['id_estado']=2;

			$this->rel()->tabla('prestamo')->set($datos);
			$this->rel()->tabla('prestamo')->sincronizar();
			$this->rel()->tabla('prestamo')->resetear();
			
			$this->rel()->tabla('libro')->set($dt_l[0]);   
			$this->rel()->tabla('libro')->sincronizar();
			$this->rel()->tabla('libro')->resetear(); 
			$this->informar_msg('prestamo realizado con exito','info');
			$this->set_pantalla('pant_seleccion');

			
		}


	
	}
	function evt__formulario__baja()
	{
		$this->rel()->tabla('prestamo')->eliminar_todo();
		$this->rel()->sincronizar();
		$this->set_pantalla('pant_seleccion');
		
	}

	

	function get_libro($id){
		$sql ="
			SELECT libro_id FROM curlib.prestamo WHERE id_prestamo = ".$id;
		return toba::db()->consultar($sql);

	}
	function evt__formulario__modificacion($datos)
	{
		$id_libro= intval($datos['libro_id']);
		$id_prestamo=$datos['id_prestamo'];
		$libro_anterior= $this->get_libro($id_prestamo);
		$libro_int=$libro_anterior[0]['libro_id'];
		if($libro_int != $id_libro){
			//	CAMBIAR EL ESTADO A INACTIVO DEL LIBRO ACTUAL DEL FORMULARIO
			$this->rel()->tabla('libro')->cargar(array('id_libro'=>$id_libro));
			$libro_nuevo=$this->rel()->tabla('libro')->get_filas();
        	$libro_nuevo[0]['id_estado']=2;
			
			
			$this->rel()->tabla('libro')->set($libro_nuevo[0]);   
			$this->rel()->tabla('libro')->sincronizar();
			$this->rel()->tabla('libro')->resetear(); 
			
			//	CAMBIAR EL ESTADO A ACTIVO DEL LIBRO QUE ESTABA PRESTADO
			$this->rel()->tabla('libro')->cargar(array('id_libro'=>$libro_int));
			$libro_viejo=$this->rel()->tabla('libro')->get_filas();
        	$libro_viejo[0]['id_estado']=1;
			$this->rel()->tabla('libro')->set($libro_viejo[0]);   
			$this->rel()->tabla('libro')->sincronizar();
			$this->rel()->tabla('libro')->resetear();

			$this->rel()->tabla('prestamo')->set($datos);
			$this->rel()->tabla('prestamo')->sincronizar();
			$this->rel()->tabla('prestamo')->resetear();
			
		}else{
	
		$datos['devolucion']="No";
		$this->rel()->tabla('prestamo')->set($datos);
		$this->rel()->tabla('prestamo')->sincronizar();
		$this->rel()->tabla('prestamo')->resetear();
		
		}
		$this->informar_msg('Datos modificados exitosamente ', 'info');
		$this->set_pantalla('pant_seleccion');
	}

	function evt__formulario__cancelar()
	{
		$this->rel()->tabla('prestamo')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
		$this->resetear();
		$this->set_pantalla('pant_edicion');

	}

	function evt__cuadro__devolucion($seleccion)
	{
		$id_prestamo=$seleccion['id_prestamo'];
		
		$rs =toba::db()->consultar("SELECT libro_id
			FROM curlib.prestamo
			where id_prestamo =$id_prestamo;");
		$libro_id=($rs[0]['libro_id']);
		
		$this->rel()->tabla('libro')->cargar(array('id_libro'=>	$libro_id));
  		$this->rel()->tabla('prestamo')->cargar(array('id_prestamo'=>$id_prestamo));
		$this->set_pantalla('pant_devolucion');
	
	}

	function conf_evt__cuadro__devolucion(toba_evento_usuario $evento, $fila)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- f_devolucion -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__f_devolucion(libros_ei_formulario $form)
	{
		if($this->rel()->tabla('prestamo')->hay_cursor()){
			$datos=$this->rel()->tabla('prestamo')->get();
			$form->set_datos($datos);
			
		}else{
			$this->pantalla()->eliminar_evento('eliminar');
		}
		
	}

	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__pant_devolucion(toba_ei_pantalla $pantalla)
	{
	}
	
	function evt__f_devolucion__modificacion($datos)
	{
		

		$devolucion= $datos['devolucion'];
		$id_libro= $datos['libro_id'];
		$fecha=date("d-m-Y", strtotime($datos['fecha_devolucion']));
		$datos['fecha_devolucion']=$fecha;
		
		if ($devolucion == 'Si'){
			$id_libro= $datos['libro_id'];
			$fecha=date("d-m-Y", strtotime($datos['fecha_devolucion']));
			$datos['fecha_devolucion']=$fecha;
        $this->rel()->tabla('libro')->cargar(array('id_libro'=>$id_libro));
			$dt_l=$this->rel()->tabla('libro')->get_filas();
        	$dt_l[0]['id_estado']=1;
			
			$this->rel()->tabla('prestamo')->set($datos);
			$this->rel()->tabla('prestamo')->sincronizar();
			$this->rel()->tabla('prestamo')->resetear();
			
			$this->rel()->tabla('libro')->set($dt_l[0]);   
			$this->rel()->tabla('libro')->sincronizar();
			$this->rel()->tabla('libro')->resetear(); 
		$this->informar_msg('Libro devuelto con exito ', 'info'); 
		$this->set_pantalla('pant_seleccion');

		}
		else{
		
	
		$this->rel()->tabla('prestamo')->resetear();
			$this->set_pantalla('pant_seleccion');
		}
			
	}
	
	
		
		


}
?>