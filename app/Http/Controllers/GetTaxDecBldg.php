<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;
use App\Http\Controllers\ChekRequestAuth;

class GetTaxDecBldg extends Controller
{
    public function getFile(Request $req) {
      $header = $req->header('Authorization');
      $token = new CheckRequestAuth();
      if($token->testToken($header)) {
        return json_encode([ 'res' => $this->makeFile($req) ]);
      } else {
        return json_encode([ 'res' => false ]);
      }
    }

    private function addToLog($username, $id) {
      $q = DB::select("CALL login_web('".$username."')");
      DB::select("CALL add_building_faas_log('PRINT BUILDING TAX DECLARATION', ".$q[0]->user_id.", ".$id.")");
    }

    private function makeFile(Request $req) {
      $pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->SetTitle('TD_' . $req['pin'] . '_' . $req['diag_date_printed'] . '.pdf');
			$pdf->SetDisplayMode(100);
			$count = $pdf->setSourceFile(base_path().'\resources\assets\pdf\tax_declaration_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$tpl = $pdf->importPage(1);
			$size = $pdf->getTemplateSize($tpl);
			$orn = $size['h'] > $size['w'] ? 'P' : 'L';
			$pdf->addPage($orn);
			$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
			$pdf->SetFont('helvetica', '', 8);
			// $pdf->Text(27, 13, $req['reference_number']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(30, 30, $req['td_no']);
			$pdf->Text(142, 30, $req['pin']);
			if(strlen($req['owner_names']) > 84 && strlen($req['owner_names']) < 199) {
				$pdf->SetFont('helvetica', '', 5);
			} else if(strlen($req['owner_names']) > 199) {
				$pdf->SetFont('helvetica', '', 4);
			}
			$pdf->writeHTMLCell(105, 9, 28, 35, '<span>'.$req['owner_names'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(55, 9, 146, 35, '<span>'.$req['owner_tins'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(72, 6, 136, 49, '<span>'.$req['owner_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->SetFont('helvetica', '', 10);
			if(strlen($req['admin_names']) > 84 && strlen($req['admin_names']) < 199) {
				$pdf->SetFont('helvetica', '', 5);
			} else if(strlen($req['admin_names']) > 199) {
				$pdf->SetFont('helvetica', '', 4);
			}
			$pdf->writeHTMLCell(118, 6, 14, 59, '<span>'.$req['admin_names'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(55, 6, 146, 59, '<span>'.$req['admin_tins'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->writeHTMLCell(72, 6, 136, 69, '<span>'.$req['admin_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(30, 45, $req['owner_addresses']);
			$pdf->Text(30, 69, $req['admin_addresses']);
			$pdf->writeHTMLCell(43, 2, 49, 80, '<span>'.$req['street_no'].'</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(50, 2, 95, 80, '<span>'.$req['brgy_district'].'</span>', 1, 0, 0, true, 'C', true);
			$pdf->Text(48, 96, $req['oct_tct_no']);
			$pdf->Text(128, 96, $req['survey_no']);
			$pdf->Text(27, 89, $req['condo_cert']);
			$pdf->Text(128, 101, $req['lot_no']);
			$pdf->Text(27, 106, $req['dated']);
			$pdf->Text(128, 106, $req['block_no']);
			$pdf->Text(40, 115, $req['north']);
			$pdf->Text(122, 115, $req['south']);
			$pdf->Text(40, 120, $req['east']);
			$pdf->Text(121, 120, $req['west']);
			$pdf->SetFont('helvetica', 'B', 12);
			$pdf->Text(16, 139, $req['s1']);
			$pdf->Text(59, 139, $req['s2']);
			$pdf->Text(132, 139, $req['s3']);
			$pdf->Text(16, 155, $req['s4']);
			$pdf->Text(32, 210, $req['tax']);
			$pdf->Text(60, 210, $req['exp']);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->Text(82, 146, $req['no_of_storey']);
			$pdf->Text(82, 151, $req['desc_bldg']);
			$pdf->Text(155, 146, $req['desc_mchn']);
			$pdf->Text(26, 162, $req['others_specify']);
			$pdf->SetFont('helvetica', '', 9);
			$pdf->writeHTMLCell(26, 2, 13, 175, '<span>'.$req['class']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(21, 2, 44, 175, '<span>'.$req['area']. ' sqm</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(27, 2, 75, 175, '<span>'.$req['market_val']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(25, 2, 106, 175, '<span>'.$req['actual_use']. '</span>', 1, 0, 0, true, 'L', true);
			$pdf->writeHTMLCell(26, 2, 133, 175, '<span>'.$req['assess_level']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(29, 2, 174, 175, '<span>'.$req['assessed_val']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(27, 2, 75, 191, '<span>'.$req['total_market_val']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(29, 2, 174, 191, '<span>'.$req['total_assessed_val']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->SetFont('helvetica', '', 10);
			$pdf->writeHTMLCell(190, 2, 13, 200, '<span>'.$req['total_assessed_value_in_words'] . ' Only</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(24, 2, 135, 213, '<span>'.$req['pa_effectivity_assess_quarter'] . '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(34, 2, 168, 213, '<span>'.$req['pa_effectivity_assess_year'] . '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(60.5, 2, 42, 224, '<span>'.$req['approved_by1']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(60.5, 2, 104, 224, '<span>'.$req['approved_by2']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->writeHTMLCell(38, 2, 166, 224, '<span>'.$req['approved_by_date']. '</span>', 0, 0, 0, true, 'C', true);
			$pdf->SetFont('times', 'I', 9);
			$pdf->Text(63, 228, $req['approver_title1']);
			$pdf->Text(125, 228, $req['approver_title2']);
			$pdf->SetFont('helvetica', '', 9);
			$pdf->Text(55, 238, $req['previous_td_no']);
			$pdf->Text(95, 238, $req['previous_owner']);
			$pdf->Text(178, 238, $req['previous_assessed_value']);
			$pdf->SetFont('helvetica', '', 10);
			// $pdf->Text(37, 247, $req['memoranda']);
			$pdf->writeHTMLCell(190, 18, 13, 247, '<p style="text-indent: 68px">'.$req['memoranda'].'</p>', 0, 0, 0, true, 'J', true);
			$pdf->SetFont('helvetica', '', 6);
			$pdf->StartTransform();
			$pdf->Rotate(90);
			$pdf->Text(200, 47, 'Date Printed: ' . $req['diag_date_printed']);
			$pdf->Text(200, 50, 'Printed By: ' . $req['diag_printed_by']);
			$pdf->StopTransform();
			$this->addToLog($req['username'], $req['id']);
			return $pdf->Output('TD_' . $req['pin'] . '_' . $req['diag_date_printed'] . '.pdf', 'E');
    }
}
