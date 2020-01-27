<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::match(['post'], 'api/login', 'LoginCtrl@login')->middleware('cors');



Route::group(['middleware' => ['cors']], function() {
		// POST requests
    Route::post('/login', 'LoginCtrl@login');
    Route::post('/register', 'RegisterCtrl@register');
    Route::post('/get-file/land-tax', 'GetClearance@getFile');
		Route::post('/check-pin-land', 'CheckPIN@checkLand');
    Route::post('/check-pin-bldg', 'CheckPIN@checkBldg');
		Route::post('/set-ref-num', 'SetRefNum@set');
		Route::post('/gen-taxdec-land', 'GetTaxDecLand@getFile');
    Route::post('/gen-taxdec-bldg', 'GetTaxDecBldg@getFile');
		Route::post('/gen-land-faas', 'GenFaas@genLandFaas');
		Route::post('/gen-bldg-faas', 'GenFaas@genBldgFaas');
		Route::post('/gen-clearance', 'SaveClearance@save');
		// GET requests
    Route::get('/test', 'TestCtrl@testToken');
    Route::get('/search-faas-record/{sysCaller}/{searchIn}/{searchBy}/{info}', 'SearchRecord@search');
    Route::get('/position-holders/{sysCaller}', 'PosHolders@getHolders');
    Route::get('/get-faas/land/{id}', 'GetLandFaas@getInfo');
    Route::get('/get-faas/bldg/{id}', 'GetBldgFaas@getInfo');
    Route::get('/segregation/get-data/{sysCaller}/{searchBy}/{info}', 'SegregationCtrl@searchRecord');
		Route::get('/pending/segregation/{name}', 'GetPendingTrns@getPendingSegregation');
		Route::get('/pending/consolidation/{name}', 'GetPendingTrns@getPendingConsolidation');
		Route::get('/pending/subdivision/{name}', 'GetPendingTrns@getPendingSubdivision');
		Route::get('/land/market-values', 'GetMarketValues@getVal');
		Route::get('/bldg/kinds', 'BldgValuesCtrl@getBldgKind');
		Route::get('/sample-pdf', 'GetTaxDec@getFile');
		Route::get('/get-brgys-subd', 'BrgysSubd@get');
		Route::get('/get-bldg-struct-mat', 'GetBldgStructMat@getLs');
		Route::get('/get-bldg-heights', 'GetBldgStHeight@get');
    Route::get('/get-bldg-incr', 'GetBldgAddItems@getItems');
    Route::get('/get-bldg-vals', 'GetAllBldgDefVal@getVals');
		// PUT requests
		Route::put('/land-asmt/add', 'AssessLand@addLand');
		Route::put('/land-asmt/update', 'AssessLand@updateLand');
		Route::put('/bldg-asmt/add', 'AssessBldg@addBldg');
		Route::put('/bldg-asmt/update', 'AssessBldg@updateBldg');
		Route::put('/land-reasmt/reassess', 'ReassessmentCtrl@reassessLand');
		Route::put('/bldg-reasmt/reassess', 'ReassessmentCtrl@reassessBldg');
});
