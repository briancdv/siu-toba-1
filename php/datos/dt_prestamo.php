<?php
class dt_prestamo extends libros_datos_tabla
{
	function get_listado()
	{
		$sql = "SELECT
			t_p.id_prestamo,
			t_p.libro_id,
			t_p.persona_id,
			t_p.fecha_alta,
			t_p.plazo,
			t_p.fecha_venc
		FROM
			prestamo as t_p";
		return toba::db('libros')->consultar($sql);
	}


	function ajax__get_calcula_retraso($dts, toba_ajax_respuesta $respuesta)
	{      
		$rs = null;
		$fe1 = date($dts['fecha_venc']); 
		$diaVenc = substr($fe1,0,2); 
		$fe2 = date($dts['fecha_devolucion']); 
		$diaDev = substr($fe2,0,2); 
		$dia_retraso=($diaDev - $diaVenc);
			if($dia_retraso <=0){
				$dia_retraso=0;
			}else{
				$dia_retraso=abs($diaDev - $diaVenc);
			}  
		$respuesta->set($dia_retraso);
	}
}
?>