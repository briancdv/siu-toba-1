<?php
/**
 * Created by Marina Barrios
 * Date: 04/08/17
 */

require_once(toba_dir().'/php/3ros/todofpdf/fpdf.php');
//ei_arbol(toba::memoria()->get_parametro('nov'));

class PDF extends FPDF

{
    function Header()
    {
        $ahora = getdate();
        $this->Ln(1);
        $this->Cell(160);
        $this->SetFont('Arial','',9);
        $this->Cell(60,5,str_pad($ahora[mday],2,'0',STR_PAD_LEFT).'/'.str_pad($ahora[mon],2,'0',STR_PAD_LEFT).'/'.
            $ahora[year].'  '.str_pad($ahora[hours],2,'0',STR_PAD_LEFT).':'.str_pad($ahora[minutes],2,'0',STR_PAD_LEFT).':'.str_pad($ahora[seconds],2,'0',STR_PAD_LEFT),'',0,'');

        $this->Image('../www/img/logo_rectangular.jpg',11,8,50); //47
        $this->Rect(10,8,196,20,'d');
        $this->Ln(8);
        $this->Cell(80);
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(204,204,208);
        $this->Cell(70,7,'MUNICIPALIDAD DE LA CIUDAD DE CORRIENTES','',0,'C');
        $this->Ln(5); $this->Cell(80);
        $this->Cell(70,7,'ACOR','',0,'C');
        $this->SetFillColor(240,240,240);
        $this->Ln(5);
    }

    function Footer()
    {
    }


}	//  Fin de la creación de la Clase


//Creación del objeto de la clase heredada

$pdf = new PDF(P);
$pdf->FPDF('P','mm',Legal);  //L APAISADO   P NORMAL   //   A4
$pdf->SetMargins(10, 3 ,10); //Establecemos los márgenes izquierda, arriba y derecha
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(3);

$nov = toba::memoria()->get_parametro('nov');

$query = "SELECT n.id_nov, n.fecha_nov, n.usu_alta, n.observaciones, n.cobrado, e.n_expediente, e.apyn as caratula,
                  e.domicilio, e.nrodoc, e.fe_alta, p.apyn as procurador, d.c01leyen, n.id_expediente, d.c01leyen,
                  (Select m.fecha
                        from tribunal.movimientos m
                        where m.id_mov = (Select max(m.id_mov) From tribunal.movimientos m
				                          Inner Join tribunal.novedades n On n.id_expediente = m.id_expediente
				                          Where n.id_nov = {$nov} and m.id_mov_destino = 5) -- devuelve el ultimo id_mov de esa novedad --
		          ) as fecha_mov -- muestro la fecha en que se movió a acor el expediente --
          FROM tribunal.novedades n
          INNER JOIN tribunal.expediente e ON n.id_expediente = e.id_expediente
          INNER JOIN tribunal.dependencias_habilitadas d ON n.id_dep = d.id_dep
          LEFT JOIN tribunal.procuradores p On n.procurador = p.dni
          WHERE n.id_nov = {$nov}";

$res = toba::db()->consultar($query);

$pdf->Rect(10,29,196,20,'d'); //-- rectangulo de la cabera del expte --//
$pdf->Rect(10,50,196,40,'d'); //-- rectangulo de la novedad --//

$pdf->ln(8);
//$pdf->Cell(10);
$pdf->SetFont('Arial','',12);
$pdf->Cell(29,5,utf8_decode('Expediente N°: '),'',0,'');
$pdf->SetFont('Arial','B',12);
$exp = substr($res[0]['n_expediente'],0,4).'-'.substr($res[0]['n_expediente'],4,2).'-'.
       substr($res[0]['n_expediente'],6,2).'-'.substr($res[0]['n_expediente'],8,6);
$pdf->Cell(50,5,$exp,'',0,'');

$pdf->Cell(69);
$pdf->SetFont('Arial','',12);
$pdf->Cell(25,5,'Fecha Inicio: ','',0,'');
$pdf->SetFont('Arial','B',12);
// Acomoda fecha
if(!is_null($res[0]['fecha_mov']))
    list($aa,$mm,$dd)=explode('-',$res[0]['fecha_mov']);
else
{
    $aa = date('Y');
    $mm = date('m');
    $dd = date('d');
}
$pdf->Cell(25,5,$dd.'/'.$mm.'/'.$aa,'',0,'');

$pdf->ln(7);
$pdf->SetFont('Arial','',12);
$pdf->Cell(18,5,utf8_decode('Carátula:'),'',0,'');
$pdf->SetFont('Arial','B',12);
if($res[0]['caratula'] != '')
{
    $pdf->Cell(178,5,$res[0]['caratula'],'',0,'');
}else{
    $pdf->Cell(178,5,'Infractor no identificado','',0,'');
}

$pdf->ln(7);
$pdf->SetFont('Arial','',12);
$pdf->Cell(20,5,utf8_decode('Ubicación:'),'',0,'');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(176,5,$res[0]['c01leyen'],'',0,'');


$pdf->ln(7);
$pdf->SetFont('Arial','',12);
$pdf->Cell(32,5,utf8_decode('Fecha Novedad:'),'',0,'');
$pdf->SetFont('Arial','B',12);
// Acomoda fecha
if(!is_null($res[0]['fecha_nov']))
    list($aa1,$mm1,$dd1)=explode('-',$res[0]['fecha_nov']);
else
{
    $aa1 = date('Y');
    $mm1 = date('m');
    $dd1 = date('d');
}
$pdf->Cell(25,5,$dd1.'/'.$mm1.'/'.$aa1,'',0,'');

$pdf->Cell(15);
$pdf->SetFont('Arial','',12);
$pdf->Cell(26,5,'Cargado por:','',0,'');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(45,5,$res[0]['usu_alta'],'',0,'');

$pdf->Cell(20);
$pdf->SetFont('Arial','',12);
$pdf->Cell(18,5,'Cobrado:','',0,'');
$pdf->SetFont('Arial','B',12);
$pdf->Cell(10,5,$res[0]['cobrado'],'',0,'');

$pdf->ln(7);
$pdf->SetFont('Arial','U',12);
$pdf->Cell(20,5,utf8_decode('Descripción de la Novedad:'),'',0,'');
$pdf->ln(6);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(196,5,$res[0]['observaciones'],0,'L');

$pdf->SetXY(12,110); $pdf->Cell(100);
$pdf->SetFont('Arial','',12);
$pdf->Cell(50,5,'............................................................','',0,'');
$pdf->ln(4); $pdf->Cell(112);
$pdf->Cell(53,5,'Firma del Agente Municipal','',0,'');
$pdf->ln(6);

/*$mov=$res[0];
$dominio =$mov['dominio'];
$propietario=$mov['apyn'];
$domicilio=$mov['domicilio'];
$localidad=$mov['c07localidad'];*/

//if ($propietario == '') {  //ERROR NO MUESTRO CARATULA
    /*    $pdf->SetFillColor(181,178,178);
        $pdf->Rect(30,60,160,20,'f');*/
 /*   $pdf->Ln(10);
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(50);
    $pdf->Cell(77,20,'DEBE COMPLETAR LA DESCRIPCION DEL INFRACTOR!','',0,'R','C');
} else {

// Acomoda fecha
    if(!is_null($mov['fe_alta']))
        list($aa,$mm,$dd)=explode('-',$mov['fe_alta']);
    else
    {
        $aa = date('Y');
        $mm = date('m');
        $dd = date('d');
    }
    if ($mov['identidad_verif']=="SI") {
        $identidad="[IDENTIDAD VERIFICADA]";
    }else{
        $identidad="";
    }


    if ($nrodoc ==0) {
        $documento="";
    } else {
        $documento=$mov['c05descr'].' '.$mov['nrodoc'];
    }


    $tipo_acta=substr($mov['n_expediente'],5,2);
    $SIT= str_pad(substr($mov['n_expediente'],11),8,'0',STR_PAD_LEFT) . ' - ' .substr($propietario,0,1) . ' - ' .
        substr($mov['n_expediente'],0,4);

    $pdf->Image('../www/img/logofabian.jpg',46,10,47);
    $pdf->Rect(45,8,165,20,'d');
    $pdf->Ln(8);
    $pdf->Cell(70);
    $pdf->SetFont('Arial','B',12);
    $pdf->SetFillColor(204,204,208);
    $pdf->Cell(70,7,'MUNICIPALIDAD DE LA CIUDAD DE CORRIENTES','',0,'C');
    $pdf->SetFillColor(240,240,240);
    $pdf->Ln(5);

    $pdf->SetFont('Arial','B',12);
    $pdf->SetFillColor(204,204,208);
    $pdf->Cell(70);
    $pdf->Cell(70,7,'Tribunal Administrativo de Faltas','',0,'C');
    $pdf->SetFillColor(240,240,240);
//$pdf->Ln(3); //--- linea que salta renglones -----
    $pdf->ln(13);
    $pdf->Cell(1);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(165,5,$mov['desc_trib'],'',0,'C');
    $pdf->Ln(6);
    $pdf->Cell(1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(165,5,'Juez/a: '.$mov['juez'],'',0,'C');
    $pdf->Ln(6);
    $pdf->Cell(1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(165,5,'Secretario/a: '.$mov['secretario'],'',0,'C');

    $pdf->ln(10);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(20,5,$identidad,'',0,'');
    $pdf->ln(2);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,15,'Infractor: ','',0,'');
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(40,15,$propietario,'',0,'');
    $pdf->Ln(8);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,15,'Documento: ','',0,'');
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(40,15,$documento,'',0,'');
    $pdf->Ln(8);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,15,'Dominio: ','',0,'');
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(40,15,$mov['dominio'],'',0,'');
    $pdf->Ln(8);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,15,'Domicilio: ','',0,'');
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(40,15,$domicilio,'',0,'');
    $pdf->Ln(8);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,15,'Localidad: ','',0,'');
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(50,15,$localidad,'',0,'');

    if ($mov['id_prioridad']==2) {
        $pdf->SetFillColor(50,50,50);
        $pdf->Rect(160,97,50,8,'FD');
        $pdf->Ln(12);
        $pdf->Cell(118);
        $pdf->SetFont('Arial','B',18);
        $pdf->SetTextColor(255,255,255);
//$pdf->SetFillColor(204,204,208);
        $pdf->Cell(50,7,'U R G E N T E','',0,'');
        $pdf->SetTextColor(0,0,0);
    }else {
        $pdf->Ln(12);
    }

    $pdf->SetFillColor(255,255,255);
    $pdf->Rect(45,105,165,48,'FD');

    $pdf->Ln(13);
    $pdf->Cell(30);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(32,5,utf8_decode('EXPEDIENTE N°: '),'',0,'L');
    $pdf->SetFont('Arial','B',18);
    $pdf->Cell(10,5,$mov['n_expediente'],'',0,'L');
    $pdf->Ln(10);
    $pdf->Cell(30);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,$mov['descripcion'],'',0,'L');

    if ($mov['id_tramite'] ==1) {
        $pdf->Ln(10);
        $pdf->Cell(30);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(25,5,utf8_decode('N° Secuestro: '),'',0,'L');
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(10,5,$mov['secuestro'],'',0,'L');
        $pdf->Cell(10);
        $pdf->Cell(10,5,'[SECUESTRO]','',0,'L');
    } elseif ($mov['id_tramite'] >=1) {
        $pdf->Ln(10);
        $pdf->Cell(1);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(165,5,'['.$mov['tramite'].']','',0,'C');
    }


    $pdf->Ln(10);
    $pdf->Cell(30);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(25,5,'Fecha del Sorteo: ','',0,'L');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(30,5,$dd.'/'.$mm.'/'.$aa,'',0,'C');

    if (($tipo_acta != '00') && ($tipo_acta != '21'))   {   //ACTAS QUE SE CARGAN AL SIIT
        $pdf->Ln(17);
        if ($mov['id_tramite'] == '') {$pdf->Ln(10);}
        $pdf->SetFillColor(255,255,255);
        $pdf->Rect(45,155,165,10,'FD');
        $pdf->Cell(1);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(165,5,utf8_decode('SIIT - Expediente N°: '.$SIT),'',0,'C');
    }


} */ //cierro $propietario == ''
$pdf->Output();
?>
