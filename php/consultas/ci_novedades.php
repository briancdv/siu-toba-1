<?php
/**
 * Created by Marina Barrios
 * Date: 01/08/17
 * Solo se llega a esta porcion de codigo si el usuario tiene perfil 'acor_carga_nov' y pertenece a alguna de las
 * dependencias habilitadas.
 * Le permite cargar novedades al expediente determinando a q dependencia pertenece el usuario.
 */
require_once('consultas/consultas.php');

class ci_novedades extends staf_ci
{
    protected $s__datos = null;
    protected $s__cobrado = null;

    function get_relacion()
    {
        return $this->controlador()->dep('dr_exp_acta');
    }
	//-----------------------------------------------------------------------------------
	//-------------------------------------- CUADRO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_nov(staf_ei_cuadro $cuadro)
	{
        $dt_cd = $this->get_relacion()->tabla('novedad')->get_filas();
        $dt_cd = rs_ordenar_por_columna($dt_cd, array('fecha_nov'), SORT_DESC);//--- ordeno mi array por el id ---
        $this->s__cobrado = null;
        if(count($dt_cd) > 0)
        {
            foreach($dt_cd as $i => $fila)
            {
                if($fila['cobrado'] == 'SI')
                { //-- guardo para q en el form le autocomplete con SI y no pueda modificarlo--//
                    $this->s__cobrado = 'SI';
                }

                if($dt_cd[$i]['procurador'] == '')
                {
                    $sql = "Select c01leyen From tribunal.dependencias_habilitadas Where id_dep = {$fila['id_dep']}";
                }else{
                    $sql = "Select
	                        (Select c01leyen From tribunal.dependencias_habilitadas Where id_dep = {$fila['id_dep']}),
	                        (Select apyn From tribunal.procuradores Where dni = {$fila['procurador']})";
                }

                $rs = toba::db()->consultar($sql);

                if(count($rs) > 0)
                {
                    $dt_cd[$i]['c01leyen'] = $rs[0]['c01leyen'];
                    $dt_cd[$i]['apyn'] = $rs[0]['apyn'];
                }
            }
        }
        $cuadro->desactivar_modo_clave_segura();
        $cuadro->set_datos($dt_cd);
        $this->s__datos = $dt_cd;

        //----- traigo un dato de otro DT para armar el titulo del cuadro --//
        $expte = $this->get_relacion()->tabla('expediente')->get_columna('n_expediente');
        $expte = substr($expte,0,4).'-'.substr($expte,4,2).'-'.substr($expte,6,2).'-'.substr($expte,8,6);;
        $cuadro->set_titulo($cuadro->get_titulo()." ".$expte);
	}

	function evt__cd_nov__seleccion($seleccion)
	{
        //No se carga la seleccion xq ya está cardaga en el DT, x eso solo se setea el cursor
       $this->get_relacion()->tabla('novedad')->set_cursor($seleccion);
	}

    function conf_evt__cd_nov__imprimir(toba_evento_usuario $evento, $fila)
    {
        //-- agrego el id_nov al evento para recibirlo y mostrarlo en la impresion --//
        $evento->vinculo()->agregar_parametro('nov',$this->s__datos[$fila]['id_nov']);
    }

	//-----------------------------------------------------------------------------------
	//------------------------------------ FORMULARIO -----------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_nov(staf_ei_formulario $form)
	{
        if($this->get_relacion()->tabla('novedad')->hay_cursor())
        {
            $datos = $this->get_relacion()->tabla('novedad')->get();
           //-- solamente el usuario q creo la novedad puede modificarlo --//
            if($datos['usu_alta'] != toba::usuario()->get_id())
            {
                $form->ef('cobrado')->set_solo_lectura();
                $form->ef('procurador')->set_solo_lectura();
                $form->ef('observaciones')->set_solo_lectura();
                $form->evento('modificacion')->ocultar();
            }
            $rs = toba::db()->consultar("Select n_expediente From tribunal.expediente Where id_expediente ='".$datos['id_expediente']."'");
            $datos['n_expediente'] = substr($rs[0]['n_expediente'],0,4).'-'.substr($rs[0]['n_expediente'],4,2).'-'.
                                     substr($rs[0]['n_expediente'],6,2).'-'.substr($rs[0]['n_expediente'],8,6);
            $form->ef('n_expediente')->set_solo_lectura();
            $datos['fecha_nov']=date("Y-m-d",strtotime($datos['fecha_nov']));
            $form->ef('fecha_nov')->set_solo_lectura();
            $form->ef('id_dep')->set_solo_lectura();
            $form->descolapsar();
            $form->set_datos($datos);
        }else
        {
            $form->colapsar();
            //----- traigo un dato de otro DT
            $datos = $this->get_relacion()->tabla('expediente')->get_columna('n_expediente');
            $datos = substr($datos,0,4).'-'.substr($datos,4,2).'-'.substr($datos,6,2).'-'.substr($datos,8,6);
            //--- Mando al form el nro de exp
            $form->ef('n_expediente')->set_opciones($datos);
            $form->ef('n_expediente')->set_solo_lectura();
            $form->ef('fecha_nov')->set_estado(date('Y-m-d'));

            $dep = consultas::get_dep_habilitada();
            $form->ef('id_dep')->set_estado($dep['id_dep']);
            $form->ef('id_dep')->set_solo_lectura();
        }
        if($this->s__cobrado == 'SI')
        {
            $form->ef('cobrado')->set_solo_lectura();
            $form->ef('cobrado')->set_estado('SI');
        }
	}

	function evt__frm_nov__alta($datos)
	{
        $datos['id_expediente'] = $this->get_relacion()->tabla('expediente')->get_columna('id_expediente');
        $datos['usu_alta'] = toba::usuario()->get_id();
        $this->get_relacion()->tabla('novedad')->nueva_fila($datos);
        $this->sincronizar();
	}

	function evt__frm_nov__baja()
	{
        //-- No está difinido si van a poder eliminar una novedad --//
	}

	function evt__frm_nov__modificacion($datos)
	{
        $datos['fe_mod'] = date('Y-m-d');
        $datos['usu_mod'] = toba::usuario()->get_id();
        $this->get_relacion()->tabla('novedad')->set($datos);
        $this->sincronizar();
	}

	function evt__frm_nov__cancelar()
	{
        $this->get_relacion()->tabla('novedad')->resetear_cursor();
        $this->colapsar();
	}

    function sincronizar()
    {
        try{
            $this->get_relacion()->sincronizar();
            $this->get_relacion()->tabla('novedad')->resetear_cursor();
            $this->colapsar();
            $this->s__cobrado = null;
        }
        catch(Exception $e)
        {
            toba::notificaciones()->warning('Error al sincronizar con la Base de Datos');
        }
    }

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__cancelar()
	{
        $this->get_relacion()->resetear();
        $this->controlador()->set_pantalla('pant_inicial_consulta');
        $this->s__datos = null;
        $this->s__cobrado = null;
	}

}
?>