<?php
/**
 * Created by Marina Barrios
 */
require_once(toba_dir().'/php/3ros/todofpdf/fpdf.php');
/*
$filtro = json_decode(toba::memoria()->get_parametro('check'));//--decodifico lo q traigo del filtro---

foreach($filtro as $f => $fila)
{
    $filtro[$f] = $fila->id_expediente;
}

$filtro1= implode(",", $filtro); //---- armo una lista separada x comas ---
ei_arbol($filtro1);


//$tribunal = toba::memoria()->get_dato('tribunal'); ei_arbol($tribunal);
*/
class PDF extends FPDF
{
    function Header()
    {
        $this->Ln(10);
        $this->Image('../www/img/logo_rectangular.jpg',12,10,50);
        $this->Rect(10,10,335,20,'d');

        //$pdf->Write(5,utf8_decode($texto1));  \xE9=é  ,  \xED=í  ,  \xFA=ú  ,  \xF1=ñ , \xF3=ó , \n=salto de carro
        $ubicacion = toba::memoria()->get_dato('ubicacion');
        $this->Ln(4);
        $this->Cell(80);
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(204,204,208);
        $this->Cell(150,7,'MUNICIPALIDAD DE LA CIUDAD DE CORRIENTES','',0,'C');
        $this->SetFillColor(240,240,240);
        $this->Ln(5);
        $this->SetFont('Arial','B',12);
        $this->SetFillColor(204,204,208);
        $this->Cell(80);
        $this->Cell(150,7,"Ubicaci\xF3n Actual: ".$ubicacion,'',0,'C');
        $this->Ln(5);
        $this->SetFont('Arial','B',12);
        $this->Cell(150);
        $this->Cell(10,7,"Fecha de Env\xEDo: ".date("d/m/Y"),'',0,'C');
        $this->SetFillColor(240,240,240);
        $this->Ln(15);
        $this->SetFont('Arial','B',10);
        $this->Cell(50,5,"Expediente                Prioridad    Apellido y Nombre                                                                                     DNI/CUIT       Domicilio                                                                    Dominio       Destino",'',0,'');
        $this->Ln(7);
    }

// Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(150,10,'Page '.$this->PageNo().'/{nb}','',0,'R');

        $ahora = getdate();
        $usuario=toba::usuario()->get_id();
        $this->Cell(180,10,str_pad($ahora[mday],2,'0',STR_PAD_LEFT).'/'.str_pad($ahora[mon],2,'0',STR_PAD_LEFT).'/'.$ahora[year].' - '.$ahora[hours].':'.$ahora[minutes].':'.$ahora[seconds] ,'',0,'R');


    }
}	//  Fin de la creación de la Clase


//Creación del objeto de la clase heredada

$pdf = new PDF(P);
$pdf->FPDF('L','mm',Legal);  //L APAISADO   P NORMAL   //   A4
$pdf->SetMargins(10, -1 ,10,40); //Establecemos los márgenes izquierda, arriba y derecha
$pdf->AliasNbPages();
//$pdf->AddPage('L',A4);
$pdf->AddPage('L',Legal);
$pdf->SetAutoPageBreak('true',15); //margen 1,5 CM

//$list_exp = toba::memoria()->get_dato('imprime_expediente');

$filtro = json_decode(toba::memoria()->get_parametro('check'));//--decodifico lo q traigo del filtro---

foreach($filtro as $f => $fila)
{
    $filtro[$f] = $fila->id_expediente;

}

$filtro1= implode(",", $filtro); //---- armo una lista separada x comas ---

$sql = "Select Distinct on (e.id_expediente) e.n_expediente, e.apyn, e.domicilio, a.dominio, p.descripcion
        as desc_prioridad, d.descripcion as destino,
        (substring(e.n_expediente,1,4) ||'-'|| substring(e.n_expediente,5,2) ||'-'|| substring(e.n_expediente,7,2)
                                       ||'-'|| substring(e.n_expediente,9,6)) as n_expediente,
        (select case when char_length(e.nrodoc::text) = 8
         then substring(e.nrodoc::text,1,2) ||'.'|| substring(e.nrodoc::text,3,3) ||'.'|| substring(e.nrodoc::text,6,3)
        when char_length(e.nrodoc::text) > 8
         then substring(e.nrodoc::text,1,2) ||'-'|| substring(e.nrodoc::text,3,8) ||'-'|| substring(e.nrodoc::text,11,1)
        when char_length(e.nrodoc::text) = 7
         then substring(e.nrodoc::text,1,1) ||'.'|| substring(e.nrodoc::text,2,3) ||'.'|| substring(e.nrodoc::text,5,3)
        end) as nrodoc
        From tribunal.expediente e
        Inner Join tribunal.movimientos m On e.id_expediente = m.id_expediente
        Inner Join tribunal.actas a On e.id_expediente = a.id_expediente
        Inner Join tribunal.prioridades p On e.id_prioridad = p.id_prioridad
        Inner Join tribunal.mov_destinos d On m.id_mov_destino = d.id_mov_destino
        Where e.id_expediente IN (".$filtro1.")
        Order By e.id_expediente DESC, m.id_mov DESC";     /** Modifique 14/12 */

$rs = toba::db()->consultar($sql);

$pdf->SetFillColor(180,180,180);
$pdf->SetTextColor(0,0,0);

foreach($rs as $i => $fila)
{
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(33,4,$fila['n_expediente'],'',0,'');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(1);
    $pdf->Cell(14,4,$fila['desc_prioridad'],'',0,'');
    $pdf->Cell(5);
    $pdf->Cell(110,4,$fila['apyn'],'',0,'');
    $pdf->Cell(27,4,$fila['nrodoc'],'',0,'C');
    $pdf->Cell(82,4,$fila['domicilio'],'',0,'');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(20,4,$fila['dominio'],'',0,'C');
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(10,4,$fila['destino'],'',0,'');
    $pdf->Ln(1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,5,'___________________________________________________________________________________________________________________________________________________________________________','',0,'');
    $pdf->Ln(7);
}

$pdf->Output();
?>
