<?php
/**
 * Created by Marina Barrios
 * Permite q los usuarios del Juzgado - Archivo - Apremio - Mesa de Entrada - consulta, puedan solamente consultar los exptes.
 * Además el perfil 'acro_carga_nov' podrá acceder al boton de novedad y cargarlo, siempre y cuando pertenezca a alguna de las
 * dependencias habilitadas a hacerlo.
 */

require_once('consultas/consultas.php');
require_once('_datos/filtros.php');
class consulta_expediente extends staf_ci
{
    protected $s__filtro = null;
    protected $s__where = '1=1';
    protected $s__usuario = null; //-- guarda el perfil del usuario logueado --//

    function rel()
    {
        return $this->dep('dr_exp_acta');
    }

//-----------------------------------------------------------------------------------
//----------------------------------- FILTRO ----------------------------------------
//-----------------------------------------------------------------------------------

	function conf__fi_exp(staf_ei_filtro $filtro)
	{
        $this->s__usuario = consultas::get_pf();

        if($this->s__usuario == 'acor_carga')
        { //-- el usuario tiene perfil 'acor_carga_nov' y pertenece a una dependencia habilitada para cargar novedades --//
            $filtro->columna('descripcion')->set_solo_lectura();
            $this->s__filtro['descripcion']['condicion'] = 'es_igual_a';
            $dep = consultas::get_dep_habilitada();
            if($dep['id_dep'] == 1)
            {
                $this->s__filtro['descripcion']['valor'] = 6; //-- Gestion --//
            }
            elseif($dep['id_dep'] == 2)
            {
                $this->s__filtro['descripcion']['valor'] = 7; //-- Procuracion --//
            }
            else
            {
                $filtro->evento('filtrar')->ocultar();
                toba::notificacion()->error('El usuario no tiene el perfil de Carga de Novedades
                                        o no pertenece a ninguna de las Dependencias habilitadas');
            }
            $filtro->set_datos($this->s__filtro);
        }

	}

	function evt__fi_exp__filtrar($datos)
	{
        if($datos['n_expediente']['valor'] != '')
        {// si fue usado en el filtro
            if(strlen($datos['n_expediente']['valor']) > 14) /*** Modifique 14/12 (> 14)*/
            {
                toba::notificacion()->error('Ingresar el Nro. de Expediente sin guiones');
                $this->s__filtro = null;
                $this->s__where = '1=1';
                $bandera = 'con_guion'; //-- bandera q uso p no cargar el filtro si el expte fue cargado con guiones --
            }
        }
        if($bandera == '')
        {
            $this->s__where = $this->dep('fi_exp')->get_sql_where();
            $this->s__filtro = $datos;
        }
	}

	function evt__fi_exp__cancelar()
	{
        $this->s__filtro = null;
        $this->s__where = '1=1';
	}

//-----------------------------------------------------------------------------------
//--------------------------------- CUADRO EXPEDIENTE--------------------------------
//-----------------------------------------------------------------------------------

	function conf__cd_exp(staf_ei_cuadro $cuadro)
	{
        /** codigo del ayuda ------------------------------------------------------------------------------------------------------------------- */
        $id_objeto = $this->pantalla($id_pantalla)->get_id();
        foreach(toba::usuario()->get_perfiles_funcionales() as $valor)
        {
            $perfiles[] = "'".utf8_encode($valor)."'"; //lista los perfiles de usuarios separados por coma
        }

        $restricciones = trim(implode(toba::usuario()->get_restricciones_funcionales(),','),','); //lista las restricciones func. separados por coma

        $parametros_ayuda = array('proyecto'=>utf8_encode($id_objeto[0]),
            'objeto'=>$id_objeto[1],
            'descr_objeto'=>utf8_encode('pantalla: Areas que solucionan problemas (no valido como parametro)')
            /*,'p1_contenido'=>utf8_encode('tipo de inspector')
            ,'p1'=>utf8_encode($this->s__inspector['id_insp_tipo'])
            ,'p2_contenido'=>utf8_encode('es supervisor')
            ,'p2'=>utf8_encode($this->s__inspector['es_supervisor'])
            ,'p3_contenido'=>utf8_encode('Id. Pantalla')
            ,'p3'=>utf8_encode($id_pantalla)*/
        ,'p4_contenido'=>utf8_encode('Perfiles del usuario')
        ,'p4'=>trim(implode($perfiles,','),',')
        ,'p5_contenido'=>utf8_encode('Restricciones funcionales')
        ,'p5'=>utf8_encode($restricciones)
        );

        /*   if(in_array('obras_supervisor',toba::usuario()->get_perfiles_funcionales()) or in_array('admin',toba::usuario()->get_perfiles_funcionales()))
           {
               $parametros_ayuda['p2_contenido'] = utf8_encode('Es administrador');
               $parametros_ayuda['p2'] = utf8_encode('1');
           }*/

        $this->evento('ayuda')->vinculo()->set_parametros($parametros_ayuda);
        /** --------------------------------------------------------------------------------------------------------------------------------------- **/

        if($this->s__usuario == 'acor_carga')
        { //-- Si el perfil del usuario es 'acor_carga_nov', armo el filtro --//
            $where = filtros::armo_filtro($this->s__filtro);
            if($this->s__filtro['cobrado']['valor'])
            {
                if($this->s__filtro['cobrado']['condicion'] == 'es_igual_a'){
                    $where1 = ($this->s__filtro['cobrado']['valor'] == 'SI') ? " TODOS.cobrado = 'SI'" : " TODOS.id_nov is null OR TODOS.cobrado = 'NO'";
                }else{
                    $where1 = ($this->s__filtro['cobrado']['valor'] == 'SI') ? " TODOS.id_nov is null OR TODOS.cobrado = 'NO'" : " TODOS.cobrado = 'SI'";
                }
            }else{
                $where1 ='1=1';
            }
            if($where)
            {
                $ci="<font color=\"red\"><b>";
                $cf="</b></font>";
                $sql = "SELECT *
                        FROM
                            (Select DISTINCT ON (e.id_expediente) e.id_expediente, e.n_expediente, e.fe_alta, e.nrodoc, e.sexo, t.descripcion as desc_trib,
                                    e.apyn, e.domicilio, d.id_mov_destino, d.descripcion as desc_mov_destino, dominio, m.fecha, m.id_mov, n.cobrado, id_nov,
                                    case when p.descripcion='URGENTE' then ' URGENTE ' else 'COMUN' end as desc_prioridad, tta.nombre as estado
                            FROM
                                (select e.id_expediente, max(m.id_mov) as maximo --recorro y busco el ultimo movimiento de cada expte --
                                 from tribunal.expediente e
                                 inner join tribunal.movimientos m on m.id_expediente = e.id_expediente
                                 group by e.id_expediente order by e.id_expediente
                                ) as completo
                            INNER JOIN tribunal.expediente e on completo.id_expediente = e.id_expediente
                            INNER JOIN tribunal.actas a ON completo.id_expediente = a.id_expediente
                            INNER JOIN tribunal.prioridades p ON p.id_prioridad = e.id_prioridad
                            LEFT JOIN tribunal.movimientos m ON completo.id_expediente = m.id_expediente and completo.maximo = m.id_mov
                            LEFT JOIN tribunal.mov_destinos d ON d.id_mov_destino = m.id_mov_destino
                            LEFT JOIN tribunal.novedades n ON completo.id_expediente = n.id_expediente
                            LEFT  JOIN tribunal.tribunales t ON t.id_tribunal = e.id_tribunal
                            LEFT JOIN tribunal.acciones ta ON ta.id_expediente = e.id_expediente AND ta.id_accion = (SELECT MAX(id_accion) FROM tribunal.acciones 
                                WHERE id_expediente = e.id_expediente)
                            LEFT JOIN tribunal.tipo_accion tta ON tta.id_tipo_accion = ta.id_tipo_accion
                            WHERE $where
                               AND m.usu_confirmacion is not null AND m.fe_confirmacion is not null
                            ORDER BY e.id_expediente,id_nov DESC) TODOS
                        Where $where1";
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
                }else{
                    toba::notificacion()->error('No existen expedientes cargados para esa b&uacute;squeda');
                    $this->s__filtro = null;
                    $this->s__where = '1=1';
                    $this->s__usuario = null;
                }
                $cuadro->set_datos($rs);
            }
        }
        else
        {
        //-- Si el perfil no es Acor muestra el cuadro sin filtrar x ubicacion --//
            $ci="<font color=\"red\"><b>";
            $cf="</b></font>";
            $sql = "Select Distinct on (e.id_expediente) e.id_expediente, e.n_expediente, e.fe_alta, e.nrodoc, e.sexo,
                                           t.descripcion as desc_trib, e.apyn, e.domicilio, d.id_mov_destino,
                                           d.descripcion as desc_mov_destino, dominio, m.fecha, m.id_mov,
                                           case when p.descripcion='URGENTE' then '$ci URGENTE $cf' else 'COMUN' end as desc_prioridad, tta.nombre as estado
                        From
                            (select e.id_expediente, max(m.id_mov) as maximo --recorro y busco el ultimo movimiento de cada expte --
                             from tribunal.expediente e
                             inner join tribunal.movimientos m on m.id_expediente = e.id_expediente
                             group by e.id_expediente order by e.id_expediente
                            ) as completo
                        INNER JOIN tribunal.expediente e on completo.id_expediente = e.id_expediente
                        INNER JOIN tribunal.actas a ON completo.id_expediente = a.id_expediente
                        INNER JOIN tribunal.prioridades p ON p.id_prioridad = e.id_prioridad
                        LEFT JOIN tribunal.tribunales t ON t.id_tribunal = e.id_tribunal
                        LEFT JOIN tribunal.movimientos m ON completo.id_expediente = m.id_expediente and completo.maximo = m.id_mov
                        LEFT JOIN tribunal.mov_destinos d ON d.id_mov_destino = m.id_mov_destino
                        LEFT JOIN tribunal.acciones ta ON ta.id_expediente = e.id_expediente AND ta.id_accion = (SELECT MAX(id_accion) FROM tribunal.acciones 
                                WHERE id_expediente = e.id_expediente)
                            LEFT JOIN tribunal.tipo_accion tta ON tta.id_tipo_accion = ta.id_tipo_accion
                        Where $this->s__where Order By e.id_expediente DESC, id_mov DESC Limit 1000";
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
            }else{
                toba::notificacion()->error('No existen expedientes cargados para esa b&uacute;squeda');
                $this->s__filtro = null;
                $this->s__where = '1=1';
                $this->s__usuario = null;
            }
            $cuadro->desactivar_modo_clave_segura();
            $cuadro->set_datos($rs);
        }
	}

	function evt__cd_exp__seleccion($seleccion)
	{
        $this->rel()->cargar($seleccion);
        $this->rel()->tabla('expediente')->set_cursor(0);
        $this->set_pantalla('pant_edi_consulta');
	}

	function evt__cd_exp__novedad($seleccion)
	{
        $this->rel()->cargar($seleccion);
        $this->rel()->tabla('expediente')->set_cursor(0);
        $this->set_pantalla('pant_novedades');
	}

	function conf_evt__cd_exp__novedad(toba_evento_usuario $evento, $fila)
	{
	//-- Solo el perfil 'acor_carga_nov' puede ver el boton de Novedad --//
        if($this->s__usuario == 'acor_carga')
        {
            $this->dep('cd_exp')->evento('novedad')->mostrar();
        }
        else
        {
            $this->dep('cd_exp')->evento('novedad')->ocultar();
        }
	}
}
?>