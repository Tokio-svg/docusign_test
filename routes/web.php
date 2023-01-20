<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocusignController;

Route::get('/', [DocusignController::class, 'index']);
// 統合後画面
Route::get('/docusign', [DocusignController::class, 'docusign']);
// 連携解除
Route::post('/release', [DocusignController::class, 'release']);
// ユーザー一覧
Route::get('/getUsers', [DocusignController::class, 'getUsers']);
// ファイルアップロード
Route::post('/upload', [DocusignController::class, 'upload']);
// 電子署名依頼
Route::get('/requestSign', [DocusignController::class, 'requestSignPage']);
Route::post('/requestSign', [DocusignController::class, 'sendRequestSign']);
// 封筒一覧
Route::get('/envelopes', [DocusignController::class, 'envelopeList']);
Route::post('/deleteEnvelope', [DocusignController::class, 'deleteEnvelope']);
// 個別の封筒情報
Route::get('/envelope/{id}', [DocusignController::class, 'envelope']);
// 封筒のドキュメントダウンロード
Route::get('/downloadDocuments/{envelopeId}', [DocusignController::class, 'downloadDocuments']);
// トークン更新
Route::get('/refreshToken', [DocusignController::class, 'refreshToken']);