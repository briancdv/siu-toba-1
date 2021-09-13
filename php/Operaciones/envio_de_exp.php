<?php
/**
 * Created by Marina Barrios
 * DATE: 13/09/17
 */
require_once('consultas/consultas.php');
require_once('_datos/filtros.php');

class envio_de_exp extends staf_ci
{
    protected $s__filtro = null;
    protected $s__where = '1=1';
    protected $s__check = null;
    protected $s__seleccion = null;

	//-----------------------------------------------------------------------------------
	//---- fi_envio ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_envio(staf_ei_filtro $filtro)
	{
        $perfil_datos = toba::manejador_sesiones()->get_perfil_datos();
        if($perfil_datos == null)
        { //-- Si el usuario no tiene un perfil de datos asignado no puede hacer nada --//
            $filtro->evento('filtrar')->ocultar();
            toba::notificacion()->error('Solic&iacute;te al administrador que le asigne un Perfil de Datos para poder operar. Gracias');
        }else{
            $filtro->set_datos($this->s__filtro);
        }
	}

	function evt__fi_envio__filtrar($datos)
	{
        $this->s__where = $this->dep('fi_envio')->get_sql_where();
        $this->s__filtro = $datos;
        $this->dep('fi_envio')->columna('id_mov_destino')->set_solo_lectura();
	}

	function evt__fi_envio__cancelar()
	{
        $this->s__filtro = null;
        $this->s__where = '1=1';
	}

	//-----------------------------------------------------------------------------------
	//---- cd_envio ---------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_envio(staf_ei_cuadro $cuadro)
	{
        if($this->s__where != '1=1')
        {
            $ubi = consultas::get_ubicacion();
            toba::memoria()->set_dato('ubicacion',$ubi['ubicacion']);
            $this->s__filtro['descripcion']['valor'] = $ubi['ubi']; // -- guardo la ubicacion actual para filtrar --//
            $this->s__filtro['id_motivo']['valor'] = $ubi['id_motivo'];
            $filtro = filtros::armo_filtro($this->s__filtro);
            $ci="<font color=\"red\"><b>";
            $cf="</b></font>";
            $sql = "Select Distinct on (e1.id_expediente) e1.id_expediente, m.id_mov, e1.apyn, e1.nrodoc,
                            e1.n_expediente, e1.id_tribunal,m.id_mov_destino,m.id_motivo, t.descripcion as desc_tribunal,
                            m.fecha, m.usu_confirmacion, m.fe_confirmacion, a.dominio,
                            case when p.descripcion='URGENTE' then ' URGENTE ' else 'COMUN' end as desc_prioridad
                            From
                                (select e.id_expediente,max(m.id_mov) as maximo
                                    from tribunal.expediente e
                                    inner join tribunal.movimientos m  on m.id_expediente = e.id_expediente
                                    group by e.id_expediente order by  e.id_expediente) as completo
                            inner join
                                  tribunal.expediente e1 on completo.id_expediente = e1.id_expediente
                            inner join
                                  tribunal.actas a on completo.id_expediente = a.id_expediente
                            inner join
                                  tribunal.prioridades p on e1.id_prioridad = p.id_prioridad
                            inner join
				                  tribunal.movimientos m on completo.id_expediente=m.id_expediente and completo.maximo=m.id_mov
                            inner join
                                  tribunal.tribunales t ON t.id_tribunal = e1.id_tribunal
                            where $filtro
                            and m.usu_confirmacion is not null and m.fe_confirmacion is not null
                            Order by e1.id_expediente DESC, m.id_mov DESC Limit 150"; 
            $rs = toba::db()->consultar($sql);
            if(count($rs) > 0){
                foreach($rs as $i => $fila)
                {
                    if(strlen($fila['nrodoc']) == 11)
                    {
                        $rs[$i]['doc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,8).'-'.substr($rs[$i]['nrodoc'],10,1);
                    }elseif(strlen($fila['nrodoc']) == 10)
                    {
                        $rs[$i]['doc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,7).'-'.substr($rs[$i]['nrodoc'],9,1);
                    }elseif(strlen($fila['nrodoc']) == 8 || strlen($fila['nrodoc']) == 7)
                    {
                        $rs[$i]['doc']= $fila['nrodoc'];
                    }

                    $rs[$i]['n_expediente'] = substr($rs[$i]['n_expediente'],0,4).'-'.substr($rs[$i]['n_expediente'],4,2).'-'.substr($rs[$i]['n_expediente'],6,2).'-'.substr($rs[$i]['n_expediente'],8,6); /*** Modifique 14/12 */
                }
            }
            $cuadro->set_datos($rs);
        }
	}

	function evt__cd_envio__checar($datos)
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
            foreach($this->s__check as $i => $fila)
            {
                $datos[$i]['usuario'] = toba::usuario()->get_id();
                $datos[$i]['fecha'] = date('Y-m-d');
                $datos[$i]['id_mov_destino'] = $this->s__filtro['id_mov_destino']['valor'];
                $datos[$i]['id_motivo'] = $this->s__filtro['id_motivo']['valor'];
                $datos[$i]['id_expediente'] = $fila['id_expediente'];

                $this->dep('dt_movimientos')->nueva_fila($datos[$i]);
                $this->s__seleccion[$i] = $fila;
                $this->sincronizar();
            }
            $this->evt__imprimir($this->s__seleccion);
        }else{
            toba::notificacion()->info('Debe seleccionar un Expediente para enviar');
        }
        $this->s__check = null;
        $this->s__filtro = null;
        $this->s__where = '1=1';
        $this->s__seleccion = null;
	}

	function evt__cancelar()
	{
        $this->s__check = null;
        $this->s__filtro = null;
        $this->s__where = '1=1';
        $this->s__seleccion = null;
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

    function evt__imprimir($exp)
    {
        $opciones = array('width'=> 1000, 'scrollbars' => 1, 'height'=> 1000, 'resizable'=> 1,
            'celda_memoria'=>'nueva','menu'=>0); //-- armo las opciones de la nueva pagina (popup)--
        //--- Genera una url que apunta a una operación de un proyecto ---
        $url = toba::vinculador()->get_url('staf','258000205',array('check'=>json_encode($exp)),$opciones);
        $codigo_js = " abrir_popup('staf', '$url') "; //-- uso la funcion abrir_popup de js ----
        toba::acciones_js()->encolar($codigo_js); //-- envia el js --
    }
}
?>