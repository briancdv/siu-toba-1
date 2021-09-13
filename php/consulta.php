<?php

/*
 * Permite avisar al/a los jefes del area de Soporte, los trabajos q estan a punto de expirar
 */

// Conectando y seleccionado la base de datos
$dbconn = pg_connect("host=172.25.50.200 dbname=bdSueldos user=postgres password=09dgc06mcc")
or die('No se ha podido conectar: ' . pg_last_error());

// Realizando una consulta SQL
$query = "Select r.id_req, r.fecha_req, r.fecha_asignacion, r.limite_tiempo, e.descripcion as desc_estado,
                        t.descripcion as tipo_tarea, dp.c01leyen, p.descripcion as tipo_problema, r.id_estado,
                        eq.descripcion as desc_eq, nombre_tec.apyn				
                        From soporte.requerimientos r
                        Inner Join soporte.estados e On r.id_estado = e.id_estado
                        Left Join soporte.problemas p On r.id_problema = p.id_problema
                        Left Join soporte.tipo_tarea t On r.id_tarea= t.id_tarea
                        Left Join public.scm001 dp On r.c01depresu= dp.c01depresu
                        Left Join soporte.equipos eq On r.id_equipo = eq.id_equipo			
			Inner Join
				(Select tec.id_req, string_agg(t.apyn, ', ') apyn
					From soporte.tec_req tec
					Inner Join soporte.tecnicos t On tec.id_tecnico = t.id_tecnico 
					GROUP BY tec.id_req) as nombre_tec
			On r.id_req = nombre_tec.id_req
			Where r.id_estado != 4 and r.limite_tiempo >= 3
			Order by r.id_req ASC
                ";
$rs = pg_query($query) or die('La consulta fallo: ' . pg_last_error());

if(count($rs) > 0)
{
//-- pg_fetch_all: Obtiene todas las filas del resultado como una matriz --//
	foreach(pg_fetch_all($rs) as $i=>$fila) 
	{
		if($fila['fecha_asignacion'] != null)
        {
			$nueva_fecha = suma_dia_semana($fila['fecha_asignacion'],$fila['limite_tiempo']);

			//-- calculo el tiempo restante para ese trabajo --// 
            //-- Resto la fecha $nueva_fecha con la fecha actual, p obtener el tiempo q resta p finalizar el trabajo --//
            $fecha_actual = date("Y-m-j");
            $dias = (strtotime($nueva_fecha)-strtotime($fecha_actual))/86400; //-- dias q resta p finalizar --//
			
            if($dias == 1)
			{
					$mensaje .= '<strong>Fecha de Alta: </strong>'.date("d/m/Y - H:i",strtotime($fila['fecha_req'])).
						    '<br> <strong>Tipo de Tarea: </strong>'.$fila['tipo_tarea'].
                        '<br> <strong>Tipo de Problema: </strong>'.$fila['tipo_problema'].
                        '<br> <strong>Fecha de Asignación: </strong>'.date("d/m/Y",strtotime($fila['fecha_asignacion'])).
			'<br> <strong>Técnicos Asignados: </strong>'.$fila['apyn'].
                        '<br> <strong>Tiempo Límite: </strong>'.$fila['limite_tiempo'].' dias '.
                        '<br> <strong>Dependencia: </strong>'.$fila['c01leyen'].
						'<br> <strong>Estado: </strong>'.$fila['desc_estado'].
						'<br> <strong>Tiempo Restante: </strong>'.$dias.' dia <br><br>'.
						'<strong><font color="red">---------------------------------------------------------------------------</font></strong> <br><br>';
            }
		} 
	}
	if(isset($mensaje))
	{
		$para      = "marinapaolab@gmail.com";
		$titulo    = 'Trabajos que van a expirar';
		$cabeceras = "MIME-Version: 1.0\r\n". 
					 "Content-type: text/html; charset=iso-8859-1\r\n". 
					 "From: Municipalidad de la Ciudad de Corrientes" . "\r\n" .
					 'X-Mailer: PHP/' .phpversion();

		mail($para, $titulo, $mensaje, $cabeceras);
	}
}

// Libero el conjunto de resultados
pg_free_result($rs);

// Cierro la conexión
pg_close($dbconn);

function suma_dia_semana($fecha,$dias)
{ 
	$datestart= strtotime($fecha);
	$diasemana = date('N',$datestart); // devuelve 4 que es = a Jueves x ej. //
	$totaldias = $diasemana + $dias;
	$findesemana = intval($totaldias/5)*2; //devuelve 2(fin de semana) o 0(entre semana) - al viernes lo toma como fin de semana //
	$diasabado = $totaldias % 5 ; 
	if ($diasabado==6) $findesemana++;
	if ($diasabado==0) $findesemana = $findesemana - 2; // resuelve lo del viernes, hace que lo tome como entre semana //
	$total = (($dias+$findesemana) * 86400) + $datestart ; 
	$twstart = date('Y-m-d', $total);
	return $twstart;
}
?>