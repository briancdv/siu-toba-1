<?php
/**
 * Created by Marina Barrios
 * Date: 16/06/16
 */

class ci_inf_trib extends staf_ci
{
	//-----------------------------------------------------------------------------------
	//-------------------------------------- CUADRO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_inf_trib(staf_ei_cuadro $cuadro)
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

        $cuadro->desactivar_modo_clave_segura();
        $sql = "Select tti.id_juzgado, tti.id_tipoi, t.descripcion as tribunal, i.descripcion as infraccion,
                 (CASE WHEN tti.activo = '1' THEN 'SI' ELSE 'NO' END) activo
                From tribunal.trib_tipoinfraccion tti
                Inner Join tribunal.tribunales t on t.id_tribunal = tti.id_juzgado
                Inner Join tribunal.tipo_infraccion i on i.id_tipoi = tti.id_tipoi
                Order By tti.id_juzgado, tti.id_tipoi";
        $rs = toba::db()->consultar($sql);

        $cuadro->set_datos($rs);
	}

	function evt__cd_inf_trib__seleccion($seleccion)
	{
        $this->dep('dt_inf_trib')->cargar($seleccion);
        $this->dep('dt_inf_trib')->set_cursor(0);
        $this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---------------------------------- FORMULARIO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_inf_trib(staf_ei_formulario $form)
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

        if($this->dep('dt_inf_trib')->hay_cursor())
        {
            $datos = $this->dep('dt_inf_trib')->get();
            $datos['id_tribunal'] = $datos['id_juzgado'];
            $form->ef('id_tribunal')->set_solo_lectura();
            $form->ef('id_tipoi')->set_solo_lectura();
            $form->set_datos($datos);
        }
	}

	function evt__frm_inf_trib__alta($datos)
	{
        $sql = "SELECT id_juzgado, id_tipoi, c_comunes, c_urgentes, activo
                  FROM tribunal.trib_tipoinfraccion
                  Where id_juzgado = {$datos['id_tribunal']} and id_tipoi = '".$datos['id_tipoi']."'";
        $rs = toba::db()->consultar($sql);

        if(count($rs) > 0)
        {
            toba::notificacion()->error('Esa combinaci&oacute;n de Infracci&oacute;n y Tribunal ya existe.
                                         Debe buscarlo en el cuadro y modificarlo');
            $this->set_pantalla('pant_inicial');
        }else{
            $datos['c_comunes'] = 0;
            $datos['c_urgentes'] = 0;
            $datos['id_juzgado'] = $datos['id_tribunal'];
            $this->dep('dt_inf_trib')->nueva_fila($datos);
            $this->dep('dt_inf_trib')->sincronizar();
            $this->dep('dt_inf_trib')->resetear();
            toba::notificacion()->info('Datos agregados correctamente');
            $this->set_pantalla('pant_inicial');
        }
	}

	function evt__frm_inf_trib__modificacion($datos)
	{
        $this->dep('dt_inf_trib')->set($datos);
        $this->dep('dt_inf_trib')->sincronizar();
        $this->dep('dt_inf_trib')->resetear();
        $this->set_pantalla('pant_inicial');
	}

	function evt__frm_inf_trib__cancelar()
	{
        $this->dep('dt_inf_trib')->resetear();
        $this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
        $this->dep('dt_inf_trib')->resetear();
        $this->set_pantalla('pant_edicion');
	}

}
?>