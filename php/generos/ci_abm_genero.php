<?php
class ci_abm_genero extends libros_ci
{
	//-----------------------------------------------------------------------------------
	//---- Configuraciones --------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	protected $s__datos_filtro;
	function conf__pant_seleccion(toba_ei_pantalla $pantalla)
	{
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}
	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function get_list($where=''){
		$sql=("SELECT t_g.id_genero, t_g.descripcion ,t_e.descripcion as id_estado_nombre
		FROM curlib.genero as t_g LEFT OUTER JOIN cidig.estado as t_e ON (t_g.id_estado = t_e.id_estado) 
			WHERE t_g.id_estado >=1 AND $where");
		return toba::db()->consultar($sql);
	}
	function conf__cuadro(libros_ei_cuadro $cuadro)
	{
		$cuadro->desactivar_modo_clave_segura();
		if (isset($this->s__datos_filtro)) {
			$filtro=$this->dep('filtro')->get_sql_where();
		
			$cuadro->set_datos($this->get_list($filtro));
		} else {
		$cuadro->set_datos(toba::db()->consultar("SELECT t_g.id_genero, t_g.descripcion ,t_e.descripcion as id_estado_nombre
													FROM curlib.genero as t_g LEFT OUTER JOIN cidig.estado as t_e ON (t_g.id_estado = t_e.id_estado) 
														WHERE t_g.id_estado >=1"));
		}
	}

	//-----------------------------------------------------------------------------------
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__formulario(libros_ei_formulario $form)
	{
		if ($this->dep('datos')->esta_cargada()) {
			$form->set_datos($this->dep('datos')->tabla('genero')->get());
		}

	}

	function evt__cuadro__seleccion($seleccion)
	{
		$this->dep('datos')->cargar($seleccion);
		$this->dep('datos')->tabla('genero')->set_cursor(0);
		$this->set_pantalla('pant_edicion');
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('genero')->set($datos);
	}

	//-----------------------------------------------------------------------------------
	//---- filtro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtro(libros_ei_filtro $filtro)
	{
		
		if (isset($this->s__datos_filtro)) {
			$filtro->set_datos($this->s__datos_filtro);
		}
	}

	function evt__filtro__filtrar($datos)
	{
		$this->s__datos_filtro = $datos;
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

}
?>