<?php
/**
 * Created by Marina Barrios
 * Listado que Acor  envia a Archivo
 * Date: 11/08/17
 */

require_once('consultas/consultas.php');

class listado_envio extends staf_ci
{
    protected $s__filtro = null;
    protected $s__where = '1=1';
    protected $s__check = null;

	//-----------------------------------------------------------------------------------
	//-------------------------------------- FILTRO -------------------------------------
	//-----------------------------------------------------------------------------------

    function conf__fi_listado_envio(staf_ei_filtro $filtro)
    {
        $filtro->set_datos($this->s__filtro);
    }

    function evt__fi_listado_envio__filtrar($datos)
    {
        $this->s__where = $this->dep('fi_listado_envio')->get_sql_where();
        $this->s__filtro = $datos;
    }

    function evt__fi_listado_envio__cancelar()
    {
        $this->s__filtro = null;
        $this->s__where = '1=1';
    }

	//-----------------------------------------------------------------------------------
	//------------------------------------------ CUADRO ---------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_listado_envio(staf_ei_cuadro $cuadro)
	{
        if($this->s__where != '1=1') //--- q solo muestre el cuadro si apretaron el botón Filtrar ---
        {
            $perfil_datos = toba::manejador_sesiones()->get_perfil_datos();
            if($perfil_datos == null)
            { //-- Si el usuario no tiene un perfil de datos asignado no puede hacer nada --//
                $this->evento('procesar')->ocultar();
                toba::notificacion()->error('Solic&iacute;te al administrador que le asigne un
                                             Perfil de Datos para poder operar. Gracias');
            }

            $datos = consultas::get_ubicacion();

            toba::memoria()->set_dato('ubicacion',$datos['ubicacion']);

            $ci="<font color=\"red\"><b>";
            $cf="</b></font>";

            //--- Se listan los Exptes q fueron enviados a la AG, cuyo motivo sea 8/9 (salida de archivo o apremio) ---//
            $sql = "select Distinct on (e1.id_expediente) e1.id_expediente, m1.id_mov, e1.apyn, e1.nrodoc,
                        e1.n_expediente,m1.fecha,a.dominio,m1.id_mov_destino,
                        case when  p.descripcion='URGENTE' then '$ci URGENTE $cf' else 'COMUN' end as desc_prioridad,
                        t.descripcion as desc_trib, d.descripcion as destino
                        From
                            (select e.id_expediente,max(m.id_mov) as maximo
                            from tribunal.expediente e
                            inner join tribunal.movimientos m on m.id_expediente = e.id_expediente
                            group by e.id_expediente order by e.id_expediente) as completo
                        inner join
                                tribunal.expediente e1 on completo.id_expediente = e1.id_expediente
                        inner join
                                tribunal.actas a on completo.id_expediente = a.id_expediente
                        inner join
                                tribunal.prioridades p on e1.id_prioridad = p.id_prioridad
                        inner join
                                tribunal.tribunales t on e1.id_tribunal = t.id_tribunal
                        inner join
                            tribunal.movimientos m1 on completo.id_expediente=m1.id_expediente and completo.maximo=m1.id_mov
                        inner join
                                tribunal.mov_destinos d on m1.id_mov_destino = d.id_mov_destino
                        where $this->s__where {$datos['motivo']} and m1.usu_confirmacion is null and m1.fe_confirmacion is null
                        Order by e1.id_expediente DESC, m1.id_mov DESC Limit 300";

            $rs = toba::db()->consultar($sql);

            if(count($rs) > 0)
            {
                foreach($rs as $i => $fila)
                {
                    if(strlen($fila['nrodoc']) == 11)
                    {
                        $rs[$i]['nrodoc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,8).'-'.
                            substr($rs[$i]['nrodoc'],10,1);
                    }elseif(strlen($fila['nrodoc']) == 10)
                    {
                        $rs[$i]['nrodoc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,7).'-'.
                            substr($rs[$i]['nrodoc'],9,1);
                    }elseif(strlen($fila['nrodoc']) == 8 || strlen($fila['nrodoc']) == 7)
                    {
                        $rs[$i]['nrodoc']= $rs[$i]['nrodoc'];
                    }elseif(strlen($fila['nrodoc']) == 1)
                    {
                        $rs[$i]['nrodoc']= '';
                    }

                    $rs[$i]['n_expediente'] = substr($rs[$i]['n_expediente'],0,4).'-'.
                        substr($rs[$i]['n_expediente'],4,2).'-'.substr($rs[$i]['n_expediente'],6,2).'-'.
                        substr($rs[$i]['n_expediente'],8,6);                              /** Modifique 14/12 */
                }
                $cuadro->desactivar_modo_clave_segura();
                $cuadro->set_datos($rs);
            }else{
                toba::notificacion()->error('No existen expedientes cargados para esa b&uacute;squeda');
                $this->s__filtro = null;
                $this->s__where = '1=1';
            }
        }
	}

    function evt__cd_listado_envio__imprimir($datos)
    {
        $this->s__check = $datos;
    }

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
        if(!empty($this->s__check))
        {
            $opciones = array('width'=> 1000, 'scrollbars' => 1, 'height'=> 1000, 'resizable'=> 1,
                'celda_memoria'=>'nueva','menu'=>0); //-- armo las opciones de la nueva pagina (popup)--
            //--- Genera una url que apunta a una operación de un proyecto ---
            $url=toba::vinculador()->get_url('staf','258000205',array('check'=>json_encode($this->s__check)),$opciones);
            $codigo_js = " abrir_popup('staf', '$url') "; //-- uso la funcion abrir_popup de js ----
            toba::acciones_js()->encolar($codigo_js); //-- envia el js --
            //--258000066: Operacion-> Imprimir Listado de envio en Acciones --//
            if($this->s__filtro){
                toba::memoria()->set_dato('titulo',$this->s__filtro);
            }

        }else{
            toba::notificacion()->info('Debe seleccionar un Expediente');
        }
        $this->s__check = null;
        $this->s__filtro = null;
        $this->s__where = '1=1';
	}
}
?>