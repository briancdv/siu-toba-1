<?php
class ci_autores extends libros_ci
{
	protected $s__filtro = null;
	protected $s__where = '1=1';


	//---Funcion tabla
	function tabla() {
		return $this->dep('autor');
	}


	//---Eventos comunes a varias pantallas
	function evt__Agregar()
	{
		$this->tabla()->resetear();
		$this->set_pantalla('pant_edicion');
	}
	//-----------------------------------------------------------------------------------
	//---- cuadro_autores -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_autores(libros_ei_cuadro $cuadro)
	{
		$limite = ' LIMIT 200 ';
		if ($this->s__where <> '1=1') {
			$limite = null;
		}

		$sql = "SELECT aa.*,
					CASE WHEN aa.id_estado >= 1
						THEN ee.descripcion 
							END AS id_estado_descr

				FROM curlib.autor AS aa
					LEFT JOIN curlib.estado AS ee
						ON ee.id_estado = aa.id_estado 
							WHERE ".$this->s__where.$limite;

		$cuadro->set_datos(toba::db()->consultar($sql));
	}

	function evt__cd_autores__seleccion($seleccion)
	{
		$this->tabla()->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- fi_autores -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_autores(libros_ei_filtro $filtro)
	{
		$filtro->set_datos($this->s__filtro);
	}

	function evt__fi_autores__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->s__where = $this->dep('fi_autores')->get_sql_where();
	}

	function evt__fi_autores__cancelar()
	{
		$this->s__filtro = null;
		$this->s__where = '1=1';
		$this->set_pantalla('pant_inicial');
	}

	//-----------------------------------------------------------------------------------
	//---- fr_autores -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fr_autores(libros_ei_formulario $form)
	{
		if ($this->tabla()->esta_cargada()) {
			$datos = $this->tabla()->get();
			list($ap, $nn) = explode(',', $datos['nombre']);
			$datos['nombre_ap'] = trim($ap);
			$datos['nombre_nn'] = trim($nn);
			$form->set_datos($datos);
		}
	}

	function evt__fr_autores__alta($datos)
	{
		$datos['nombre'] = strtoupper(trim($datos['nombre_ap'])) . ',' . ucfirst(strtolower(trim($datos['nombre_nn'])));
		$this->tabla()->nueva_fila($datos);
		$this->tabla()->sincronizar();
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_autores__baja()
	{
		$this->tabla()->set();
		$this->tabla()->sincronizar();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_autores__modificacion($datos)
	{
		$datos['nombre'] = strtoupper(trim($datos['nombre_ap'])).', '.ucfirst(strtolower(trim($datos['nombre_nn'])));
		$this->tabla()->set($datos);
		$this->tabla()->sincronizar();
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_autores__cancelar()
	{
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

}
?>