<?php
/**
 * Created by Marina Barrios
 * Date: 01/08/17
 * ABM de Dependencias habilitadas a agregar novedades al expediente
 */
class ci_dep_habilitadas extends staf_ci
{
    protected $s__filtro = null;
    protected $s__where = "1=1";

	//-----------------------------------------------------------------------------------
	//-------------------------------------- CUADRO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_dep_hab(staf_ei_cuadro $cuadro)
	{
        $sql = "SELECT id_dep, c01depresu AS codigo, c01leyen AS secretaria, estado FROM tribunal.dependencias_habilitadas
                WHERE $this->s__where";
        $rs = toba::db()->consultar($sql);
        $cuadro->set_datos($rs);
	}

	function evt__cd_dep_hab__seleccion($seleccion)
	{
        $this->dep('dt_dependencias')->cargar($seleccion);
        $this->dep('dt_dependencias')->set_cursor(0);
	}

	//-----------------------------------------------------------------------------------
	//-------------------------------------- FORMULARIO ---------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_dep_hab(staf_ei_formulario $form)
	{
        if($this->dep('dt_dependencias')->hay_cursor())
        {
            $datos = $this->dep('dt_dependencias')->get();
            $form->set_datos($datos);
        }
	}

	function evt__frm_dep_hab__alta($datos)
	{
        $datos['c01leyen'] = $this->get_depNombre($datos['c01depresu']);
        $this->dep('dt_dependencias')->nueva_fila($datos);
        $this->sincronizar();
	}

	function evt__frm_dep_hab__modificacion($datos)
	{
        $datos['c01leyen'] = $this->get_depNombre($datos['c01depresu']);
        $this->dep('dt_dependencias')->set($datos);
        $this->sincronizar();
	}

	function evt__frm_dep_hab__cancelar()
	{
        $this->dep('dt_dependencias')->resetear();
	}

    function sincronizar()
    {
        try
        {
            $this->dep('dt_dependencias')->sincronizar();
            $this->dep('dt_dependencias')->resetear();
        }
        catch(Exception $e)
        {
            toba::notificacion()->error('Error al sincronizar con la Base de Datos');
        }
    }

    function get_dep($id)
    {
       $sql ="select c01depresu, c01leyen AS leyen from public.scm001 where c01depresu = '$id'";
       $dep = toba::db()->consultar($sql);
       return $dep[0]['leyen'];
    }

    function get_depNombre($id){

        $sql ="select c01leyen AS leyenda from public.scm001 where c01depresu = '$id'";
        $dep = toba::db()->consultar($sql);
        return $dep[0]['leyenda'];
    }
	//-----------------------------------------------------------------------------------
	//---- fi_dep_hab -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_dep_hab(staf_ei_filtro $filtro)
	{
        $filtro->set_datos($this->s__filtro);
	}

	function evt__fi_dep_hab__filtrar($datos)
	{
        $this->s__where = $this->dep('fi_dep_hab')->get_sql_where();
        $this->s__filtro = $datos;
	}

	function evt__fi_dep_hab__cancelar()
	{
        $this->s__filtro = null;
        $this->s__where = "1=1";
	}

}
?>