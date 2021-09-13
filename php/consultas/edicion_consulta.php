<?php
/**
 * Created by Marina Barrios
 */
class edicion_consulta extends staf_ci
{
    protected $s__numExpe = null;

    function get_relacion()
    {
        return $this->controlador->dep('dr_exp_acta');
    }

//-----------------------------------------------------------------------------------
//--------------------------- FORMULARIO  EXPEDIENTE---------------------------------
//-----------------------------------------------------------------------------------

	function conf__frm_exp(staf_ei_formulario $form)
	{
        if($this->get_relacion()->tabla('expediente')->hay_cursor()) //seleccion
        {
            $datos = $this->get_relacion()->tabla('expediente')->get();

            $datos['n_expediente'] = substr($datos['n_expediente'],0,4).'-'.substr($datos['n_expediente'],4,2).'-'.substr($datos['n_expediente'],6,2).'-'.substr($datos['n_expediente'],8,6); /*** Modifique 14/12 */

            if(isset($datos['id_tribunal'])) // detecta si el tribunal existe o no
            {
                $sql = "Select id_tribunal, descripcion From tribunal.tribunales where id_tribunal = {$datos['id_tribunal']}";
                $rs = toba::db()->consultar($sql);
                if(count($rs) > 0)
                {
                    $datos['descripcion'] = $rs[0]['descripcion'];
                }
            }

            if($datos['c05tipodoc'] == '5')
            {
                $datos['c05tipodoc'] = "DNI";
            }
            elseif($datos['c05tipodoc'] == '6')
            {
                $datos['c05tipodoc'] = "CUIL/CUIT";
            }

            //---- Doy vuelta la fecha
            $datos['fe_alta']=date("d-m-Y",strtotime($datos['fe_alta']));
            $datos['fe_mod']=date("d-m-Y",strtotime($datos['fe_mod']));

            //--- Defino si es un CUIT o DNI y lo muestro en el Formulario --------
            if(strlen($datos['nrodoc']) == 11)
            {
                $datos['nrodoc'] = substr($datos['nrodoc'],0,2).'-'.substr($datos['nrodoc'],2,8).'-'.substr($datos['nrodoc'],10,1);

            }
            elseif(strlen($datos['nrodoc']) == 10)
            {
                $datos['nrodoc'] = substr($datos['nrodoc'],0,2).'-'.'0'.substr($datos['nrodoc'],2,7).'-'.substr($datos['nrodoc'],9,1);

            }

            $form->ef('id_prioridad')->set_solo_lectura();
            $form->ef('id_tipo_persona')->set_solo_lectura();
            $form->ef('identidad_verif')->set_solo_lectura();
            $form->ef('c05tipodoc')->set_solo_lectura();
            $form->ef('nrodoc')->set_solo_lectura();
            $form->ef('sexo')->set_solo_lectura();
            $form->ef('apyn')->set_solo_lectura();
            $form->ef('domicilio')->set_solo_lectura();
            $form->ef('c06id')->set_solo_lectura();
            $form->ef('c07id')->set_solo_lectura();

            $form->set_datos($datos);
        }
	}

//-----------------------------------------------------------------------------------
//-------------------------------CUADRO DE ACTAS ------------------------------------
//-----------------------------------------------------------------------------------

	function conf__cd_actas(staf_ei_cuadro $cuadro)
	{
        $dt_cd = $this->get_relacion()->tabla('actas')->get_filas();//ei_arbol($dt_cd);
        $dt_cd =rs_ordenar_por_columna($dt_cd, array('id_acta'), SORT_ASC);//--- ordeno mi array por el id ---

        if(count($dt_cd) > 0) //-- hay actas cargadas ---
        {
            foreach($dt_cd as $i => $fila)
            {
                if($dt_cd[$i]['id_tramite'] == '')
                {
                    $sql = "Select
                 (Select descripcion From tribunal.medio_constatacion where id_medio= '".$fila['id_medio']."') as medio,
                 (Select descripcion From tribunal.tipo_infraccion where id_tipoi= '".$fila['id_tipoi']."') as tipo";
                }else{
                    $sql = "Select
                 (Select descripcion From tribunal.tramites where id_tramite= '".$fila['id_tramite']."') as desc_tram,
                 (Select descripcion From tribunal.medio_constatacion where id_medio= '".$fila['id_medio']."') as medio,
                 (Select descripcion From tribunal.tipo_infraccion where id_tipoi= '".$fila['id_tipoi']."') as tipo";
                }
                $rs = toba::db()->consultar($sql);

                if(count($rs) > 0)
                {
                    //---Armo el Acta para que lo muestre en el cuadro
                    if($dt_cd[$i]['id_tipoi'] == '00')
                    {
                        $dt_cd[$i]['acta_completa'] = substr($dt_cd[$i]['acta_completa'],0,2).'-'.substr($dt_cd[$i]['acta_completa'],2,2).'-'.substr($dt_cd[$i]['acta_completa'],4,3).'-'.substr($dt_cd[$i]['acta_completa'],7,2).'-'.substr($dt_cd[$i]['acta_completa'],9,6); /*** Modifique 14/12 */

                    }else{
                        $dt_cd[$i]['acta_completa'] = substr($dt_cd[$i]['acta_completa'],0,4).'-'.substr($dt_cd[$i]['acta_completa'],4,2).'-'.substr($dt_cd[$i]['acta_completa'],6,6); /*** Modifique 14/12 */

                    }

                    $dt_cd[$i]['tipo'] = $rs[0]['tipo'];
                    $dt_cd[$i]['medio'] = $rs[0]['medio'];
                    $dt_cd[$i]['desc_tram'] = $rs[0]['desc_tram'];
                }
            }
        }
        $cuadro->set_datos($dt_cd);
	}

//-----------------------------------------------------------------------------------
//-------------------------------- EVENTO DEL CI ------------------------------------
//-----------------------------------------------------------------------------------

	function evt__atras()
	{
        $this->get_relacion()->resetear();
        $this->controlador()->set_pantalla('pant_inicial_consulta');
	}
	//-----------------------------------------------------------------------------------
	//---- cd_estados -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_estados(staf_ei_cuadro $cuadro)
	{
        $dt_cd = $this->get_relacion()->tabla('acciones')->get_filas();
        $dt_acta = $this->get_relacion()->tabla('actas')->get_filas();
        $datos = $this->get_relacion()->tabla('expediente')->get();
        $this->s__numExpe = $datos['n_expediente'];

        if(count($dt_cd) > 0 && count($dt_acta) > 0)
        {
            $this->s__nro_acta = 'existe';
            if(isset($dt_acta[0]['id_tramite'])){

                $this->s__tramite = 'existe';
            }else{

                $this->s__tramite = 'no_existe';
            }


            foreach($dt_cd as $i => $filas){

                $sql_estado = "SELECT tta.nombre FROM tribunal.acciones ta
                LEFT JOIN tribunal.tipo_accion tta ON tta.id_tipo_accion = {$filas['id_tipo_accion']}";

                $rs = toba::db()->consultar($sql_estado);
                if(count($rs) > 0)
                {
                    $dt_cd[$i]['nombre'] = $rs[0]['nombre'];
                   // $dt_cd[$i]['fecha']=date("d-m-Y",strtotime($dt_cd[$i]['fecha']));
                }
            }
        }

        $cuadro->set_datos($dt_cd);

        $cuadro->set_titulo($cuadro->get_titulo(). ' Nro. de Expediente: ' .$this->s__numExpe);
	}

	//-----------------------------------------------------------------------------------
	//---- cd_movimientos ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_movimientos(staf_ei_cuadro $cuadro)
	{
        $dt_cd = $this->get_relacion()->tabla('movimiento')->get_filas();
        

        foreach($dt_cd as $i => $filas){

                if (is_null($filas['id_tribunal'])) 
                {
                    $filas['id_tribunal'] = 'null';

                }

                $sql_movimiento = "select mov.fecha, des.descripcion as destino, mot.descripcion as motivo, trib.descripcion as tribunal, mov.usuario, mov.usu_confirmacion,mov.fe_confirmacion
                        from tribunal.movimientos mov
                        left join tribunal.mov_destinos des on des.id_mov_destino =  {$filas['id_mov_destino']}
                        left join tribunal.mov_motivos mot on mot.id_motivo =  {$filas['id_motivo']}
                        left join tribunal.tribunales trib on trib.id_tribunal =  {$filas['id_tribunal']}
                        where mov.id_expediente = {$filas['id_expediente']}";

                $rs = toba::db()->consultar($sql_movimiento);
                if(count($rs) > 0)
                {
                   $dt_cd[$i]['fecha'] = $rs[0]['fecha'];
                   $dt_cd[$i]['destino'] = $rs[0]['destino'];
                   $dt_cd[$i]['motivo'] = $rs[0]['motivo'];
                   $dt_cd[$i]['tribunal'] = $rs[0]['tribunal'];
                   $dt_cd[$i]['usuario'] = $rs[0]['usuario'];
                   $dt_cd[$i]['usu_confirmacion'] = $rs[0]['usu_confirmacion'];
                   $dt_cd[$i]['fe_confirmacion'] = $rs[0]['fe_confirmacion'];
                   // $dt_cd[$i]['fecha']=date("d-m-Y",strtotime($dt_cd[$i]['fecha']));
                }
            }
        

        $cuadro->set_datos($dt_cd);

        $cuadro->set_titulo($cuadro->get_titulo(). ' del expediente: ' .$this->s__numExpe);
	}

}
?>