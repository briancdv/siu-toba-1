<?php
/**
 * Created by Marina Barrios
 * DATE: 31/07/17
 * En el cuadro se muestran todos los exptes que NO estan confirmados, cuya UBICACION actual puede ser:
 * 'Mesa de EyS', 'Gestion de deudas' o 'Procuracion y legales'
 * y el MOTIVO pueder ser: 'Salida de Apremio', 'Salida de Mesa de EyS' o 'Salida de Gestion de deudas' respectivamente
 * Se crea un perfil 'acor_mesa' para que Mesa de EyS pueda acceder a este menu
 * y un perfil 'acor_carga_nov' para Gestion de deudas y Procuracion y legales
 */

require_once('consultas/consultas.php');

class confirmar_exp extends staf_ci
{
    protected $s__filtro = null;
    protected $s__where = '1=1';
    protected $s__check = null;
    protected $s__motivo = null; //-- guarda el motivo para pasarlo al sql --//

	//-----------------------------------------------------------------------------------
	//------------------------------------ FILTRO ---------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__fi_conf_acor(staf_ei_filtro $filtro)
	{
        $filtro->columna('id_mov_destino')->set_solo_lectura();
        $this->s__filtro['id_mov_destino']['condicion'] = 'es_igual_a';
        $usuario = consultas::get_pf();
        $dep = consultas::get_dep_habilitada();

        if($usuario == 'acor_mesa')
        { //-- Mesa de EyS puede ver los exptes q el destino es: Mesa de EyS
          //   y el motivo es: 9:Salida de Apremio, 11:Salida de Gestion de deudas y 12:Salida de Procuracion--//
            if($dep['id_dep'] == 3)
            {
                $this->s__filtro['id_mov_destino']['valor'] = 5;
                $this->s__motivo = 'and (m1.id_motivo = 9 or m1.id_motivo = 11 or m1.id_motivo = 12)';
            }
        }
        elseif($usuario == 'acor_carga')
        {
            if($dep['id_dep'] == 1)
            { //-- la DIR. DE GESTION DE DEUDAS puede ver los exptes q el destino es: Gestion de deudas y el motivo es: Salida de Mesa de EyS--//
                $this->s__filtro['id_mov_destino']['valor'] = 6;
                $this->s__motivo = 'and m1.id_motivo = 10'; //-- 10: Salida de Mesa de EyS --//
            }
            elseif($dep['id_dep'] == 2)
            { //-- la DIR. DE PROC Y LEGALES puede ver los exptes q el destino es: Procuracion y legales y el motivo es: Salida de Gestion de deudas--//
                $this->s__filtro['id_mov_destino']['valor'] = 7;
                $this->s__motivo = 'and m1.id_motivo = 11'; //-- 9: Salida de Gestion de Deudas --//
            }
            elseif($dep['id_dep'] == null)
            { //-- si no pertenece a ninguna dep_hab, no puede hacer nada --//
                $filtro->eliminar_columnas(array('id_mov_destino'));
                $filtro->evento('filtrar')->ocultar();
                $this->evento('procesar')->ocultar();
                toba::notificacion()->info('Ud. no pertenece a ninguna dependencia habilitada');
            }
        }
        $filtro->set_datos($this->s__filtro);
	}

	function evt__fi_conf_acor__filtrar($datos)
	{
        $this->s__where = $this->dep('fi_conf_acor')->get_sql_where();
        $this->s__filtro = $datos;
	}

	function evt__fi_conf_acor__cancelar()
	{
        $this->s__filtro = null;
        $this->s__where = '1=1';
	}

	//-----------------------------------------------------------------------------------
	//----------------------------------------- CUADRO ----------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cd_conf_acor(staf_ei_cuadro $cuadro)
	{
        if($this->s__where != '1=1') //--- q solo muestre el cuadro si apretaron el botón Filtrar ---
        {
            $cuadro->desactivar_modo_clave_segura();
            //--- No deben estar confirmados, ubicacion actual en: Acor y el motivo debe ser: salida de Apremio --//
            $sql="Select Distinct on (e1.id_expediente) e1.id_expediente, m1.id_mov, e1.apyn, e1.nrodoc, e1.n_expediente,
                     m1.fecha, m1.id_mov_destino, a.dominio
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
                      tribunal.movimientos m1  on completo.id_expediente = m1.id_expediente and  completo.maximo = m1.id_mov
                     where $this->s__where $this->s__motivo
                     and m1.usu_confirmacion is null and m1.fe_confirmacion is null
                     Order by e1.id_expediente DESC, m1.id_mov DESC Limit 150";

            $rs = toba::db()->consultar($sql);

            if(count($rs) > 0)
            {
                foreach($rs as $i => $fila)
                {
                    if(strlen($fila['nrodoc']) == 11)
                    {
                        $rs[$i]['nrodoc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,8).'-'.substr($rs[$i]['nrodoc'],10,1);
                    }elseif(strlen($fila['nrodoc']) == 10)
                    {
                        $rs[$i]['nrodoc'] = substr($rs[$i]['nrodoc'],0,2).'-'.substr($rs[$i]['nrodoc'],2,7).'-'.substr($rs[$i]['nrodoc'],9,1);
                    }elseif(strlen($fila['nrodoc']) == 8 || strlen($fila['nrodoc']) == 7)
                    {
                        $rs[$i]['nrodoc']= $rs[$i]['nrodoc'];
                    }elseif(strlen($fila['nrodoc']) == 1)
                    {
                        $rs[$i]['nrodoc']= '';
                    }

                    $rs[$i]['n_expediente'] = substr($rs[$i]['n_expediente'],0,4).'-'.substr($rs[$i]['n_expediente'],4,2).'-'.substr($rs[$i]['n_expediente'],6,2).'-'.substr($rs[$i]['n_expediente'],8,6); /** Modifique 14/12 */
                }
                $cuadro->set_datos($rs);
            }else{
                toba::notificacion()->error('No existen expedientes cargados para esa b&uacute;squeda');
                $this->s__filtro = null;
                $this->s__where = '1=1';
            }
        }
	}

	function evt__cd_conf_acor__confirmar($datos)
	{
        $this->s__check = $datos;
	}

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__procesar()
	{
        unset($trans);
        if(!empty($this->s__check))
        {
            foreach($this->s__check as $i => $fila)
            {
                $usuario[$i] = toba::usuario()->get_id();
                $fecha[$i] = date('Y-m-d');
                //--- Se arma una transaccion ---//
                //- Como esta dentro de un foreach se van concatenando las transacciones y se la ejecuta fuera del foreach ---//
                $trans = $trans."UPDATE tribunal.movimientos SET usu_confirmacion = '{$usuario[$i]}',
                                  fe_confirmacion = '{$fecha[$i]}'
                                  where id_mov = {$fila['id_mov']}; ";
            }

            if (isset($trans))
            {
                $trans = "begin; ".$trans." end;";
                toba::notificacion()->info('Los Expedientes han sido confirmados satisfactoriamente');
            }
            toba::db()->ejecutar_transaccion($trans);
            unset($trans);
            $this->s__filtro = null;
            $this->s__where = '1=1';
            $this->s__motivo = null;
        }else{
            toba::notificacion()->info('Debe seleccionar un Expediente para confirmar');
        }
        $this->s__check = null;
	}

	function evt__cancelar()
	{
        $this->s__filtro = null;
        $this->s__where = '1=1';
        $this->s__check = null;
        $this->s__motivo = null;
	}

}
?>