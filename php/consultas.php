<?php
/**
 * Created by Marina Barrios
 * Date: 24/05/16
 * Time: 10:05
 */
class consultas
{
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

}
?>