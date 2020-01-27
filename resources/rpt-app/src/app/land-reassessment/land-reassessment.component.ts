import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { MatTableDataSource } from '@angular/material';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import * as _ from 'lodash';
import * as jwt_decode from 'jwt-decode'
import { selectOpt } from '../interfaces/selectOpt';
import { landOwner } from '../interfaces/landOwner';
import { adminOwner } from '../interfaces/adminOwner';
import { stripInfo } from '../interfaces/stripInfo';
import { improvementInfo } from '../interfaces/improvementInfo';
import { marketValue } from '../interfaces/marketValue';
import { pincheck } from '../services/pincheck.service';
import { getMarketValues } from '../services/getMarketValues.service';
import { assessLand } from '../services/assesssLand.service';
import { getPosHolders } from  '../services/getPosHolders.service';
import { MatDialog } from '@angular/material/dialog';
import { LndReasmtSearch } from './dialog-search/lndreasmt-search';
import { genFaas } from '../services/genFaas.service';
import { landAsmtDataTemp } from '../classes/landAsmtDataTemp';
import * as moment from 'moment';
import { reassessments } from '../services/reassessments.service';
import { getBrgySubd } from '../services/getBrgySubd.service';

var ownerLs: landOwner[] = []
var adminLs: adminOwner[] = []
var stripInf: stripInfo[] = []
var imprInf: improvementInfo[] = []
var mrktVal: marketValue[] = []

@Component({
  selector: 'app-land-reassessment',
  templateUrl: './land-reassessment.component.html',
  styleUrls: ['./land-reassessment.component.scss']
})
export class LandReassessmentComponent implements OnInit {

  checkpinresult = 'help';

  ownersLs = new MatTableDataSource(ownerLs)
  adminsLs = new MatTableDataSource(adminLs)
  stripSetInfo = new MatTableDataSource(stripInf)
  impInf = new MatTableDataSource(imprInf)
  marketValue = new MatTableDataSource(mrktVal)
  clrMD: boolean;
  saveMD: boolean;
  stripToggleVal = false
	ownAdd: boolean;
  adminAdd: boolean;
  otheImprvmntsAdd: boolean;
  mvAdd: boolean;
  lndApprAdd: boolean;
	otherTrns: boolean = false;
	username: string;
	searchBtnClckd: boolean = false;
	approvedBy: string;
	landFaasId: number;
	brgySubdLs: any;
	lsBrgy: selectOpt[] = [];
	lsSubd: selectOpt[] = [
		{ value: 'NONE', viewVal: 'NONE' }
	];

  stripToggle(grp: any) {
    this.stripToggleVal = !this.stripToggleVal
		this.computeBMV(this.landAssessment.get('landAppraisal'));
    if (this.stripToggleVal) {
      Object.keys(grp.controls).forEach(key => {
        grp.controls[key].enable();
      });
			grp.controls['remLandArea'].setValue(this.landAssessment.get('landAppraisal').get('area').value)
    } else {
      Object.keys(grp.controls).forEach(key => {
				grp.controls[key].reset();
        grp.controls[key].disable();
      });


    }

  }

  ownerHeader: string[] = ['fname', 'mname', 'lname', 'address', 'contact', 'tin', 'actions']
  adminHeader: string[] = ['fname', 'mname', 'lname', 'address', 'contact', 'tin', 'actions']
  stripHeader: string[] = ['stripno', 'striparea', 'adjustment', 'adbaserate', 'stripmval', 'actions']
  impHeader: string[] = ['kind', 'total', 'unitval', 'baseval', 'actions']
  mValHeader: string[] = ['bmval', 'adjfactor', 'adjperc', 'adjval', 'markval', 'actions']

  trnsLs: selectOpt[] = [
    { value: 'GENERAL REVISION (GR)', viewVal: 'GENERAL REVISION (GR)' }
  ]

	landClassLs = [];

  lndAppSubc: number;
  lndAppUnitVal: string = '';
  subClassLs: selectOpt[];
  lndAppBMV: string = '0';
  lndAppArea: string;

  actualUse: selectOpt[] = [
    { value: 'COMMERCIAL', viewVal: 'COMMERCIAL' },
    { value: 'INDUSTRIAL', viewVal: 'INDUSTRIAL' },
    { value: 'RESIDENTIAL', viewVal: 'RESIDENTIAL' },
    { value: 'AGRICULTURAL', viewVal: 'AGRICULTURAL' }
  ]

  status: selectOpt[] = [
    { value: 'TAXABLE', viewVal: 'TAXABLE' },
    { value: 'EXEMPTED', viewVal: 'EXEMPTED' }
  ]

  quarter: selectOpt[] = [
    { value: '1', viewVal: '1' },
    { value: '2', viewVal: '2' },
    { value: '3', viewVal: '3' },
    { value: '4', viewVal: '4' }
  ]

  stripNo: selectOpt[]

  public landAssessment: FormGroup;

  constructor(
		private router: ActivatedRoute,
		private route: Router,
		private chckpin: pincheck,
		private matDialog: MatDialog,
		private gLndFaas: genFaas,
		private getMrktVal: getMarketValues,
		private asmtLand: assessLand,
		private gPosHolder: getPosHolders,
		private reasmt: reassessments,
		private lsBrgySubd: getBrgySubd
	) { }

  ngOnInit() {
    let token: any = jwt_decode(localStorage.getItem('auth'));
    this.username = token.username
    this.resetForm();
		this.getMrktVal.getValues().subscribe(res => {
			_.forEach(res, arr => {
				this.landClassLs.push(arr)
			})
		})
		this.gPosHolder.getPosHoldersCl("FAAS").subscribe(res => {
			this.approvedBy = res[0].holder_name;
			this.landAssessment.get('propertyAssessment').get('approvedName').setValue(res[0].holder_name)
		})
		this.lsBrgySubd.get().subscribe(res => {
			this.brgySubdLs = res.res;
			let brgys = Array.from(new Set(res.res.map(x => x.barangay_name)));
			_.forEach(brgys, (arr: string) => {
				this.lsBrgy.push({
					value: arr,
					viewVal: arr
				});
			})
		})
		setTimeout(()=>{
			this.otherTrns = true;
			const md = this.matDialog.open(LndReasmtSearch, { disableClose: true, data: {tCode: 'GENERAL REVISION (GR)'}, width: '90%', height: '90%', panelClass: 'custom-dialog-container' })
			md.afterClosed().subscribe(res => {
				if(res == undefined) {
					this.route.navigate(['/user/' + this.username + '/reassessments']);
					this.otherTrns = false
				} else {
					this.populateForm(res)
				}
			})
		}, 100)
	}

	setDistCodeSubd() {
		this.lsSubd = [
			{ value: 'NONE', viewVal: 'NONE'}
		];
		let val = this.landAssessment.get('propertyLocation').get('barangay').value,
				obj = _.find(this.brgySubdLs, { barangay_name: val })
		this.landAssessment.get('pin').get('district').setValue((obj.district_code.length == 2) ? obj.district_code : '0' + obj.district_code);
		this.landAssessment.get('pin').get('barangay').setValue((obj.barangay_code.length == 2) ? '0' + obj.barangay_code : '00' + obj.barangay_code);
		_.forEach(this.brgySubdLs, arr => {
			if(arr.barangay_name == val) {
				if(arr.subdivision_name != null) {
					this.lsSubd.push({
						value: arr.subdivision_name,
						viewVal: arr.subdivision_name
					});
				}
			}
		});
	}

	setClsSubCls() {
		let brgyVal = this.landAssessment.get('propertyLocation').get('barangay').value,
				subdVal = this.landAssessment.get('propertyLocation').get('subdivision').value,
				obj = _.find(this.brgySubdLs, { barangay_name: brgyVal, subdivision_name: subdVal });
		if(subdVal != 'NONE') {
			this.landAssessment.get('landAppraisal').get('class').setValue('RESIDENTIAL');
			this.lndAppChngVal(this.landAssessment.get('landAppraisal'))
			this.landAssessment.get('landAppraisal').get('subclass').setValue(obj.sub_class);
			this.lnAppSubCUV(this.landAssessment.get('landAppraisal'))
			this.landAssessment.get('propertyAssessment').get('actualUse').setValue('RESIDENTIAL')
		} else {
			this.landAssessment.get('landAppraisal').get('class').setValue('');
			this.landAssessment.get('landAppraisal').get('subclass').setValue('');
			this.landAssessment.get('propertyAssessment').get('actualUse').setValue('')
		}
	}

  lndAppChngVal(grp: any) {
    let val = grp.controls['class'].value;
		this.subClassLs = [];
		Object.keys(this.landClassLs).forEach(key => {
			if(this.landClassLs[key].class == val) {
				this.subClassLs.push({
					value: this.landClassLs[key].sub_class,
					viewVal: this.landClassLs[key].sub_class
				})
			}
		})
		grp.controls['unitVal'].reset();
    grp.controls['baseMarketVal'].reset();
  }

  isVisible_spinner = false
  pinspinner = true

	setAsmtLvl(propAsmt: any) {
		let actlUse = propAsmt.get('actualUse').value,
				asmtLvl = propAsmt.get('assessmentLvl');
		switch(actlUse) {
			case 'RESIDENTIAL':
				asmtLvl.setValue('15');
				break;
			case 'AGRICULTURAL':
				asmtLvl.setValue(40);
				break;
			case 'COMMERCIAL':
				asmtLvl.setValue(40);
				break;
			case 'INDUSTRIAL':
				asmtLvl.setValue(40);
				break;
		}
		this.compAssessedVal(propAsmt)

	}

	compAssessedVal(propAsmt: any) {
		let asmtVal = parseFloat(this.lndAppBMV) * (parseFloat(propAsmt.get('assessmentLvl').value) / 100);
		propAsmt.get('assessedVal').setValue(asmtVal);
	}

	populateForm(id: number): void {
		this.landFaasId = id;
		let data = {
			id: id
		}
		this.gLndFaas.generateLand(data).subscribe(res => {
			this.initializeForm(res);
		})

	}

  lnAppSubCUV(grp: any) {
    let clss = grp.controls['class'].value,
				subclss = grp.controls['subclass'].value;
		Object.keys(this.landClassLs).forEach(key => {
			if(this.landClassLs[key].class == clss) {
				if(this.landClassLs[key].sub_class == subclss) {
					this.lndAppUnitVal = this.landClassLs[key].unit_market_value
				}
			}
		})
		this.computeBMV(grp);
  }

	stripUpdated: boolean;

  computeBMV(grp: any) {
		(grp.controls['area'].value == null || grp.controls['area'].value == '') ? this.lndAppArea = '0' : this.lndAppArea = grp.controls['area'].value;
		let area: number = parseFloat(this.lndAppArea);
		let unitVl: number = parseFloat(this.lndAppUnitVal);
		this.lndAppBMV = (grp.controls['interiorLot'].value == 1) ? ((area * unitVl) / 2).toString() : (area * unitVl).toString();
		this.stripNo = [];
    if(!this.otherTrns) {
	    for(let i = 1; i <= (+this.landAssessment.get('stripSet').get('stripCount').value); i++) {
				this.stripNo.push({ value: i.toString(), viewVal: i.toString() })
			}
			let strpSet = this.landAssessment.get('stripSet');
			strpSet.get('adjustment').setValue('0');
			let adjustedBaseRate = parseFloat(this.lndAppUnitVal) * (1 + (+strpSet.get('adjustment').value / 100));
			let stripMarkVal = adjustedBaseRate * parseFloat(grp.controls['area'].value);
			strpSet.get('stripCount').setValue('1');
			strpSet.get('stripNo').setValue('1');
			if(!this.stripToggleVal) {
				stripInf = [];
				stripInf.push({
					stripNum: strpSet.get('stripNo').value,
		      stripArea: grp.controls['area'].value,
		      adjustment: strpSet.get('adjustment').value,
		      adjustedBaseRate: adjustedBaseRate.toString(),
		      stripMarkVal: (stripMarkVal.toString() == 'NaN') ? '0' : stripMarkVal.toString()
				})
				this.stripSetInfo = new MatTableDataSource(stripInf)
				this.lndAppBMV = ((area * unitVl).toString() == 'NaN') ? '0' : (area * unitVl).toString()
			} else {
				this.lndAppBMV = '0'
				this.landAssessment.get('stripSet').get('remLandArea').setValue(grp.controls['area'].value);
				this.landAssessment.get('stripSet').get('stripArea').setValue('')
				let stripObj = _.find(stripInf, { stripNum: '1' });
				stripObj.stripNum = '1'
				stripObj.stripArea = '0'
				stripObj.adjustment = '0'
				stripObj.adjustedBaseRate = '0'
				stripObj.stripMarkVal = '0'
				stripInf = [];
				stripInf.push(stripObj)
				this.stripSetInfo = new MatTableDataSource(stripInf);
			}
		}
		let prpAsmtVal = parseFloat(this.lndAppBMV) * (parseFloat(this.landAssessment.get('propertyAssessment').get('assessmentLvl').value) / 100)
		this.landAssessment.get('propertyAssessment').get('assessedVal').setValue(prpAsmtVal.toString())
  }

  save(form: any) {
		console.log(form)
    let data: any = {
			id: this.landFaasId,
			trnsCode: 'GENERAL REVISION (GR)',
			arpNo: form.arpNo,
			pin: {
				city: form.pin.city,
				district: form.pin.district,
				barangay: form.pin.barangay,
				section: form.pin.section,
				parcel: form.pin.parcel,
			},
			OCT_TCT: form.OCT_TCT,
			surveyNo: form.surveyNo,
			lotNo: form.lotNo,
			blockNo: form.blockNo,
			propLoc: {
				streetNo: form.propertyLocation.streetNo,
				brgy: form.propertyLocation.barangay,
				subd: form.propertyLocation.subdivision,
				city: form.propertyLocation.city,
				province: form.propertyLocation.province,
				north: form.propertyLocation.north,
				south: form.propertyLocation.south,
				east: form.propertyLocation.east,
				west: form.propertyLocation.west,
			},
			ownerDetails: this.getOwners(),
			adminDetails: this.getAdmins(),
			landAppraisal: {
				class: form.landAppraisal.class,
				subCls: form.landAppraisal.subclass,
				area: form.landAppraisal.area,
				unitVal: this.lndAppUnitVal,
				baseMarketVal: this.lndAppBMV,
				interiorLot: form.landAppraisal.interiorLot,
				cornerLot: form.landAppraisal.cornerLot,
				stripping: form.landAppraisal.stripping,
			},
			stripSet: this.getStrip(),
			othImp: this.getImpr(),
			marketVal: this.getMarketVal(),
			propAsmt: {
				actualUse: form.propertyAssessment.actualUse,
				marketVal: form.propertyAssessment.marketVal,
				assessmentLvl: form.propertyAssessment.assessmentLvl,
				assessedVal: form.propertyAssessment.assessedVal,
				specialClass: form.propertyAssessment.specialClass,
				status: form.propertyAssessment.status,
				efftQ: form.propertyAssessment.efftQ,
				effty: form.propertyAssessment.effty,
				total: form.propertyAssessment.total,
				appraisedName: form.propertyAssessment.appraisedName,
				appraisedDate: (form.propertyAssessment.appraisedDate == '') ? '' : moment(form.propertyAssessment.appraisedDate).format('MM/DD/YYYY'),
				recommendName: form.propertyAssessment.recommendName,
				recommendDate: (form.propertyAssessment.recommendDate == '') ? '' : moment(form.propertyAssessment.recommendDate).format('MM/DD/YYYY'),
				approvedName: form.propertyAssessment.approvedName,
				approvedDate: (form.propertyAssessment.approvedDate == '') ? '' : moment(form.propertyAssessment.approvedDate).format('MM/DD/YYYY'),
				memoranda: form.propertyAssessment.memoranda,
			},
			supersededRec: {
				supPin: form.supersededRec.supPin,
				supArpNo: form.supersededRec.supArpNo,
				supTDNo: form.supersededRec.supTDNo,
				supTotalAssessedVal: form.supersededRec.supTotalAssessedVal,
				supPrevOwner: form.supersededRec.supPrevOwner,
				supEff: form.supersededRec.supEff,
				supARPageNo: form.supersededRec.supARPageNo,
				supRecPersonnel: form.supersededRec.supRecPersonnel,
				supDate: (form.supersededRec.supDate == '') ? '' : moment(form.supersededRec.supDate).format('MM/DD/YYYY'),
			},
			status: form.status,
			dateCreated: form.dateCreated,
			encoder: form.encoder,
			attachment: form.attachment,
		};
		console.log(data);
		this.reasmt.reassessLand(data).subscribe(res => {
			console.log(res)
			if(res.res) {
				this.resetForm();
				this.searchBtnClckd = false;
				const md = this.matDialog.open(LndReasmtSearch, { disableClose: true, data: {tCode: 'GENERAL REVISION (GR)'}, width: '90%', height: '90%', panelClass: 'custom-dialog-container' })
				md.afterClosed().subscribe(res => {
					if(res == undefined) {
						this.route.navigate(['/user/' + this.username + '/reassessments']);
						this.otherTrns = false;
					} else {
						this.populateForm(res);
					}
				})
			}
		})
  }

	getOwners(): landOwner[] {
		let data: landOwner[] = [];
		_.forEach(ownerLs, (arr) => {
			data.push({
				ownFName: arr.ownFName,
				ownMName: arr.ownMName,
				ownLName: arr.ownLName,
			  ownAddress: arr.ownAddress,
			  ownContact: arr.ownContact,
			  ownTIN: arr.ownTIN,
			});
		});
		return data;
	}

	getAdmins(): adminOwner[] {
		let data: adminOwner[] = [];
		_.forEach(adminLs, arr => {
			data.push({
				admFName: arr.admFName,
				admMName: arr.admMName,
				admLName: arr.admLName,
			  admAddress: arr.admAddress,
			  admContact: arr.admContact,
			  admTIN: arr.admTIN,
			});
		});
		return data;
	}

	getStrip(): stripInfo[] {
		let data: stripInfo[] = [];
		_.forEach(stripInf, arr => {
			data.push({
				stripNum: arr.stripNum,
			  stripArea: arr.stripArea,
			  adjustment: arr.adjustment,
			  adjustedBaseRate: arr.adjustedBaseRate,
			  stripMarkVal: arr.stripMarkVal,
			});
		});
		return data;
	}

	getImpr(): improvementInfo[] {
		let data: improvementInfo[] = [];
		_.forEach(imprInf, arr => {
			data.push({
				kind: arr.kind,
			  totalNo: arr.totalNo,
			  unitVal: arr.unitVal,
			  baseMarkVal: arr.baseMarkVal,
			});
		});
		return data;
	}

	getMarketVal(): marketValue[] {
		let data: marketValue[] = [];
		_.forEach(mrktVal, arr => {
			data.push({
				mBaseVal: arr.mBaseVal,
			  mAdjustFactor: arr.mAdjustFactor,
			  mAdjustPercentage: arr.mAdjustPercentage,
			  mAdjustValue: arr.mAdjustValue,
			  mMarketVal: arr.mMarketVal,
			});
		});
		return data;
	}

  setStripNumSel(grp: any) {

		stripInf = [];
    this.stripNo = []
    let cnt = +grp.controls['stripCount'].value
    for (let i = 1; i <= cnt; i++) {
      this.stripNo.push({ value: i.toString(), viewVal: i.toString() })
			stripInf.push({
				stripNum: i.toString(),
	      stripArea: '0',
	      adjustment: '0',
	      adjustedBaseRate: '0',
	      stripMarkVal: '0'
			});
    }
		grp.controls['remLandArea'].setValue(this.landAssessment.get('landAppraisal').get('area').value);
		this.stripSetInfo = new MatTableDataSource(stripInf);
  }

  addOwner(grp: any) {
    let ownerData = grp.value
    ownerLs.push({
			ownFName: ownerData.ownfName,
			ownMName: ownerData.ownmName,
			ownLName: ownerData.ownlName,
      ownAddress: ownerData.ownaddress,
      ownContact: ownerData.owncontact,
      ownTIN: ownerData.ownTIN
    })
    this.ownersLs = new MatTableDataSource(ownerLs)
    Object.keys(grp.controls).forEach(key => {
      grp.controls[key].reset()
    })
  }

  addAdmin(grp: any) {
    let adminData = grp.value
    adminLs.push({
      admFName: adminData.admfName,
			admMName: adminData.middle_name,
			admLName: adminData.last_name,
      admAddress: adminData.admaddress,
      admContact: adminData.admcontact,
      admTIN: adminData.admTIN
    })
    this.adminsLs = new MatTableDataSource(adminLs)
    Object.keys(grp.controls).forEach(key => {
      grp.controls[key].reset()
    })
  }

  addStrip(grp: any) {
    let stripData = grp.value
		let remLnd: number = 0;
    let adjustedBaseRate: number = 0;
    let stripMarkVal: number = 0;
		let adjPerc = (stripData.adjustment == '0') ? 1 : (parseFloat(stripData.adjustment) / 100)
    if(parseFloat(stripData.remLandArea) <= 0) {
			let obj: any = _.find(stripInf, { stripNum: stripData.stripNo })
			remLnd = parseFloat(obj.stripArea) - parseFloat(stripData.stripArea);
			adjustedBaseRate = parseFloat(this.lndAppUnitVal) * adjPerc;
			stripMarkVal = adjustedBaseRate * parseFloat(stripData.stripArea);
		} else {
			remLnd = parseFloat(stripData.remLandArea) - parseFloat(stripData.stripArea);
			adjustedBaseRate = parseFloat(this.lndAppUnitVal) * adjPerc;
			stripMarkVal = adjustedBaseRate * parseFloat(stripData.stripArea);
		}
		let ind = _.findIndex(stripInf, { stripNum: stripData.stripNo });
		stripInf.splice(ind, 1, {
			stripNum: stripData.stripNo,
			stripArea: stripData.stripArea,
			adjustment: stripData.adjustment,
			adjustedBaseRate: adjustedBaseRate.toString(),
			stripMarkVal: stripMarkVal.toString()
		})
		grp.controls['remLandArea'].setValue(remLnd.toString());
		this.stripSetInfo = new MatTableDataSource(stripInf)
		this.stripComp();
		this.compAssessedVal(this.landAssessment.get('propertyAssessment'))
  }

  stripComp() {
    let totalMarketVal = 0;
		_.forEach(stripInf, arr => {
			totalMarketVal = totalMarketVal + parseFloat(arr.stripMarkVal)
		})
		this.lndAppBMV = (totalMarketVal).toString();
  }

  addImp(grp: any) {
    let impData = grp.value
    imprInf.push({
      kind: impData.kind,
      totalNo: impData.totalNo,
      unitVal: impData.unitVal,
      baseMarkVal: impData.basicMarketVal
    })
    this.impInf = new MatTableDataSource(imprInf)
    Object.keys(grp.controls).forEach(key => {
      grp.controls[key].reset()
    })
  }

  addMVal(grp: any) {
    let mValue = grp.value
    mrktVal.push({
      mBaseVal: '',
      mAdjustFactor: '',
      mAdjustPercentage: '',
      mAdjustValue: '',
      mMarketVal: ''
    })
    this.marketValue = new MatTableDataSource(mrktVal)
    Object.keys(grp.controls).forEach(key => {
      grp.controls[key].reset()
    })
  }

  removeOwnerDetail(evt: any) {
    _.remove(ownerLs, evt)
    this.ownersLs = new MatTableDataSource(ownerLs)
  }

  removeAdminDetail(evt: any) {
    _.remove(adminLs, evt)
    this.adminsLs = new MatTableDataSource(adminLs)
  }

  removeStripDetail(evt: any) {
		if(this.landAssessment.get('landAppraisal').get('stripping').value) {
			this.lndAppBMV = (parseFloat(this.lndAppBMV) - parseFloat(evt.stripMarkVal)).toString()
			this.landAssessment.get('stripSet').get('remLandArea').setValue((parseFloat(this.landAssessment.get('stripSet').get('remLandArea').value) + parseFloat(evt.stripArea)).toString())
			let ind = _.findIndex(stripInf, { stripNum: evt.stripNum });
			stripInf.splice(ind, 1, {
				stripNum: evt.stripNum,
	      stripArea: '0',
	      adjustment: '0',
	      adjustedBaseRate: '0',
	      stripMarkVal: '0'
			})
			this.stripSetInfo = new MatTableDataSource(stripInf);
		}
		this.compAssessedVal(this.landAssessment.get('propertyAssessment'))
  }

  removeImp(evt: any) {
    _.remove(imprInf, evt)
    this.impInf = new MatTableDataSource(imprInf)
  }

  removeMVal(evt: any) {
    _.remove(mrktVal, evt)
    this.marketValue = new MatTableDataSource(mrktVal)
  }

  initializeForm(xobj: any) {
		if(xobj instanceof Object) {
			let data = xobj.faas,
					owners = xobj.owners,
					admins = xobj.admins,
					strips = xobj.strips,
					marketval = xobj.marketval;
			this.landAssessment.controls['trnsCode'].setValue('GENERAL REVISION (GR)');
			this.landAssessment.controls['arpNo'].setValue(data.arp_no);
			this.landAssessment.controls['pin'].setValue({
				city: data.pin_city,
				district: data.pin_district,
				barangay: data.pin_barangay,
				section: data.pin_section,
				parcel: data.pin_parcel
			});
			this.landAssessment.controls['OCT_TCT'].setValue(data.OCT_TCT_no);
			this.landAssessment.controls['surveyNo'].setValue(data.survey_no);
			this.landAssessment.controls['lotNo'].setValue(data.lot_no);
			this.landAssessment.controls['blockNo'].setValue(data.block_no);
			this.landAssessment.controls['propertyLocation'].setValue({
				streetNo: data.street_no,
				barangay: data.barangay,
				subdivision: data.subdivision,
				city: data.city,
				province: data.province,
				north: data.north,
				south: data.south,
				east: data.east,
				west: data.west
			});
			this.landAssessment.controls['landAppraisal'].setValue({
				class: data.class,
				subclass: data.sub_class,
				area: data.area,
				unitVal: data.unit_value,
				baseMarketVal: data.base_market_value,
				interiorLot: data.interior_lot,
				cornerLot: data.corner_lot,
				stripping: data.stripping
			})
			this.lndAppChngVal(this.landAssessment.get('landAppraisal'));
			this.lnAppSubCUV(this.landAssessment.get('landAppraisal'));
			this.landAssessment.controls['marketVal'].setValue({
				baseMarketVal: data.base_market_value,
				adjustmentFactor: '',
				adjustmentPercent: '',
				adjustmentVal: '',
				marketVal: '',
				mvSubTotal: data.base_market_value
			});
			this.landAssessment.controls['propertyAssessment'].setValue({
				actualUse: data.pa_actual_use,
				marketVal: data.pa_market_value,
				assessmentLvl: data.pa_assessment_level,
				assessedVal: data.pa_assessed_value,
				specialClass: data.pa_special_class,
				status: data.pa_status,
				efftQ: data.pa_effectivity_assess_quarter,
				effty: data.pa_effectivity_assess_year,
				total: data.pa_total_assessed_value,
				appraisedName: data.appraised_by,
				appraisedDate: (data.appraised_by_date == '') ? '' : new Date(data.appraised_by_date),
				recommendName: data.recommending,
				recommendDate: (data.recommending_date == '') ? '' : new Date(data.recommending_date),
				approvedName: data.approved_by,
				approvedDate: (data.approved_by_date == '') ? '' : new Date(data.approved_by_date),
				memoranda: data.memoranda
			})
			this.landAssessment.controls['supersededRec'].setValue({
				supPin: data.superseded_pin,
				supArpNo: data.superseded_arp_no,
				supTDNo: data.superseded_td_no,
				supTotalAssessedVal: data.superseded_total_assessed_value,
				supPrevOwner: data.superseded_previous_owner,
				supEff: data.superseded_effectivity_assess,
				supARPageNo: data.superseded_ar_page_no,
				supRecPersonnel: data.superseded_recording_personnel,
				supDate: (data.superseded_date == '') ? '' : new Date(data.superseded_date)
			})
			this.landAssessment.controls['status'].setValue(data.status);
			this.landAssessment.controls['dateCreated'].setValue(data.date_created);
			this.landAssessment.controls['encoder'].setValue(this.username);
			this.landAssessment.controls['attachment'].setValue(data.attachment_file);
			this.setAsmtLvl(this.landAssessment.get('propertyAssessment'));
			_.forEach(owners, arr => {
				ownerLs.push({
					ownFName: arr.first_name,
					ownMName: arr.middle_name,
					ownLName: arr.last_name,
					ownAddress: arr.address,
					ownContact: arr.contact_no,
					ownTIN: arr.TIN
				});
			});
			_.forEach(admins, arr => {
				adminLs.push({
					admFName: arr.first_name,
					admMName: arr.middle_name,
					admLName: arr.last_name,
					admAddress: arr.address,
					admContact: arr.contact_no,
					admTIN: arr.TIN
				});
			});
			_.forEach(strips, arr => {
				stripInf.push({
					stripNum: arr.land_strip_no,
					stripArea: arr.area,
					adjustedBaseRate: arr.adjusted_unit_value,
					adjustment: arr.adjustment_percentage,
					stripMarkVal: arr.adjusted_market_value,
				});
			});
			_.forEach(marketval, arr => {
				mrktVal.push({
					mBaseVal: arr.base_market_value,
					mAdjustValue: arr.adjustment_value,
					mAdjustFactor: arr.type,
					mAdjustPercentage: arr.adjustment_percentage,
					mMarketVal: arr.market_value,
				})
			});
			this.ownersLs = new MatTableDataSource(ownerLs);
		  this.adminsLs = new MatTableDataSource(adminLs);
			this.stripSetInfo = new MatTableDataSource(stripInf);
			this.marketValue = new MatTableDataSource(mrktVal);
		} else {
			this.landAssessment = new FormGroup({
	      trnsCode: new FormControl({ value: 'GENERAL REVISION (GR)', disabled: true }),
	      arpNo: new FormControl(''),
	      pin: new FormGroup({
	        city: new FormControl('', [Validators.required]),
	        district: new FormControl('', [Validators.required]),
	        barangay: new FormControl('', [Validators.required]),
	        section: new FormControl('', [Validators.required]),
	        parcel: new FormControl('', [Validators.required])
	      }),
	      OCT_TCT: new FormControl(''),
	      surveyNo: new FormControl(''),
	      lotNo: new FormControl(''),
	      blockNo: new FormControl(''),
	      propertyLocation: new FormGroup({
	        streetNo: new FormControl(''),
	        barangay: new FormControl(''),
	        subdivision: new FormControl(''),
	        city: new FormControl(''),
	        province: new FormControl(''),
	        north: new FormControl(''),
	        south: new FormControl(''),
	        east: new FormControl(''),
	        west: new FormControl('')
	      }),
	      ownerDetails: new FormGroup({
	        ownfName: new FormControl(''),
	        ownmName: new FormControl(''),
	        ownlName: new FormControl(''),
	        ownaddress: new FormControl(''),
	        owncontact: new FormControl(''),
	        ownTIN: new FormControl(''),
	      }),
	      adminOwnerLs: new FormGroup({
	        admfName: new FormControl(''),
	        admmName: new FormControl(''),
	        admlName: new FormControl(''),
	        admaddress: new FormControl(''),
	        admcontact: new FormControl(''),
	        admTIN: new FormControl(''),
	      }),
	      landAppraisal: new FormGroup({
	        class: new FormControl(''),
	        subclass: new FormControl(''),
	        area: new FormControl('0'),
	        unitVal: new FormControl(''),
	        baseMarketVal: new FormControl(''),
	        interiorLot: new FormControl(''),
	        cornerLot: new FormControl(''),
	        stripping: new FormControl(false)
	      }),
	      stripSet: new FormGroup({
	        stripCount: new FormControl({ value: '', disabled: true }),
	        remLandArea: new FormControl({ value: '', disabled: true }),
	        stripArea: new FormControl({ value: '', disabled: true }),
	        adjustment: new FormControl({ value: '', disabled: true }),
	        stripNo: new FormControl({ value: '', disabled: true })
	      }),
	      otherImprovements: new FormGroup({
	        kind: new FormControl(''),
	        totalNo: new FormControl(''),
	        unitVal: new FormControl(''),
	        basicMarketVal: new FormControl(''),
	        othImpSubTotal: new FormControl({ value: '', disabled: true })
	      }),
	      marketVal: new FormGroup({
	        baseMarketVal: new FormControl(''),
	        adjustmentFactor: new FormControl(''),
	        adjustmentPercent: new FormControl(''),
	        adjustmentVal: new FormControl(''),
	        marketVal: new FormControl(''),
	        mvSubTotal: new FormControl({ value: '', disabled: true })
	      }),
	      propertyAssessment: new FormGroup({
	        actualUse: new FormControl(''),
	        marketVal: new FormControl('0'),
	        assessmentLvl: new FormControl(''),
	        assessedVal: new FormControl(''),
	        specialClass: new FormControl(''),
	        status: new FormControl(''),
	        efftQ: new FormControl(''),
	        effty: new FormControl(''),
	        total: new FormControl(''),
	        appraisedName: new FormControl(''),
	        appraisedDate: new FormControl(''),
	        recommendName: new FormControl(''),
	        recommendDate: new FormControl(''),
	        approvedName: new FormControl(''),
	        approvedDate: new FormControl(''),
	        memoranda: new FormControl('')
	      }),
	      supersededRec: new FormGroup({
	        supPin: new FormControl(''),
	        supArpNo: new FormControl(''),
	        supTDNo: new FormControl(''),
	        supTotalAssessedVal: new FormControl(''),
	        supPrevOwner: new FormControl(''),
	        supEff: new FormControl(''),
	        supARPageNo: new FormControl(''),
	        supRecPersonnel: new FormControl(''),
	        supDate: new FormControl(''),
	      }),
	      status: new FormControl(''),
	      dateCreated: new FormControl(''),
	      encoder: new FormControl(''),
	      attachment: new FormControl(''),
	    })
			this.lndAppUnitVal = '0';
			this.lndAppBMV ='0';
			this.landAssessment.get('propertyAssessment').get('actualUse').setValue('COMMERCIAL');
			this.landAssessment.get('propertyAssessment').get('approvedName').setValue(this.approvedBy);
			this.setAsmtLvl(this.landAssessment.get('propertyAssessment'))
		}

  }

	resetForm() {
		this.initializeForm('');
		ownerLs = [];
		adminLs = [];
		stripInf = [];
		imprInf = [];
		mrktVal = [];
		this.ownersLs = new MatTableDataSource(ownerLs)
	  this.adminsLs = new MatTableDataSource(adminLs)
	  this.stripSetInfo = new MatTableDataSource(stripInf)
	  this.impInf = new MatTableDataSource(imprInf)
	  this.marketValue = new MatTableDataSource(mrktVal)
	}

  srch() {
		this.resetForm();
    this.otherTrns = true;
		this.searchBtnClckd = true;
    const md = this.matDialog.open(LndReasmtSearch, { disableClose: true, data: {tCode: 'GENERAL REVISION (GR)'}, width: '90%', height: '90%', panelClass: 'custom-dialog-container' })
    md.afterClosed().subscribe(res => {
      if(res == undefined) {
        if(!this.searchBtnClckd) {
					this.route.navigate(['/user/' + this.username + '/reassessments']);
	        this.otherTrns = false
				}
      } else {
        this.populateForm(res)
      }
    })
  }

}
