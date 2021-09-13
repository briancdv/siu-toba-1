<?php
/**
 * Created by Marina Barrios
 */
class consultas
{
    function get_combo_editable1Desc($filtro = null)
    {
        if (isset($filtro))
        {
            $sql = "SELECT nombre as clave, nombre as valor
                     FROM tribunal.apyn_standarizados";
            $rs = toba::db()->consultar($sql);
        }

        if(isset($filtro))
        {
            $i = count($rs);
            $rs[$i]['clave'] = $filtro;
            $rs[$i]['valor'] = $filtro;
        }
        return $rs;

     }

    #------

  /*  function get_pago($acta)
    {

      try {
          $hostname = "172.25.27.57";
          $port = 1433;
          $dbname = "staCMP";
          $username = "ANSV";
          $pw = "ANSV";
          $dbh = new PDO ("dblib:host=$hostname:$port;dbname=$dbname","$username","$pw");

          //procedemiento almacenado
          $parametro = $acta; //'1900614000079';
          // $sth = $dbh->prepare("{call consultaANSV(?)}");
          // $sth = $dbh->prepare("{exec consultaANSV(?)}");
          $sth = $dbh->prepare("exec consultaANSV ?");
          $sth->bindParam(1, $parametro);
          $sth->execute();

          while($result = $sth->fetch(PDO::FETCH_ASSOC)) {
              //var_dump($result);
              return($result);
          }


      } catch (PDOException $e) {
          echo "Failed to get DB handle: " . $e->getMessage() . "\n";
          exit;
      }
    }*/

    static function get_secretaria() //secretaria del usuario logueado
    {
        $dni = toba::usuario()->get_parametro(a); //obtengo dni
    //--- obtengo la secretaria (estado null por si tiene mas de una entrada en la tabla) ---//
        $sql = "select n_heredera from public.per_neike where documento = $dni and estado is null"; //"201704031540000"
        $rs = toba::db()->consultar($sql);//ei_arbol($rs);

    /* Obtengo el c01depresu que comienza con 2017 de esa n_heredera, ya q es la q tengo en mi tabla de tribunales
     y así poder comparar y determinar a q tribunal pertenece ese empleado */
        $sql2 = "select c01depresu from public.scm001_her
                 Where n_heredera = '".$rs[0]['n_heredera']."' and c01depresu ilike '20170403%'";
        $rs2 = toba::db()->consultar($sql2); //ei_arbol($sql2);
        if(count($rs2)>0)
        {
            $dep = substr($rs2[0]['c01depresu'],0,11).'0000';//ei_arbol($dep);
            $sql1 = "Select id_tribunal, descripcion as tribunal
                                From tribunal.tribunales
                                Where c01depresu = '".$dep."'";
            $rs1 = toba::db()->consultar($sql1);

            if(count($rs1) > 0)
            {
                $id_trib = $rs1[0]['id_tribunal'];
                $trib = $rs1[0]['tribunal'];
                $rs[0]['id_tribunal'] = $id_trib;
                $rs[0]['tribunal'] = $trib;

            }
            return $rs[0];
        }else{
            throw new toba_error('Usuario inv&aacute;lido');

        }
    }

    static function get_tribunales($id)
    {
        $sql = "select descripcion
					from tribunal.tribunales
					where id_tribunal = '$id'";

        $res = toba::db()->consultar($sql);
        return $res[0]['descripcion'];
    }

    static function get_tramite($id)
    {
        $rs = toba::db()->consultar("Select descripcion From tribunal.tramites
									  Where id_tramite = '$id'");
        return $rs[0]['descripcion'];
    }

    static function get_pf()
    {
        $usuario = false;
        $perfil = toba::usuario()->get_perfiles_funcionales();
        if(in_array('super',$perfil))
        {
            $usuario = 'superusuario';
        }
        elseif(in_array('supervisor',$perfil))
        {
            $usuario = 'supervisor';
        }
        elseif(in_array('carga',$perfil))
        {
            $usuario = 'carga';
        }
        elseif(in_array('archivo',$perfil))
        {
            $usuario = 'archivo';
        }
        elseif(in_array('apremio',$perfil))
        {
            $usuario = 'apremio';
        }
        elseif(in_array('carga_juzgado',$perfil))
        {
            $usuario = 'carga_juzgado';
        }
        elseif(in_array('acor_carga_nov',$perfil))
        {
            $usuario = 'acor_carga';
        }
        elseif(in_array('acor_mesa',$perfil))
        {
            $usuario = 'acor_mesa';
        }elseif(in_array('admin',$perfil))
        {
            $usuario = 'admin';
        }
        return $usuario;
    }

    static function get_dep_habilitada()
    {
        //-- obtengo dni el usuario --//
        $dni = toba::usuario()->get_parametro(a);

        if($dni)
        {
            //--- obtengo la secretaria (estado null por si tiene mas de una entrada en la tabla) ---//
            $sql = "select n_heredera from public.per_neike where documento = $dni and estado is null";
            $rs = toba::db()->consultar($sql);
           // '201704030530410'; //-- Mesa de E y S --
           // '201704030534000'; //-- Procuracion --
	       // '201704030531200'; //-- Gestion --
            /** Obtengo el c01depresu que comienza con 2017 de esa n_heredera, ya q es la q tengo en mi tabla 'dependencias_habilitadas'
            y así poder comparar y determinar a q dependencia pertenece ese empleado **/
            $sql2 = "select c01depresu from public.scm001_her
                     Where n_heredera = '".$rs[0]['n_heredera']."' and c01depresu ilike '20170403%'";
            $rs2 = toba::db()->consultar($sql2);
           // $rs2[0]['c01depresu'] = '201704030530410';
         /** Busco la posicion 12 en n_heredera q es donde empieza a cambiar la numeracion de una dep a la otra por si el usuario
         se encuentra en una subdependencia, corto ahí la n_heredera y completo con ceros, ya q así está en dep_habilitadas --------*/
            $valor = substr($rs2[0]['c01depresu'],0,12); //-- me devuelve hasta la posicion 12 de la n_heredera --//
            if($valor == '201704030531')
            { //-- se trata de de la dir. de gestion --//
                $rs[0]['n_heredera'] = str_pad(substr($rs2[0]['c01depresu'],0,13),15,'0',STR_PAD_RIGHT);
            }elseif($valor == '201704030534')
            { //-- se trata de de la dir. de procuracion --//
                $rs[0]['n_heredera'] = str_pad(substr($rs2[0]['c01depresu'],0,12),15,'0',STR_PAD_RIGHT);
            }elseif($valor == '201704030530')
            { //-- se trata de del dpto. mesa de e y s, no tiene subdependencias --//
                $rs[0]['n_heredera'] = $rs2[0]['c01depresu'];
            }
            if(((substr($rs2[0]['c01depresu'],0,11)) == '20170403053') AND count($rs2) > 0)
            {
                $sql1 = "Select id_dep, c01leyen, estado
                                From tribunal.dependencias_habilitadas
                                Where c01depresu = '".$rs2[0]['c01depresu']."'";
                $rs1 = toba::db()->consultar($sql1); //-- el resultado es un único registro --//

                if(count($rs1) > 0)
                {
                    if($rs1[0]['estado'] == 'Activo')
                    {
                        if($rs1[0]['id_dep'] == 1)
                        {
                            if(toba::perfil_de_datos()->posee_dimension('gestion','staf') == 1)
                            {
                                return $rs1[0];
                            }
                            elseif(toba::perfil_de_datos()->posee_dimension('procuracion','staf') == 1 OR
                                   toba::perfil_de_datos()->posee_dimension('mesa','staf') == 1)
                            { 
                                throw new toba_error('El Perfil de Datos asignado es incorrecto. Comun&iacute;quese con el administrador. Gracias.');
                            }
                        }
                       elseif($rs1[0]['id_dep'] == 2){
                            if(toba::perfil_de_datos()->posee_dimension('gestion','staf') == 1 OR
                               toba::perfil_de_datos()->posee_dimension('mesa','staf') == 1)
                            {
                                throw new toba_error('El Perfil de Datos asignado es incorrecto. Comun&iacute;quese con el administrador. Gracias.');
                            }
                            elseif(toba::perfil_de_datos()->posee_dimension('procuracion','staf') == 1)
                            {
                                return $rs1[0];
                            }
                        }
                        elseif($rs1[0]['id_dep'] == 3){
                            if(toba::perfil_de_datos()->posee_dimension('gestion','staf') == 1 OR
                               toba::perfil_de_datos()->posee_dimension('procuracion','staf') == 1)
                            {
                                throw new toba_error('El Perfil de Datos asignado es incorrecto. Comun&iacute;quese con el administrador. Gracias.');
                            }
                            elseif(toba::perfil_de_datos()->posee_dimension('mesa','staf') == 1)
                            {
                                return $rs1[0];
                            }
                        }
                    }else{
                        throw new toba_error('La Dependencia fu&eacute; dada de Baja');
                    }

                }else{ //-- no pertenece a ninguna de las dep_habilitadas --//
                    throw new toba_error('Usuario inv&aacute;lido');
                }
            }else{
                throw new toba_error('Usuario inv&aacute;lido');
            }
        }else{
            throw new toba_error('Solicitar al administrador agregue el par&aacute;metro DNI para poder acceder');
        }
    }

    static function get_destino($id = false)
    {
        /**
         * En el sistema existen 3 perfiles de datos -> 'mesa', 'gestion', 'procuracion'.
         * si el usuario tiene alguno de esos perfiles asociado solo muestro el destino permitido para ese perfil
         * mesa: 6:gestion de deudas y 3: apremio
         * gestion: 7:procuracion y 5:mesa de eys
         * procuracion: 5:mesa de eys
         */
        if($id)
        {
            $sql =  "SELECT descripcion FROM tribunal.mov_destinos Where id_mov_destino = $id";
        }else{
        //$perfil_datos = toba::manejador_sesiones()->get_perfil_datos();
            $sql =  "SELECT id_mov_destino, descripcion FROM tribunal.mov_destinos";

            if(toba::perfil_de_datos()->posee_dimension('mesa','staf') == 1)
            {
                $sql = toba::perfil_de_datos('mesa')->filtrar($sql,'staf');
            }
            elseif(toba::perfil_de_datos()->posee_dimension('gestion','staf') == 1)
            {
                $sql = toba::perfil_de_datos('gestion')->filtrar($sql,'staf');
            }
            elseif(toba::perfil_de_datos()->posee_dimension('procuracion','staf') == 1)
            {
                $sql = toba::perfil_de_datos('procuracion')->filtrar($sql,'staf');
            }
        }
        $datos = toba::db()->consultar($sql);
        return $datos;
    }

    function get_ubicacion()
    { //-- obtengo la dependencia del usuario --//
        $usuario = consultas::get_pf();
        $dep = consultas::get_dep_habilitada();
        if($usuario == 'acor_mesa')
        {
            if($dep['id_dep'] == 3)
            { //-- 3:Mesa de EyS --//
                $datos['motivo'] = 'and m1.id_motivo = 10'; //-- 10: Salida de Mesa de EyS --//
                $datos['ubicacion'] = 'DPTO. MESA DE EyS';
                $datos['ubi'] = 5;
                $datos['id_motivo'] = '10';
            }
        }elseif($usuario == 'acor_carga')
        {
            if($dep['id_dep'] == 1)
            { //-- 1:Gestion de deudas --//
                $datos['motivo'] = 'and m1.id_motivo = 11'; //-- 10: Salida de Gestion de deudas --//
                $datos['ubicacion'] = 'DIR. GESTION DE DEUDAS';
                $datos['ubi'] = 6;
                $datos['id_motivo'] = '11';
            }
            elseif($dep['id_dep'] == 2)
            { //-- 2:Procuracion y legales --//
                $datos['motivo'] = 'and m1.id_motivo = 12'; //-- 10: Salida de Procuracion y legales --//
                $datos['ubicacion'] = 'DIR. PROCURACION Y LEGALES';
                $datos['ubi'] = 7;
                $datos['id_motivo'] = '12';
            }
        }
        return $datos;
    }

    static function get_situacion_actual($id_expe){

        $query = "SELECT tta.nombre 
        FROM tribunal.acciones ta
        INNER JOIN tribunal.tipo_accion tta ON tta.id_tipo_accion = ta.id_tipo_accion
        WHERE ta.id_expediente = $id_expe";

        $situacion = toba::db()->consultar($query);

        return $situacion;
    }

}
?>