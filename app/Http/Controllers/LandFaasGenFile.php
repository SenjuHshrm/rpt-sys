<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;

class LandFaasGenFile extends Controller {
	
    public function genFile(Request $request) {
			$pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetDisplayMode(100);
			$count = $pdf->setSourceFile(base_path().'\resources\assets\pdf\land_faas_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$pdf->SetFont('helvetica', '', 10);
			for($page = 1; $page <= $count; $page++) {
				if($page == 1) {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->Text(145, 32, "Sample Text");
				} else {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->Text(18, 23, "Sample Text");
				}
			}
			$res = 'data:pdf;base64,' . $pdf->Output('example_002.pdf', 'E');
			return view('samplepdf')->with(['data' => $res]);
		}
}
