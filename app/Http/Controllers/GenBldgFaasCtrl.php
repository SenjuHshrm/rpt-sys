<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Classes\pdf\tcpdf\TCPDF;
use App\Classes\pdf\tcpdi\TCPDI;

class GenBldgFaasCtrl extends Controller
{
    public function genFile(Request $req) {
			$pdf = new TCPDI(PDF_PAGE_ORIENTATION, 'mm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$pdf->setTitle('BldgFaas_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf');
			$pdf->SetDisplayMode(100);
			$count = $pdf->SetSourceFile(base_path().'\resources\assets\pdf\building_faas_template.pdf');
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			for($page = 1; $page <= $count; $page++) {
				if($page == 1) {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->Text(144, 32, $req['transaction_code']);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->Text(27, 41.5, $req['arp_no']);
					$pdf->Text(122, 41.5, $req['pin']);
					if(strlen($req['owner_names']) > 124) {
						$pdf->SetFont('helvetica', '', 5);
					} else if(strlen($req['owner_names']) < 124 && strlen($req['owner_names']) > 248) {
						$pdf->SetFont('helvetica', '', 4);
					}
					$pdf->writeHTMLCell(108, 9, 27, 47, '<span>'.$req['owner_names'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(82, 8, 121, 99, '<span>'.$req['land_owner'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(113, 8, 22, 57, '<span>'.$req['owner_tins'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(54, 8, 149, 57, '<span>'.$req['owner_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
					if(strlen($req['admin_names']) > 129) {
						$pdf->SetFont('helvetica', '', 5);
					} else if(strlen($req['admin_names']) < 129 && strlen($req['admin_names']) > 258) {
						$pdf->SetFont('helvetica', '', 4);
					}
					$pdf->writeHTMLCell(96, 12, 39, 67, '<span>'.$req['admin_names'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(113, 8, 22, 80, '<span>'.$req['admin_tins'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(54, 8, 149, 80, '<span>'.$req['admin_contact_nos'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->writeHTMLCell(52, 9, 151, 47, '<span>'.$req['owner_addresses'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->writeHTMLCell(52, 12, 151, 67, '<span>'.$req['admin_addresses'].'</span>', 0, 0, 0, true, 'J', true);
					$pdf->Text(32, 101, $req['street_no']);
					$pdf->Text(20, 113, $req['barangay_district']);
					$pdf->Text(35, 125, $req['municipality']);
					$pdf->Text(36, 133, $req['province_city']);
					$pdf->Text(109, 112, $req['land_oct_tct_no']);
					$pdf->Text(109, 119, $req['land_survey_no']);
					$pdf->Text(166, 112, $req['land_lot_no']);
					$pdf->Text(166, 119, $req['land_survey_no']);
					$pdf->Text(129, 125, $req['land_arp_no']);
					$pdf->Text(120, 133, $req['land_area'] . ' sqm');
					$pdf->SetFont('helvetica', '', 8);
					$pdf->Text(35, 150, $req['kind_of_building']);
					$pdf->Text(141, 150, $req['building_age']);
					$pdf->Text(39, 155, $req['structural_type']);
					$pdf->Text(148, 155, $req['no_of_storeys']);
					$pdf->Text(39, 160, $req['building_permit_no']);
					$pdf->Text(85, 160, $req['permit_issue_date']);
					$pdf->Text(74, 165, $req['condominium_certificate']);
					$pdf->Text(71, 170, $req['completion_issue_date']);
					$pdf->Text(61, 179, $req['date_constructed']);
					$pdf->Text(39, 184, $req['date_occupied']);
					$pdf->Text(150, 160, $req['floor1_area'] . 'sqm');
					$pdf->Text(150, 165, $req['floor2_area']);
					$pdf->Text(150, 169.5, $req['floor3_area']);
					$pdf->Text(150, 174.5, $req['floor4_area']);
					$pdf->Text(151, 184, $req['total_floor_area'] . 'sqm');
					$pdf->Text(169, 240, '');
					$pdf->SetFont('helvetica', '', 6);
					$pdf->StartTransform();
					$pdf->Rotate(90);
					$pdf->Text(200, 42, 'Date Printed: ' . $req['diag_date_printed']);
					$pdf->Text(200, 45, 'Printed By: '. $req['diag_printed_by']);
					$pdf->StopTransform();
				} else if($page == 2) {
					$tpl = $pdf->importPage($page);
					$size = $pdf->getTemplateSize($tpl);
					$orn = $size['h'] > $size['w'] ? 'P' : 'L';
					$pdf->addPage($orn);
					$pdf->useTemplate($tpl, null, null, 0, 0, TRUE);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->Text(54, 43.5, $req['bc_unit_construction_cost']);
					$pdf->Text(36, 68.8, $req['bc_sub_total']);
					$pdf->Text(150, 73.5, $req['ad_sub_total']);
					$pdf->Text(43, 81.5, $req['depreciation_rate']);
					$pdf->Text(49, 86.5, $req['depreciation_cost']);
					$pdf->Text(140, 81.5, $req['total_percent_depreciation']);
					$pdf->Text(135, 86.5, $req['depreciated_market_value']);
					$pdf->SetFont('helvetica', '', 8);
					$pdf->writeHTMLCell(47, 3, 13, 107, '<span>' . $req['pa_actual_use'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(44, 3, 64, 107, '<span>' . $req['pa_market_value'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(47, 3, 108, 107, '<span>' . $req['pa_assessment_level'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(44, 3, 159, 107, '<span>' . $req['pa_assessed_value'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(44, 3, 64, 121.5, '<span>' . $req['pa_market_value'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(44, 3, 159, 121.5, '<span>' . $req['pa_total_assessed_value'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->SetFont('helvetica', 'B', 10);
					$pdf->Text(30.5, 130.8, $req['pa_taxable']);
					$pdf->Text(54, 130.8, $req['pa_exp']);
					$pdf->SetFont('helvetica', '', 9);
					$pdf->Text(135, 130.8, $req['pa_effectivity_assess_quarter']);
					$pdf->Text(158, 130.8, $req['pa_effectivity_assess_year']);
					$pdf->writeHTMLCell(53, 2, 8, 159, '<span>' . $req['appraised_by'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(30, 2, 68, 159, '<span>' . $req['appraised_by_date'] . '</span>', 0, 0, 0, true, 'C' , true);
					$pdf->writeHTMLCell(53, 2, 103, 159, '<span>' . $req['recommending'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(30, 2, 163, 159, '<span>' . $req['recommending_date'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(66, 2, 65, 176, '<span>' . $req['approved_by'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->writeHTMLCell(30, 2, 152, 176, '<span>' . $req['approved_by_date'] . '</span>', 0, 0, 0, true, 'C', true);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->writeHTMLCell(190, 18, 13, 186, '<p style="text-indent: 75px">' . $req['memoranda'] . '</p>', 0, 0, 0, true, 'J', true);
					$pdf->Text(98, 207, $req['date_created']);
					$pdf->writeHTMLCell(64, 1, 139, 207, '<p>' . $req['entry_by'] . '</p>', 0, 0, 0, true, 'C', true);
					$pdf->Text(21, 221.8, $req['superseded_pin']);
					$pdf->Text(28, 226.7, $req['superseded_arp_no']);
					$pdf->Text(122, 226.7, $req['superseded_td_no']);
					$pdf->Text(48, 231.6, $req['superseded_total_assessed_value']);
					$pdf->Text(40, 236.5, $req['superseded_previous_owner']);
					$pdf->Text(54, 241.2, $req['superseded_effectivity_assess']);
					$pdf->Text(43, 246.1, $req['superseded_recording_personnel']);
					$pdf->Text(160, 246.1, $req['superseded_date']);
				}
			}
			return $pdf->Output('BldgFaas_' . $req['pin'] . '_' . date('m-d-Y') . '.pdf', 'E');
		}

    private function addToLog($username, $id){
      $q = DB::select("CALL login_web('".$username."')");
      DB::select("CALL add_building_faas_log('PRINT BUILDING FAAS', ".$q[0]->user_id.", ".$id.")");
    }
}
