<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;

class GenLandFaasCtrl extends Controller
{
		public function genFile(Request $req) {
			$pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->setTitle('LandFaas_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf');
			$pdf->SetDisplayMode(100);
			$count = $pdf->SetSourceFile(base_path().'\resources\assets\pdf\land_faas_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			for($page = 1; $page <= $count; $page++) {
				// Page 1
				if($page == 1) {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->Text(145, 32, $req['transaction_code']);
					$pdf->Text(28, 45.9, $req['arp_no']);
					$pdf->Text(116, 45.9, $req['pin']);
					$pdf->Text(47, 53.2, $req['oct_tct_no']);
					$pdf->Text(25, 58, $req['oct_tct_dated']);
					$pdf->Text(126, 50.9, $req['survey_no']);
					$pdf->Text(121, 55.5, $req['lot_no']);
					$pdf->Text(114, 60, $req['block']);
					if(strlen($req['owner_names']) > 123) {
						$pdf->SetFont('helvetica', '', 5);
					} else if(strlen($req['owner_names']) < 123 && strlen($req['owner_names']) > 280) {
						$pdf->SetFont('helvetica', '', 4);
					}
					$pdf->writeHTMLCell(93, 11, 14, 69, '<span>'.$req['owner_names'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(93, 9, 14, 84, '<span>'.$req['owner_tins'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(93, 9, 109, 84, '<span>'.$req['owner_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->SetFont('helvetica', '', 9);
					if(strlen($req['admin_names']) > 123) {
						$pdf->SetFont('helvetica', '', 5);
					} else if(strlen($req['admin_names']) < 123 && strlen($req['admin_names']) > 280) {
						$pdf->SetFont('helvetica', '', 4);
					}
					$pdf->writeHTMLCell(68, 14, 39, 95, '<span>'.$req['admin_names'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(85, 12, 22, 110, '<span>'.$req['admin_tins'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(93, 8, 109, 114, '<span>'.$req['admin_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->writeHTMLCell(93, 11, 109, 69, '<span>'.$req['owner_addresses'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(93, 11, 109, 99, '<span>'.$req['admin_addresses'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->Text(31, 135, $req['street_no']);
					$pdf->Text(130, 135, $req['barangay_district']);
					$pdf->Text(34, 144, $req['municipality']);
					$pdf->Text(131, 144, $req['province_city']);
					$pdf->writeHTMLCell(83, 6, 25, 159.5, '<span>'.$req['north'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(83, 6, 25, 166, '<span>'.$req['east'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(83, 6, 25, 172.8, '<span>'.$req['south'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(83, 6, 25, 180, '<span>'.$req['west'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 13, 201.5, '<span>'.$req['class'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 51, 201.5, '<span>'.$req['sub_class'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 89, 201.5, '<span>'.$req['area'].' sqm</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 127, 201.5, '<span>P '.$req['unit_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 165, 201.5, '<span>P '.$req['base_market_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 89, 221, '<span>'.$req['area'].' sqm</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 165, 221, '<span>P '.$req['total_base_market_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->SetFont('helvetica', '', 6);
					$pdf->StartTransform();
					$pdf->Rotate(90);
					$pdf->Text(200, 19, 'Date Printed: ' . $req['diag_date_printed']);
					$pdf->Text(200, 22, 'Printed By: ' . $req['diag_printed_by']);
					$pdf->StopTransform();
				}
				// Page 2
				else if($page == 2) {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->writeHTMLCell(47, 2, 13, 58, '<span>'.$req['pa_actual_use'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 65, 58, '<span>'.$req['pa_market_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(47, 2, 108, 58, '<span>'.$req['pa_assessment_level'].'%</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 160, 58, '<span>'.$req['pa_assessed_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 65, 73, '<span>'.$req['pa_market_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(37.9, 2, 160, 73, '<span>'.$req['pa_total_assessed_value'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->SetFont('helvetica', 'B', 9);
					$pdf->Text(30.5, 81.5, $req['pa_tax']);
					$pdf->Text(55, 81.5, $req['pa_exp']);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->Text(135, 81.5, $req['pa_effectivity_assess_quarter']);
					$pdf->Text(160, 81.5, $req['pa_effectivity_assess_year']);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->writeHTMLCell(55, 2, 13, 114, '<span>'.$req['appraised_by'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(36, 2, 69, 114, '<span>'.$req['appraised_by_date'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(55, 2, 106, 114, '<span>'.$req['recommending'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(36, 2, 162, 114, '<span>'.$req['recommending_date'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(60, 2, 70, 131, '<span>'.$req['approved_by'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(42, 2, 148, 131, '<span>'.$req['approved_by_date'].'</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(190, 18, 13, 146.5, '<p style="text-indent: 75px">'.$req['memoranda'].'</p>', 0, 0, 0, true, 'J', true);
					$pdf->Text(101, 172, $req['date_created']);
					$pdf->writeHTMLCell(59, 1, 140, 171, '<p>'.$req['entry_by'].'</p>', 0, 0, 0, true, 'C', true);
					$pdf->Text(22, 186.3, $req['superseded_pin']);
					$pdf->Text(28, 191, $req['superseded_arp_no']);
					$pdf->Text(127, 191, $req['superseded_td_no']);
					$pdf->Text(48, 196, $req['superseded_total_assessed_value']);
					$pdf->Text(40, 201, $req['superseded_previous_owner']);
					$pdf->Text(54, 205.6, $req['superseded_effectivity_assess']);
					$pdf->Text(35, 210.6, $req['superseded_ar_page_no']);
					$pdf->Text(43, 215.6, $req['superseded_recording_personnel']);
					$pdf->Text(166, 215.6, $req['superseded_date']);
				}
			}
			$this->addToLog($req['username'], $req['id']);
			return $pdf->Output('LandFaas_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf', 'E');
		}

		private function addToLog($username, $id) {
			$q = DB::select("CALL login_web('".$username."')");
			DB::select("CALL add_land_faas_log('PRINT LAND FAAS', ".$q[0]->user_id.", ".$id.")");
		}
}
