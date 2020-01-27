import { Component, OnInit, Inject, ViewEncapsulation } from '@angular/core';
import { selectOpt } from '../interfaces/selectOpt';
import { searchRec } from '../services/searchFaasRec.service';
import { landTaxTable } from '../interfaces/landTaxTable';
import { landTaxInfOwn } from '../interfaces/landTaxInfOwn';
import { landTaxInfAdm } from '../interfaces/landTaxInfAdm';
import { landTaxTableBldg } from '../interfaces/landTaxTableBldg';
import { getPosHolders } from '../services/getPosHolders.service'
import { MatTableDataSource } from '@angular/material';
import { lTaxClearance } from '../classes/lTaxClearance';
import * as _ from 'lodash';
import * as jwt_decode from 'jwt-decode';
import { MatDialog, MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';
import { genLandTaxCl } from '../services/genLandTaxCl';
// import docxtemplater from 'docxtemplater';
// import * as JSZip from 'jszip';
// import * as JSZipUtils from 'jszip-utils';
import * as moment from 'moment';
import { DomSanitizer } from '@angular/platform-browser';
import { Pipe, PipeTransform } from '@angular/core';
import { getClFile } from '../services/getClFile.service';
import { Router } from '@angular/router';

var ltTableLs: landTaxTable[] = []
var ltTableBldgLs: landTaxTableBldg[] = []
var ltTableInfOwner: landTaxInfOwn[] = []
var ltTableInfAdmin: landTaxInfAdm[] = []

@Component({
  selector: 'app-clearance',
  templateUrl: './clearance.component.html',
  styleUrls: ['./clearance.component.scss'],
  //styles: [''],
  //encapsulation: ViewEncapsulation.None,
})
export class ClearanceComponent implements OnInit {

  LTTable = new MatTableDataSource(ltTableLs);
  LTTableInfOwn = new MatTableDataSource(ltTableInfOwner);
  LTTableInfAdm = new MatTableDataSource(ltTableInfAdmin);

  LTTableBldg = new MatTableDataSource(ltTableBldgLs);

  input1: string;
  amount: string;
  CTONo: string;
  dated: string;
  requestor: string;
  purpose: string;
  encoder1: string;
  date: string;
  certfee: string;
  amt: string;
  orNo: string;
  remarks: string;
  posHolders: any;
	selectedRow = [];
	selectedOwner = [];
	selectedAdmin = [];
	faas: any;
	owner: any;
	admin: any;
  clseDb: boolean;
	clrnceMD: boolean;


  lTaxHeader: string[] = [
    'arpNo', 'pin', 'surveyNo', 'lotNo', 'blockNo',
    'streetNo', 'brgy', 'subd', 'city', 'province',
    'class', 'subclass', 'area', 'assessedVal', 'stat'
  ];

  lTaxBldgHeader: string[] = [
    'arpNo', 'pin', 'brgy', 'subd', 'city',
    'province', 'kind', 'structType', 'bldgPermit', 'dateConstr',
    'storey', 'actualUse', 'assessedVal'
  ];

  lTaxInfHeaderOwn: string[] = [
    'ownName', 'ownAddress', 'ownContact', 'ownTIN'
  ];

  lTaxInfHeaderAdm: string[] = [
    'admName', 'admAddress', 'admContact', 'admTIN'
  ];

  constructor(private srchRec: searchRec,
    private genCL: genLandTaxCl,
    private route: Router,
    private gPos: getPosHolders,
    public matDialog: MatDialog) {}

  ngOnInit() {
    if(localStorage.getItem('auth')) {
      let obj: any = jwt_decode(localStorage.getItem('auth'));
      this.encoder1 = obj.name;
      this.gPos.getPosHoldersCl("TAX CLEARANCE").subscribe(res => {
        this.posHolders = res;
      })
			this.resetPage();
    } else {
      window.location.href = '/'
    }
  }

  param1: string = 'land';
  param2: string = 'pin';
  req: string;

  params1: selectOpt[] = [
    { value: 'land', viewVal: 'Land' },
    { value: 'building', viewVal: 'Building' },
  ];
  params2: selectOpt[] = [
    { value: 'pin', viewVal: 'PIN' },
    { value: 'arpNo', viewVal: 'ARP No.' },
    { value: 'name', viewVal: 'Name' },
  ];

  prps: selectOpt[] = [
    { value: 's1', viewVal: 'Cancellation/Registration of mortgage contract' },
    { value: 's2', viewVal: 'Transfer of ownership' },
    { value: 's3', viewVal: 'Bank Loan/Pag-ibig Loan' },
    { value: 's4', viewVal: 'Business Permit' },
    { value: 's5', viewVal: 'Others: For whatever legal purpose' },
  ]

	tableRowSelected(row: any) {
		ltTableInfOwner = [];
		ltTableInfAdmin = [];
		this.LTTableInfOwn = new MatTableDataSource(ltTableInfOwner);
		this.LTTableInfAdm = new MatTableDataSource(ltTableInfAdmin);
		this.selectedRow = [];
		this.selectedOwner = [];
		this.selectedAdmin = [];
		this.selectedRow.push(row);
		let ind: number;
		if(this.param1 == 'land') {
			ind = ltTableLs.indexOf(row);
		} else {
			ind = ltTableBldgLs.indexOf(row);
		}
		_.forEach(this.owner[ind], (arr: any) => {
			ltTableInfOwner.push({
				ownName: arr.first_name + ' ' + arr.middle_name + ' ' + arr.last_name,
				ownAddress: arr.address,
				ownContact: arr.contact_no,
				ownTIN: arr.TIN
			});
		});
		_.forEach(ltTableInfOwner, (arr: any) => {
			this.selectedOwner.push(arr);
		})
		_.forEach(ltTableInfAdmin, (arr: any) => {
			this.selectedAdmin.push(arr);
		})
		_.forEach(this.admin[ind], (arr: any) => {
			ltTableInfAdmin.push({
				admName: arr.first_name + ' ' + arr.middle_name + ' ' + arr.last_name,
				admAddress: arr.address,
				admContact: arr.contact_no,
				admTIN: arr.TIN
			});
		});
		this.LTTableInfOwn = new MatTableDataSource(ltTableInfOwner);
		this.LTTableInfAdm = new MatTableDataSource(ltTableInfAdmin);
		console.table(this.selectedRow);
		console.table(this.selectedOwner);
		console.table(this.selectedAdmin);
    this.clrnceBtn = true;
	}

  clicked: boolean = false;
  clckd = false;
  isVisible_spinner = false;
  clrnceBtn = false;
  search() {
    if(this.req == null || this.req == "" || this.req.trim() === "") {
      this.matDialog.open(ClearanceComponentErr, {width: '300px', height: '180px', panelClass: 'custom-dialog-container', disableClose: true, data: 'Empty input' });
    }
    else
    {
      if(this.param2 == 'pin' && this.req.match(/^[ A-Za-z_@./#&+]*$/) || this.param2 == 'arpNo' && this.req.match(/^[ A-Za-z_@./#&+]*$/))
      {
        this.matDialog.open(ClearanceComponentErr, {width: '300px', height: '180px', panelClass: 'custom-dialog-container', disableClose: true, data: 'Invalid input value' });
      }
      else if(this.param2 === 'name' && typeof this.req === "string" && !Number.isNaN(Number(this.req)) || this.req.match(/^[ _@./#&+-]*$/))
      {
        this.matDialog.open(ClearanceComponentErr, {width: '300px', height: '180px', panelClass: 'custom-dialog-container', disableClose: true, data: 'Invalid name' });
      }
      else
      {
        if(this.param2 == 'pin' || this.param2 == 'arpNo')
        {
          this.req = this.req.trim().split(' ').join('-');
        }
        this.clicked = false;
        this.isVisible_spinner = true;
        //var bg = document.getElementById('spinnerCon');
        //bg.style.display = 'none';
        ltTableLs = []
        ltTableBldgLs = []
        ltTableInfOwner = []
        ltTableInfAdmin = []
        this.LTTable = new MatTableDataSource(ltTableLs);
        this.LTTableInfOwn = new MatTableDataSource(ltTableInfOwner);
        this.LTTableInfAdm = new MatTableDataSource(ltTableInfAdmin);
        this.LTTableBldg = new MatTableDataSource(ltTableBldgLs);
        let reqdata: any = {
          SearchIn: this.param1,
          SearchBy: this.param2,
          info: this.req,
          sysCaller: 'LAND TAX'
        }
        this.srchRec.search(reqdata).subscribe(res => {
          if(res.success)
          {
          let resdata = res.data;
          this.faas = resdata.faas;
          this.owner = resdata.owner;
          this.admin = resdata.admin;
          console.table(resdata);
          if(this.faas.length > 0 || this.owner.length > 0 || this.admin.length > 0)
          {
            switch(this.param1) {
              case 'land':
                _.forEach(this.faas, (arr: any)=> {
                  ltTableLs.push({
                    id: arr.id,
                    arpNo: arr.ARPNo,
                    pin: arr.PIN,
                    surveyNo: arr.SurveyNo,
                    lotNo: arr.LotNo,
                    blockNo: arr.BlockNo,
                    streetNo: arr.StreetNo,
                    brgy: arr.Barangay,
                    subd: arr.Subdivision,
                    city: arr.City,
                    province: arr.Province,
                    class: arr.Class,
                    subclass: arr.SubClass,
                    area: arr.Area,
                    assessedVal: arr.AssessedValue,
                    stat: arr.Status
                  });
                });
                this.LTTable = new MatTableDataSource(ltTableLs);
                break;
              case 'building':
                _.forEach(this.faas, (arr: any) => {
                  ltTableBldgLs.push({
                    id: arr.id,
                    arpNo: arr.ARPNo,
                    pin: arr.PIN,
                    brgy: arr.Barangay,
                    subd: arr.Subdivision,
                    city: arr.City,
                    province: arr.Province,
                    kind: arr.Kind,
                    structType: arr.StructuralType,
                    bldgPermit: arr.BldgPermit,
                    dateConstr: arr.DateConstructed,
                    storey: arr.Storey,
                    actualUse: arr.ActualUse,
                    assessedVal: arr.AssessedValue
                  });
                });
                this.LTTableBldg = new MatTableDataSource(ltTableBldgLs);
                break;
            }
            this.clrnceBtn = false;
            this.srchClse();
          }
          else {
            this.matDialog.open(ClearanceComponentErr, {width: '300px', height: '180px', panelClass: 'custom-dialog-container', disableClose: true, data: 'Data not found' });
          }
          this.isVisible_spinner = false;
          this.clicked = false;
        }
        else {
          this.matDialog.open(ClearanceComponentErr, { width: '300px', height: '180px', panelClass: 'custom-dialog-container', disableClose: true, data: res.err });
        }
        });
      }
    }
  }

  srchIco() {
    var clientHeight = document.getElementById('search_db').clientHeight;
    var srchDb = document.getElementById("search_db");
    var clrDb = document.getElementById("clrnce_db");
    var bckgrnd = document.getElementById("bgOverlay");
    var wheight = document.body.clientHeight;
    var wWidth = document.body.clientWidth;
    var wheight50 = wheight / 2;
    var wWidth50 = wWidth / 2;
    var pos = -clientHeight - 1100;
    var id = setInterval(frame, 0.1);
    function frame() {
        if (pos >= Math.round(wheight50)) {
          clearInterval(id);
        } else {
          pos+=23;
          srchDb.style.top = pos / 3 + 'px';
          srchDb.style.display = "block";
          clrDb.style.display = 'none'
          bckgrnd.style.display = 'block';
        }
      }
  }

  srchClse() {
    var elem = document.getElementById("search_db");
    var bckgrnd = document.getElementById("bgOverlay");
    var clientHeight = document.getElementById('search_db').clientHeight;
    var id = setInterval(frame, 0.1);
    function frame() {
      //
      if(Math.round(clientHeight) < -1100) {
        bckgrnd.style.display = 'none';
        elem.style.display = 'none';
        clearInterval(id);
      } else {
        clientHeight-=23;
        elem.style.top = clientHeight / 3 + 'px';
      }
    }
  }

  genIco() {
    var clientHeight = document.getElementById('clrnce_db').clientHeight;
    var clrDb = document.getElementById("clrnce_db");
    var bckgrnd = document.getElementById("bgOverlay");
    var srchDb = document.getElementById("search_db");
    var wheight = document.body.clientHeight;
    var wWidth = document.body.clientWidth;
    var wheight50 = wheight / 2;
    var wWidth50 = wWidth / 2;
    var pos = -clientHeight - 1100;
    var id = setInterval(frame, 0.1);
    function frame() {
        if (pos >= 300) {
          clearInterval(id);
        } else {
          pos+=23;
          clrDb.style.top = pos / 5 + 'px';
          clrDb.style.display = "block";
          srchDb.style.display = 'none';
          bckgrnd.style.display = 'block';
          document.getElementById('input1').focus();
        }
      }
  }

  clseClrnce() {
    var clrDb = document.getElementById("clrnce_db");
    var bckgrnd = document.getElementById("bgOverlay");
    var clientHeight2 = document.getElementById('clrnce_db').clientHeight;
    var clientHeight = 300;
    var id = setInterval(frame, 0.1);
    function frame() {
      //
      if(Math.round(clientHeight) < -2200) {
        bckgrnd.style.display = 'none';
        clrDb.style.display = 'none';
        clearInterval(id);
      } else {
        clientHeight-=23;
        clrDb.style.top = clientHeight / 3 + 'px';
      }
    }
  }

  genCl() {
		this.clseClrnce();
    console.log(this.date);
    let data: lTaxClearance = {
      current_date: moment(new Date).format('MM-DD-YYYY'),
      owner_names: this.getOwners(),
      pin: this.selectedRow[0].pin,
      arp_no: this.selectedRow[0].arpNo,
      location: this.selectedRow[0].brgy + ', ' + this.selectedRow[0].city + ', ' + this.selectedRow[0].province,
      assessed_value: this.selectedRow[0].assessedVal,
      payment_reason: this.input1,
      total_amount: this.amount,
      cto_no: this.CTONo,
      dated: moment(new Date(this.dated)).format('MM/DD/YYYY') ,
      name_of_requestor: this.requestor,
      s1: (this.purpose == 's1') ? 'x' : ' ',
      s2: (this.purpose == 's2') ? 'x' : ' ',
      s3: (this.purpose == 's3') ? 'x' : ' ',
      s4: (this.purpose == 's4') ? 'x' : ' ',
      s5: (this.purpose == 's5') ? 'x' : ' ',
      verified_by: this.encoder1,
      by_name1: this.posHolders[0].holder_name,
      by_title1: this.posHolders[0].position_name,
      certification_fee: this.certfee,
      or_no: this.orNo,
      date: moment(new Date(this.date)).format('MM/DD/YYYY'),
      amount: this.amt,
      by_name2: this.posHolders[1].holder_name,
      by_title2: this.posHolders[1].position_name,
      remarks: this.remarks
    };
		console.log(data);
    this.genCL.loadFile(data).subscribe(res => {
			this.matDialog.open(DialogClearance, { data: { file: res.res }, width: '95%' });
		});
  }

  getOwners(): string {
    return (ltTableInfOwner.length > 1) ? ltTableInfOwner[0].ownName + ' ET AL' : ltTableInfOwner[0].ownName ;
  }

	resetPage() {
		ltTableLs = [];
		ltTableInfOwner = [];
		ltTableInfAdmin = [];
		ltTableBldgLs = [];
		this.LTTable = new MatTableDataSource(ltTableLs);
	  this.LTTableInfOwn = new MatTableDataSource(ltTableInfOwner);
	  this.LTTableInfAdm = new MatTableDataSource(ltTableInfAdmin);
	  this.LTTableBldg = new MatTableDataSource(ltTableBldgLs);
		this.input1 = '';
	  this.amount = '';
	  this.CTONo = '';
	  this.dated = '';
	  this.requestor = '';
	  this.purpose = '';
	  this.date = '';
	  this.certfee = '';
	  this.amt = '';
	  this.orNo = '';
	  this.remarks = '';
		this.selectedRow = [];
		this.selectedOwner = [];
		this.selectedAdmin = [];
		this.faas = null;
		this.owner = null;
		this.admin = null;
		this.param1 = 'land';
		this.param2 = 'pin';
		this.req = '';
		this.clrnceBtn = false;
	}

}

@Component({
  selector: 'app-dialog-clearance',
  templateUrl: 'dialog-clearance.html',
	styleUrls: ['./dialog-clearance.scss']
})


export class DialogClearance implements OnInit{

  docxSrc: any;

  constructor(
    public dialogRef: MatDialogRef<DialogClearance>,
    @Inject(MAT_DIALOG_DATA) public genData: any,
    private gC: getClFile
  ) {}

  ngOnInit() {
    // console.log(this.genData)
    this.docxSrc = 'data:application/pdf;base64,' + this.genData.file;
    // this.docxSrc = 'http://192.168.100.24:5000/api/get-file/land-tax/' + this.genData.filename + '.pdf';
    // this.docxSrc = 'data:document;base64,' + this.genData
  }
}

@Pipe({ name: 'docxPipe' })
export class DialogClearancePipe implements PipeTransform  {
  constructor(private sanitizer: DomSanitizer) { }
  transform(value: any) {
    return this.sanitizer.bypassSecurityTrustResourceUrl(value);
  }
}

@Component({
  selector: 'app-clearance.component.err',
  templateUrl: './clearance.component.err.html',
  styleUrls: ['./clearance.component.err.scss']
})
export class ClearanceComponentErr {
  msg: string = '\t ' + this.data;
	okBtn: boolean;
  constructor(private dialogRef: MatDialogRef<ClearanceComponentErr>,
    @Inject(MAT_DIALOG_DATA) public data: any) { }

  close() {
    this.dialogRef.close()
  }
}
