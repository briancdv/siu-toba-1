<?php
class ci_editoriales_ed extends libros_ci
{
	function rel()
	{
		return $this->controlador()->rel();
	}

	function evt__Agregar()
	{
		try{
			$this->rel()->sincronizar();
			$this->rel()->resetear();
			$this->controlador()->set_pantalla('pant_inicial');
		}
		catch(toba_error $e){
			toba::notificacion()->error($e->get_mensaje_motor());
		}
	}

	function evt__Cancelar()
	{
	}

	//-----------------------------------------------------------------------------------
	//---- fr_editorial -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fr_editorial(libros_ei_formulario $form)
	{
		if($this->rel()->tabla('editorial')->hay_cursor()){
			$form->set_datos($this->rel()->tabla('editorial')->get());
		}
	}

	function evt__fr_editorial__alta($datos)
	{
		$this->rel()->tabla('editorial')->nueva_fila($datos);
		$this->rel()->sincronizar();
		$this->rel()->resetear();
		$this->controlador()->set_pantalla('pant_inicial');
	}

	function evt__fr_editorial__baja()
	{
		$this->rel()->tabla('editorial')->eliminar_filas();
		$this->rel()->sincronizar();
		$this->controlador()->set_pantalla('pant_inicial');
	}

	function evt__fr_editorial__modificacion($datos)
	{
		$this->rel()->tabla('editorial')->set($datos);
		$this->rel()->sincronizar();
		$this->rel()->resetear();
		$this->controlador()->set_pantalla('pant_inicial');
	}

	function evt__fr_editorial__cancelar()
	{
	}

}
?>