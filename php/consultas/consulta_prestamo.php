<?php
class consulta_prestamo{
  function  get_devolucion(){ 
      $sql="SELECT devolucion 
      FROM
      curlib.prestamo;";
      toba::db()->consultar($sql);

    }
}

?>