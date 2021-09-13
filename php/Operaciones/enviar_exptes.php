<?php
/**
 * Created by Marina Barrios
 * DATE: 22/08/17
 */

require_once('consultas/consultas.php');

class enviar_exptes extends staf_ci
{
    protected $s__usuario = null; //-- guarda el perfil del usuario logueado --//
    protected $s__dep = null; //-- guarda la dependencia a la q pertenece el usuario logueado --//

	//-----------------------------------------------------------------------------------
	//---------------------------------- FORMULARIO -------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__frm_envio(staf_ei_formulario $form)
	{
        $perfil_datos = toba::manejador_sesiones()->get_perfil_datos();
        if($perfil_datos == null)
        { //-- Si el usuario no tiene un perfil de datos asignado no puede hacer nada --//
            $form->evento('alta')->ocultar();
            $this->evento('procesar')->ocultar();
            toba::notificacion()->error('Solic&iacute;te al administrador que le asigne un Perfil de Datos para poder operar. Gracias');
        }
	}

	function evt__frm_envio__alta($datos)
	{
        if ($datos['n_expediente'] != null)
        {//-- si n_expediente fue usado en el form --//
            //-- si ingresan el nro de exp incompleto, resetea el cursor y limpia las variables
            if(strlen($datos['n_expediente']) < 14)  /** Modifique 14/12 */
            {
                toba::notificacion()->error('Debe ingresar el Nro. de Expediente completo, con o sin guiones');
                //$this->get_relacion()->tabla('expediente')->resetear_cursor();
            }else{
                $separar = explode('-', $datos['n_expediente']); // elimino guinoes
                if (count($separar) > 1)
                {// si habia ingresado con '-'
                    //--- str_pad = esta funcion se utiliza para rellenar una cadena a una cierta longitud con otra cadena
                    $datos['n_expediente'] = $separar[0].$separar[1].$separar[2].str_pad($separar[3],6,'0',STR_PAD_LEFT);  /** Modifique 14/12 */
                }else{
                    //--- si no ingresó con '-'
                    $datos['n_expediente'] = substr($datos['n_expediente'],0,8).str_pad(substr($datos['n_expediente'],8),6,'0',STR_PAD_LEFT); /** Modifique 14/12 */
                }

                $con = "Select m.id_mov_destino, m.usu_confirmacion, m.fe_confirmacion, d.descripcion, m.fecha, e.id_expediente
                        From tribunal.movimientos m
                        Inner Join tribunal.expediente e On e.id_expediente = m.id_expediente
                        Inner Join tribunal.mov_destinos d On d.id_mov_destino = m.id_mov_destino
                        Where e.n_expediente = ".quote($datos['n_expediente']).
                        " Order by m.id_mov DESC Limit 1";
                $res = toba::db()->consultar($con);
                if(count($res) > 0)
                {
                    //--- verifico la ubicacion del usuario logueado para compararlo con la ubicacion real del expte ---//
                    $ubi = consultas::get_ubicacion();
                    if($res[0]['id_mov_destino'] == $ubi['ubi'] )
                    {
                        if($res[0]['usu_confirmacion'] != '' && $res[0]['fe_confirmacion'] != '')
                        {   //--- el Exp debe estar confirmado para poder moverlo -----
                            if($res[0]['id_mov_destino'] == 5)
                            { //-- si el expte esta en 5:Mesa de EyS (acor), se puede enviar a 6:Gestion o a 3:Apremio
                                if($datos['id_mov_destino'] == 6)
                                {
                                    $datos['destino'] = consultas::get_destino($datos['id_mov_destino']);
                                    $datos['id_motivo'] = 10; //-- Motivo: Salida de Mesa EyS ---
                                }
                                elseif($datos['id_mov_destino'] == 3)
                                {
                                    $datos['destino'] = consultas::get_destino($datos['id_mov_destino']);
                                    $datos['id_motivo'] = 10; //-- Motivo: Salida de Mesa EyS ---
                                }
                            }
                            elseif($res[0]['id_mov_destino'] == 6)
                            {//-- si el expte esta en 6:Gestion de Deudas(acor), se puede enviar a 7:Procuracion o a 5:Mesa de EyS(acor)
                                if($datos['id_mov_destino'] == 7)
                                {
                                    $datos['destino'] = consultas::get_destino($datos['id_mov_destino']);
                                    $datos['id_motivo'] = 11; //-- Motivo: Salida de Gestion ---
                                }
                                elseif($datos['id_mov_destino'] == 5)
                                {
                                    $datos['destino'] = consultas::get_destino($datos['id_mov_destino']);
                                    $datos['id_motivo'] = 11; //-- Motivo: Salida de Gestion ---
                                }
                            }
                            elseif($res[0]['id_mov_destino'] == 7)
                            {//-- si el expte esta en 7:Procuracion(acor), se puede enviar a 5:Mesa de EyS(acor)
                                if($datos['id_mov_destino'] == 5)
                                {
                                    $datos['destino'] = consultas::get_destino($datos['id_mov_destino']);
                                    $datos['id_motivo'] = 12; //-- Motivo: Salida de Procuracion ---
                                }
                            }
                            else{
                                toba::notificacion()->info('En el &uacute;ltimo movimiento del Expediente figura
                                                    que se encuentra en: '.$res[0]['descripcion']);
                            }

                            //--- Rescato el DT para verificar q no exista ya ese expediente cargado en el DT ---//
                            $mov = $this->dep('dt_movimientos')->get_filas();
                            if(count($mov) > 0)
                            {
                                //-- recorro las filas del DT para controlar que el expte ya no este en el cuadro --//
                                foreach($mov as $i => $fila)
                                {
                                    /* recorre el DT, si encuentra uno igual limpia la variable. Despues de recorrer el DT, pregunto si la
                                    variable sigue cargada, si sigue cargada es xq ese expediente no fue cargado aun en el cuadro */
                                    if($mov[$i]['n_expediente'] == $datos['n_expediente'])
                                    {
                                        unset($datos['n_expediente']); //-- limpio el id --//
                                        toba::notificacion()->info('El Expediente ya fu&eacute; cargado en el cuadro');
                                        // $this->s__num_exp = null;
                                    }
                                }
                                if(isset($datos['n_expediente']))
                                {
                                    $datos['usuario'] = toba::usuario()->get_id();
                                    $datos['fecha'] = date('Y-m-d');
                                    $datos['id_expediente'] = $res[0]['id_expediente'];
                                    $this->dep('dt_movimientos')->nueva_fila($datos);//ei_arbol($datos);
                                }
                            }else
                            {
                                $datos['usuario'] = toba::usuario()->get_id();
                                $datos['fecha'] = date('Y-m-d');
                                $datos['id_expediente'] = $res[0]['id_expediente'];
                                $this->dep('dt_movimientos')->nueva_fila($datos);//ei_arbol($datos);
                            }
                        }else{
                            toba::notificacion()->info('El Expediente no se encuentra Confirmado');
                        }

                    }else{
                        toba::notificacion()->info('El Expediente se encuentra en: '.$res[0]['descripcion']);
                    }

                }else{
                    toba::notificacion()->info('El Nro. de Expediente no existe');
                }
            } //-- FIN else strlen($datos['n_expediente']) < 14 --//
        }else{
            toba::notificacion()->error('Ya fué ingresado ese Expediente');
        }
	}

	function evt__frm_envio__cancelar()
	{
        $this->dep('dt_movimientos')->resetear_cursor();
	}

	//-----------------------------------------------------------------------------------
	//--------------------------------------- CUADRO ------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_envio(staf_ei_cuadro $cuadro)
	{
        $mov = $this->dep('dt_movimientos')->get_filas();
        if(count($mov) > 0)
        {
            foreach($mov as $i => $filas)
            {
                $sql ="Select Distinct on (e.id_expediente)p.descripcion as desc_prioridad,e.nrodoc,e.apyn,e.fe_alta, e.n_expediente, e.domicilio, a.dominio,
                            (Select m.fecha
                                from tribunal.movimientos m
                                where m.id_mov = (Select max(m.id_mov) From tribunal.movimientos m)
                            ) as fecha_mov
                      From tribunal.expediente e
                      Inner Join tribunal.prioridades p On e.id_prioridad = p.id_prioridad
                      Inner Join tribunal.actas a On e.id_expediente = a.id_expediente
                      Where e.id_expediente ='".$mov[$i]['id_expediente']."'";
                $rs = toba::db()->consultar($sql);

                if(count($rs) > 0)
                {
                    $mov[$i]['n_expediente'] = substr($filas['n_expediente'],0,4).'-'.
                        substr($filas['n_expediente'],4,2).'-'.substr($filas['n_expediente'],6,2).'-'.
                        substr($filas['n_expediente'],8,6); /** Modifique 14/12 */
                    $mov[$i]['fecha'] = $rs[0]['fecha_mov'];
                    $mov[$i]['desc_prioridad'] = $rs[0]['desc_prioridad'];
                    $mov[$i]['nrodoc'] = $rs[0]['nrodoc'];
                    $mov[$i]['apyn'] = $rs[0]['apyn'];
                    $mov[$i]['domicilio'] = $rs[0]['domicilio'];
                    $mov[$i]['dominio'] = $rs[0]['dominio'];
                    if(strlen($rs[0]['nrodoc']) == 11)
                    {
                        $mov[$i]['nrodoc'] = substr($rs[0]['nrodoc'],0,2).'-'.substr($rs[0]['nrodoc'],2,8).'-'.substr($rs[0]['nrodoc'],10,1);

                    }elseif(strlen($rs[0]['nrodoc']) == 10)
                    {
                        $mov[$i]['nrodoc'] = substr($rs[0]['nrodoc'],0,2).'-'.substr($rs[0]['nrodoc'],2,7).'-'.substr($rs[0]['nrodoc'],9,1);

                    }elseif(strlen($rs[0]['nrodoc']) == 8 || strlen($rs[0]['nrodoc']) == 7)
                    {
                        $mov[$i]['nrodoc']= $rs[0]['nrodoc'];
                    }elseif(strlen($rs[0]['nrodoc']) == 1)
                    {
                        $mov[$i]['nrodoc']= '';
                    }
                }
            }
        }
        $cuadro->set_datos($mov);
	}

	function evt__cd_envio__eliminar($seleccion)
	{
        $this->dep('dt_movimientos')->set_cursor($seleccion); //--- posiciono el cursor
        $this->dep('dt_movimientos')->get_fila($seleccion); //--- selecciono la fila
        $this->dep('dt_movimientos')->set(); //--- seteo la fila con vacio
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
        $mov = $this->dep('dt_movimientos')->get_filas();
        if(count($mov) > 0)
        {
            $this->sincronizar();
            toba::notificacion()->info('La operaci&oacute;n se realiz&oacute; exitosamente');
            $this->s__usuario = null;
            $this->s__dep = null;
        }else{
            toba::notificacion()->info('Debe agregar un Expediente y su Destino');
        }
	}

	function evt__cancelar()
	{
        $this->s__usuario = null;
        $this->s__dep = null;
        $this->dep('dt_movimientos')->resetear();
	}

    function sincronizar()
    {
        try
        {
            $this->dep('dt_movimientos')->sincronizar();
            $this->dep('dt_movimientos')->resetear();
        }
        catch(Exception $e)
        {
            toba::notificacion()->error('Error al sincronizar con la Base de Datos',$e->getMessage());
        }
    }
}
?>