<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;
use App\Http\Controllers\ChekRequestAuth;

class SaveClearance extends Controller
{
    public function save(Request $request) {
			$header = $request->header('Authorization');
			$token = new CheckRequestAuth();
			if($token->testToken($header)) {
				return json_encode([ 'res' => $this->genFile($request)]);
			} else {
				return json_encode([ 'res' => false ]);
			}
    }

		private function genFile(Request $req) {
			$pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetTitle('CL_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf');
			$pdf->SetDisplayMode(100);
			$count = $pdf->setSourceFile(base_path().'\resources\assets\pdf\clearance_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$tpl = $pdf->importPage(1);
			$size = $pdf->getTemplateSize($tpl);
			$orn = $size['h'] > $size['w'] ? 'P' : 'L';
			$pdf->addPage($orn);
			$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
			$pdf->SetFont('helvetica', '', 12);
			$pdf->Text(157, 36, $req['current_date']);
			$pdf->SetFont('helvetica', '', 9);
			if(strlen($req['owner_names']) > 25) {
				$pdf->SetFont('helvetica', '', 5);
			} else if (strlen($req['owner_names']) > 50) {
				$pdf->SetFont('helvetica', '', 4);
			}
			$pdf->writeHTMLCell(52, 2, 131, 65, '<span>'.$req['owner_names'].'</span>', 0, 0, 0, true, 'L', true);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(35, 79, $req['pin']);
			$pdf->Text(130, 79, $req['arp_no']);
			$pdf->Text(35, 86, $req['location']);
			$pdf->Text(146, 86, 'P ' . $req['assessed_value']);
			$pdf->Text(156, 121.5, $req['payment_reason']);
			$pdf->Text(52, 127, $req['total_amount']);
			$pdf->Text(16, 132, $req['cto_no']);
			$pdf->Text(65, 132, $req['dated']);
			$pdf->Text(125, 142.5, $req['name_of_requestor']);
			$pdf->SetFont('helvetica', 'B', 12);
			$pdf->Text(92.3, 158.3, $req['s1']);
			$pdf->Text(92.3, 170.3, $req['s2']);
			$pdf->Text(92.3, 181.6, $req['s3']);
			$pdf->Text(92.3, 193.5, $req['s4']);
			$pdf->Text(92.3, 205.3, $req['s5']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(48, 229, $req['verified_by']);
			$pdf->Text(151, 229, $req['by_name1']);
			$pdf->Text(155, 233, $req['by_title1']);
			$pdf->Text(48, 241.5, $req['certification_fee']);
			$pdf->Text(48, 246, $req['or_no']);
			$pdf->Text(48, 251.5, $req['date']);
			$pdf->Text(48, 256.3, 'P ' . $req['amount']);
			$pdf->Text(152, 251.5, $req['by_name2']);
			$pdf->Text(159, 256.1, $req['by_title2']);

			$pdf->Text(48, 261.3, $req['remarks']);


			return $pdf->Output('CL_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf', 'E');
		}
}
