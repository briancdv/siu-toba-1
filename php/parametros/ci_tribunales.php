<?php
/**
 * Created by Marina Barrios
 */

require_once('consultas/consultas.php');
class ci_tribunales extends staf_ci
{
    protected $s__msj = null;

	//-----------------------------------------------------------------------------------
	//---------------------------------- CUADRO -----------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_tribunales(staf_ei_cuadro $cuadro)
	{
        /** codigo del ayuda ------------------------------------------------------------------------------------------------------------------- */
        $id_objeto = $this->pantalla($id_pantalla)->get_id();
        foreach(toba::usuario()->get_perfiles_funcionales() as $valor)
        {
            $perfiles[] = "'".utf8_encode($valor)."'"; //lista los perfiles de usuarios separados por coma
        }

        $restricciones = trim(implode(toba::usuario()->get_restricciones_funcionales(),','),','); //lista las restricciones func. separados por coma

        $parametros_ayuda = array('proyecto'=>utf8_encode($id_objeto[0]),
            'objeto'=>$id_objeto[1],
            'descr_objeto'=>utf8_encode('pantalla: Areas que solucionan problemas (no valido como parametro)')
            /*,'p1_contenido'=>utf8_encode('tipo de inspector')
            ,'p1'=>utf8_encode($this->s__inspector['id_insp_tipo'])
            ,'p2_contenido'=>utf8_encode('es supervisor')
            ,'p2'=>utf8_encode($this->s__inspector['es_supervisor'])
            ,'p3_contenido'=>utf8_encode('Id. Pantalla')
            ,'p3'=>utf8_encode($id_pantalla)*/
        ,'p4_contenido'=>utf8_encode('Perfiles del usuario')
        ,'p4'=>trim(implode($perfiles,','),',')
        ,'p5_contenido'=>utf8_encode('Restricciones funcionales')
        ,'p5'=>utf8_encode($restricciones)
        );

        /*   if(in_array('obras_supervisor',toba::usuario()->get_perfiles_funcionales()) or in_array('admin',toba::usuario()->get_perfiles_funcionales()))
           {
               $parametros_ayuda['p2_contenido'] = utf8_encode('Es administrador');
               $parametros_ayuda['p2'] = utf8_encode('1');
           }*/

        $this->evento('ayuda')->vinculo()->set_parametros($parametros_ayuda);
        /** --------------------------------------------------------------------------------------------------------------------------------------- **/

		$sql = 'Select * From tribunal.tribunales order by id_tribunal';
		$rs = toba::db()->consultar($sql);

		foreach($rs as $i => $fila)
		{
			if(strlen($fila['id_tribunal']) < 2)
			{ 
				$fila['id_tribunal'] = '0'.$fila['id_tribunal'];
				$fila['descripcion'] = $fila['descripcion'];
			}
			if(count($fila) > 0)
			{
				$rs[$i]['id_tribunal'] = $fila['id_tribunal'];
				$rs[$i]['descripcion'] = $fila['descripcion'];
			}
		}
		
		$cuadro->set_datos($rs);
	}

	function evt__cd_tribunales__seleccion($seleccion)
	{	
		$this->dep('dt_tribunales')->cargar($seleccion);
		$this->dep('dt_tribunales')->set_cursor(0);
	}

	//-----------------------------------------------------------------------------------
	//---------------------------------- FORMULARIO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_tribunales(staf_ei_formulario $form)
	{
		if( $this->dep('dt_tribunales')->hay_cursor())
		{
			$datos = $this->dep('dt_tribunales')->get();

//--------------------------------- USUARIOS ------------------------------------------
            $form->set_solo_lectura(array('id_tribunal','descripcion','juez','secretario','prosecretario'));
            $form->evento('alta')->ocultar();
            $form->evento('baja')->ocultar();
            $form->evento('modificacion')->ocultar();

            $this->s__usuario = consultas::get_pf(); //ei_arbol($this->s__usuario);
            if($this->s__usuario == 'superusuario')
            {
                $form->evento('alta')->mostrar();
                $form->evento('baja')->mostrar();
                $form->evento('modificacion')->mostrar();
                $this->dep('frm_tribunales')->set_solo_lectura(array('descripcion','juez','secretario','prosecretario'),false);
            }
//---------------------------------------------------------------------------------------
			$form->set_datos($datos);
		}
	}

	function evt__frm_tribunales__alta($datos)
	{
		//convierto todos los string en mayúscula
        if($datos['descripcion'])
		    $datos['descripcion'] = mb_convert_case($datos['descripcion'], MB_CASE_UPPER, "LATIN1");
		$datos['juez'] = mb_convert_encoding($datos['juez'], "LATIN1");
        if($datos['secretario'])
		    $datos['secretario']= mb_convert_encoding($datos['secretario'], "LATIN1");
        if($datos['prosecretario'])
            $datos['prosecretario']= mb_convert_encoding($datos['prosecretario'], "LATIN1");

        //Verifico si el juez o secretario ya existe en la tabla
        $comprobar = "Select juez
                      From tribunal.tribunales
                      Where juez ilike '%".$datos['juez']."%'";
        $rs = toba::db()->consultar($comprobar);
        if(count($rs) == 0)
        {
            $this->dep('dt_tribunales')->nueva_fila($datos);
            $this->sincronizar();
        }elseif(count($rs) > 0)
        {
            toba::notificacion()->error('El Juez ya pertenece a otro juzgado');
            $this->dep('dt_tribunales')->resetear();
        }
	}

	function evt__frm_tribunales__baja()
	{
		$this->dep('dt_tribunales')->set();
		$this->sincronizar();
	}

	function evt__frm_tribunales__modificacion($datos)
	{
        //convierto todos los string en mayúscula
        if($datos['descripcion'])
            $datos['descripcion'] = mb_convert_case($datos['descripcion'], MB_CASE_UPPER, "LATIN1");
        $datos['juez'] = mb_convert_encoding($datos['juez'], "LATIN1");
        if($datos['secretario'])
            $datos['secretario']= mb_convert_encoding($datos['secretario'], "LATIN1");
        if($datos['prosecretario'])
            $datos['prosecretario']= mb_convert_encoding($datos['prosecretario'], "LATIN1");

        //Verifico si el juez ya existe en la tabla
        $comprobar = "Select juez, id_tribunal
                      From tribunal.tribunales
                      Where juez ilike '%".$datos['juez']."%' and id_tribunal != ".$datos['id_tribunal'];
        $rs = toba::db()->consultar($comprobar);

        if(count($rs) == 0)
        {
            $this->dep('dt_tribunales')->set($datos);
            $this->sincronizar();

        }elseif(count($rs) > 0)
        {
            toba::notificacion()->error('El Juez ya pertenece a otro juzgado');
            $this->dep('dt_tribunales')->resetear();
        }

	}

	function evt__frm_tribunales__cancelar()
	{
		$this->dep('dt_tribunales')->resetear();
	}

	function sincronizar()
	{
		try
		{
			$this->dep('dt_tribunales')->sincronizar();
			$this->dep('dt_tribunales')->resetear();
		}
		catch(Exception $e)
		{
			toba::notificacion()->error('Error al sincronizar con la Base de Datos');
		}
	}

}
?>