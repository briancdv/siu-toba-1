<?php
class ci_categorias extends libros_ci
{
	protected $s__filtro = null;
	protected $s__where = '1=1';

	//---Funcion tabla
	function tabla() 
	{
		return $this->dep('categoria');
	}

	//---- Eventos comunes a varias pantallas
	function evt__Agregar()
	{
		$this->tabla()->resetear();
		$this->set_pantalla('pant_edicion');
	}

	function evt__cancelar()
	{
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	//-------------------------------------------------
	//---- Cuadro - Categorias
	function conf__cd_categorias(libros_ei_cuadro $cuadro)
	{
		$limite = ' LIMIT 200 ';

		if ($this->s__where <> '1=1'){
			$limite = null;
		}

		$sql = "SELECT ca.*,
					CASE WHEN ca.id_estado >= 1
							THEN ee.descripcion 
								END AS id_estado_descr
				FROM curlib.categoria AS ca
					LEFT JOIN curlib.estado AS ee
						ON ee.id_estado = ca.id_estado
							WHERE ".$this->s__where.$limite;

		$cuadro->set_datos(toba::db()->consultar($sql));
	}

	function evt__cd_categorias__seleccion($seleccion)
	{
		$this->tabla()->cargar($seleccion);
		$this->set_pantalla('pant_edicion');
	}

	//-------------------------------------------------
	//---- Filtro - Categorias
	function conf__fi_categorias(libros_ei_filtro $filtro)
	{
		$filtro->set_datos($this->s__filtro);
	}

	function evt__fi_categorias__filtrar($datos)
	{
		$this->s__filtro = $datos;
		$this->s__where = $this->dep('fi_categorias')->get_sql_where();
	}

	function evt__fi_categorias__cancelar()
	{
		$this->s__filtro = null;
		$this->s__where = '1=1';
		$this->set_pantalla('pant_inicial');
	}

	//-------------------------------------------------
	//---- Formulario - categorias 
	function conf__fr_categorias(libros_ei_formulario $form)
	{
		if($this->tabla()->esta_cargada()){
			$datos = $this->tabla()->get();
			$form->set_datos($datos);
		}
	}

	function evt__fr_categorias__alta($datos)
	{
		$this->tabla()->nueva_fila($datos);
		$this->tabla()->sincronizar();
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_categorias__baja($datos)
	{
		$this->tabla()->set();
		$this->tabla()->sincronizar();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_categorias__modificacion($datos)
	{
		$this->tabla()->set($datos);
		$this->tabla()->sincronizar();
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}

	function evt__fr_categorias__cancelar()
	{
		$this->tabla()->resetear();
		$this->set_pantalla('pant_inicial');
	}



}
?>