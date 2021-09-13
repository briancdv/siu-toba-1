<?php
class ci_tipo_acciones extends staf_ci
{
	//-----------------------------------------------------------------------------------
	//---- cd_tipo_accion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_tipo_accion(staf_ei_cuadro $cuadro)
	{
		$sql = "SELECT aa.id_tipo_accion,aa.nombre, aa.descripcion,ee.descr_estado FROM tribunal.tipo_accion aa
				LEFT JOIN tribunal.estados ee ON ee.id_estado = aa.id_estado
				WHERE ee.identificadores = 'tipo_accion'
				ORDER BY aa.id_tipo_accion ASC";
		$cuadro->set_datos(toba::db()->consultar($sql));
	}

	function evt__cd_tipo_accion__seleccion($seleccion)
	{
		$this->dep('tipo_accion')->cargar($seleccion);
        $this->dep('tipo_accion')->set_cursor(0);
	}

	//-----------------------------------------------------------------------------------
	//---- fr_tipo_accion ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fr_tipo_accion(staf_ei_formulario $form)
	{
		if($this->dep('tipo_accion')->hay_cursor())
        {
            $datos = $this->dep('tipo_accion')->get();
            $form->set_datos($datos);
        }
	}

	function evt__fr_tipo_accion__alta($datos)
	{
		$this->dep('tipo_accion')->nueva_fila($datos);
        $this->sincronizar();
	}

	function evt__fr_tipo_accion__modificacion($datos)
	{
		$this->dep('tipo_accion')->set($datos);
        $this->sincronizar();
	}

	function evt__fr_tipo_accion__cancelar()
	{
		 $this->dep('tipo_accion')->resetear();
	}

	function sincronizar()
    {
        try
        {
            $this->dep('tipo_accion')->sincronizar();
            $this->dep('tipo_accion')->resetear();
        }
        catch(Exception $e)
        {
            toba::notificacion()->error('Error al sincronizar con la Base de Datos');
        }
    }

}
?>