<?php
/**
 * Created by Marina Barrios
 */

class ci_tipo_infraccion extends staf_ci
{
    protected $s__inf_desc = null;

	function rel()
	{
		return $this->dep('dr_inf_tram');
	}
	
	//-----------------------------------------------------------------------------------
	//------------------------------------ CUADRO ---------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_tipo_infraccion(staf_ei_cuadro $cuadro)
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

		$sql = 'Select * From tribunal.tipo_infraccion';
		$rs = toba::db()->consultar($sql);
		$cuadro->set_datos($rs);
	}

	function evt__cd_tipo_infraccion__seleccion($seleccion)
	{	
		$this->rel()->cargar($seleccion);
		$this->rel()->tabla('tipo_inf')->set_cursor(0);
        $inf = $this->rel()->tabla('tipo_inf')->get();
        $this->s__inf_desc = $inf['descripcion']; //-- guardo la infraccion p pasarle al cuadro de sus tramites --//
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//--------------------------------- FORMULARIO --------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__frm_infraccion_alta__alta($datos)
	{
		if(strlen($datos['id_tipoi']) < 2)
		{
			$datos['id_tipoi']= '0'.$datos['id_tipoi'];
		}
		$datos['descripcion'] = mb_convert_case($datos['descripcion'], MB_CASE_UPPER, "LATIN1");
		$this->rel()->tabla('tipo_inf')->nueva_fila($datos);
		$this->sincronizar();
	}

	function sincronizar()
	{
		try
		{
			$this->rel()->sincronizar();
			$this->rel()->resetear();
		}
		catch(Exception $e)
		{
			toba::notificacion()->error('Error al sincronizar con la Base de Datos');
			$this->controlador()->set_pantalla('tipos_inf');
		}
	}

    function get_descripcion_inf()
    {
        return $this->s__inf_desc;
    }
}
?>