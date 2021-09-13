<?php
class ci_edicion extends staf_ci
{
    protected $s__usuario = false;

	function get_relacion()
	{
		return $this->controlador->dep('dr_inf_tram');
	}
	
	//-----------------------------------------------------------------------------------
	//---------Pantalla Edicion de Infracciones (pant_ed_inf)----------------------------
	//-----------------------------------------------------------------------------------
	//---- frm_infraccion_edicion -------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_infraccion_edicion(staf_ei_formulario $form)
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

		if($this->get_relacion()->tabla('tipo_inf')->hay_cursor())
		{
			$datos = $this->get_relacion()->tabla('tipo_inf')->get();

//--------------------------------- USUARIOS ------------------------------------------
            $form->set_solo_lectura(array('id_tipoi','descripcion'));
            $form->evento('baja')->ocultar();
            $form->evento('modificacion')->ocultar();

            $this->s__usuario = $this->get_pf(); //ei_arbol($this->s__usuario);
            if($this->s__usuario == 'supervisor' OR $this->s__usuario == 'super')
            {
                $form->evento('baja')->mostrar();
                $form->evento('modificacion')->mostrar();
                $this->dep('frm_infraccion_edicion')->set_solo_lectura(array('descripcion'),false);
            }
//--------------------------------------------------------------------------------------
			$form->set_datos($datos);
		}
	}
	
	function evt__frm_infraccion_edicion__baja()
	{
		$this->get_relacion()->tabla('tipo_inf')->set();
		$this->sincronizar();
        $this->controlador()->set_pantalla('tipos_inf');
	}

	function evt__frm_infraccion_edicion__modificacion($datos)
	{
		$datos['descripcion'] = mb_convert_case($datos['descripcion'], MB_CASE_UPPER, "LATIN1");
		$this->get_relacion()->tabla('tipo_inf')->set($datos);
	}

	function evt__frm_infraccion_edicion__cancelar()
	{
		$this->get_relacion()->tabla('tipo_inf')->resetear();
		$this->controlador()->set_pantalla('tipos_inf');
	}
	
	function sincronizar()
	{
		try
		{
			$this->get_relacion()->sincronizar();
			$this->get_relacion()->resetear();
		}
		catch(Exception $e)
		{
			toba::notificacion()->error('Error al sincronizar con la Base de Datos',$e);
			$this->controlador()->set_pantalla('tipos_inf');
		}
	}
	
	//-----------------------------------------------------------------------------------
	//----------------- PANTALLA PARA AGREGAR TRAMITES (pant_add_tram) ------------------
	//-----------------------------------------------------------------------------------
	
	//------------------------------------ CUADRO ---------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_tram_asoc(staf_ei_cuadro $cuadro)
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

		$datos = $this->get_relacion()->tabla('tram_inf')->get_filas();
		if(count($datos) > 0)
		{
			foreach($datos as $i => $fila)
			{
				$sql = "Select descripcion From tribunal.tramites where id_tramite = '".$fila['id_tramite']."'";
				$rs = toba::db()->consultar($sql);
				$datos[$i]['descripcion'] = $rs[0]['descripcion'];
			}
		}
		$cuadro->set_datos($datos);
        $titulo = $this->controlador()->get_descripcion_inf();
        $cuadro->set_titulo($cuadro->get_titulo()." ".$titulo);
	}

    function evt__cd_tram_asoc__seleccion($seleccion)
    {
        $this->get_relacion()->tabla('tram_inf')->set_cursor($seleccion);
    }

	//-----------------------------------------------------------------------------------
	//---- frm_tramites -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_tramites(staf_ei_formulario $form)
	{	
		if($this->get_relacion()->tabla('tram_inf')->hay_cursor())
		{
			$datos = $this->get_relacion()->tabla('tram_inf')->get();

//--------------------------------- USUARIOS ------------------------------------------

            $form->evento('baja')->ocultar();
            $this->evento('procesar')->ocultar();

            $this->s__usuario = $this->get_pf();
            if($this->s__usuario == 'supervisor' OR $this->s__usuario == 'super')
            {
                $form->evento('baja')->mostrar();
                $this->evento('procesar')->mostrar();
            }
//--------------------------------------------------------------------------------------
			$form->set_datos($datos);
		}
	}

	function evt__frm_tramites__alta($datos)
	{
		$this->get_relacion()->tabla('tram_inf')->nueva_fila($datos);
	}

    function evt__frm_tramites__baja()
    {
        $this->get_relacion()->tabla('tram_inf')->set();
    }

    function evt__frm_tramites__cancelar()
    {
        $this->get_relacion()->tabla('tram_inf')->resetear_cursor();
    }

    function get_pf()
    {
        $usuario = false;
        $perfil = toba::usuario()->get_perfiles_funcionales();
        if(in_array('super',$perfil))
        {
            $usuario = 'super';
        }elseif(in_array('supervisor',$perfil))
        {
            $usuario = 'supervisor';
        }
        return $usuario;
    }

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
        try
        {
            $this->get_relacion()->sincronizar();
            $this->get_relacion()->resetear();
            $this->controlador()->set_pantalla('tipos_inf');
        }
        catch(Exception $e)
        {
            toba::notificacion()->error('Error al sincronizar con la Base de Datos', $e);
        }
	}
}
?>