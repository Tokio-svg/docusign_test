<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Docusign;
use App\Models\Envelope;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use DocuSign\eSign\Configuration;
// use DocuSign\eSign\Api\EnvelopesApi;
// use DocuSign\eSign\Client\ApiClient;

class DocusignController extends Controller
{
    // -----------------------------------------------
    // ホーム画面
    // -----------------------------------------------
    public function index() {
        $params = [
            'account_id' => null,
            'account_name' => null,
            'base_url' => null,
            'access_token' => null,
            'expires_at' => null,
            'file_path' => null
        ];

        $docusign = Docusign::first();
        if ($docusign) {
            $params['account_id'] = $docusign->account_id;
            $params['account_name'] = $docusign->account_name;
            $params['base_url'] = $docusign->base_url;
            $params['access_token'] = $docusign->access_token;
            $params['expires_at'] = $docusign->expires_at;
        }

        return view('index', $params);
    }

    // -----------------------------------------------
    // アクセストークン取得後のリダイレクト先
    // -----------------------------------------------
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function docusign(Request $request) {
        try {
            DB::beginTransaction();
            // 認証コードを取得
            $code = $request->code;
            Log::debug($request->code);

            // アクセストークンを取得
            $base_64_string = base64_encode(config('app.docusign_integration_key') . ':' . config('app.docusign_secret_key'));
            $header_authorization = 'Basic ' . $base_64_string;
            $response = Http::withHeaders([
                'Authorization' => $header_authorization
            ])->post('https://account-d.docusign.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
            Log::debug($response);
            $access_token = $response['access_token'];
            $refresh_token = $response['refresh_token'];
            $expires_in = $response['expires_in'];
            $expires_at = Carbon::now()->addSeconds($expires_in);

            // ベースURIを取得
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://account-d.docusign.com/oauth/userinfo');
            Log::debug($response);
            $account_id = $response['accounts'][0]['account_id'];
            $account_name = $response['accounts'][0]['account_name'];
            $base_url = $response['accounts'][0]['base_uri'];

            $docusign_item = Docusign::first();
            if (!$docusign_item) {
                Docusign::create([
                    'account_id' => $account_id,
                    'account_name' => $account_name,
                    'base_url' => $base_url,
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_at' => $expires_at
                ]);
            } else {
                $docusign_item->update([
                    'account_id' => $account_id,
                    'account_name' => $account_name,
                    'base_url' => $base_url,
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_at' => $expires_at
                ]);
            }

            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

        return view('docusign', [
            'access_token' => $access_token,
            'account' => [
                'account_id' => $account_id,
                'account_name' => $account_name,
                'base_url' => $base_url
            ]
        ]);
    }

    // -----------------------------------------------
    // 連携解除
    // -----------------------------------------------
    public function release() {
        try {
            DB::beginTransaction();
            $docusign = Docusign::first();
            if ($docusign) {
                $docusign->delete();
            }
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
        return redirect('/');
    }

    // -----------------------------------------------
    // ユーザー一覧
    // -----------------------------------------------
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function getUsers() {

        try {
            $docusign_item = Docusign::first();
            $access_token = $docusign_item->access_token;
            $account_id = $docusign_item->account_id;
            $base_url = $docusign_item->base_url;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get($base_url . '/restapi/v2.1/accounts/' . $account_id . '/users');
        } catch(\Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return view('users', [
            'users' => $response['users']
        ]);
    }

    // -----------------------------------------------
    // 電子署名依頼処理
    // -----------------------------------------------
    public function requestSignPage() {

        return view('request_sign');
    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function sendRequestSign(Request $request) {
        try {
            DB::beginTransaction();

            // パラメータ取得
            $signer_email = $request->signer_email;
            $cc_email = $request->cc_email;
            $docsFilePath = public_path('doc/fudousann_chintaikeiyaku_sample.pdf');
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $file = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));
            $file_base64 = base64_encode($file);
            $docusign_item = Docusign::first();
            $access_token = $docusign_item->access_token;
            $account_id = $docusign_item->account_id;
            $base_url = $docusign_item->base_url;


            // エンベロープ定義を作成
            $request_data = [
                'emailSubject' => 'Please sign this document',
                'documents' => [
                    [
                        'documentBase64' => $file_base64,
                        'name' => 'Example Document File',
                        'fileExtension' => 'pdf',
                        'documentId' => '1'
                    ]
                ],
                'recipients' => [
                    'carbonCopies' => [
                        [
                            'email' => $cc_email,
                            'name' => '{CC送信先の名前}',
                            'recipientId' => '2',
                            'routingOrder' => '2'
                        ]
                    ],
                    'signers' => [
                        [
                            'email' => $signer_email,
                            'name' => '{署名者の名前}',
                            'recipientId' => '1',
                            'routingOrder' => '1',
                            'tabs' => [ // サインする場所を指定
                                'signHereTabs' => [
                                    [
                                        'anchor_string' => '（名称）',
                                        'anchor_units' => 'pixels',
                                        'anchor_y_offset' => '10',
                                        'anchor_x_offset' => '20'
                                    ],
                                    // [
                                    //     'anchor_string' => '（名称）',
                                    //     'anchor_units' => 'pixels',
                                    //     'anchor_y_offset' => '40',
                                    //     'anchor_x_offset' => '40'
                                    // ]
                                ]
                            ]
                        ]
                    ]
                ],
                'status' => 'sent'
            ];

            // 封筒を作成して送信
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ])->post($base_url . '/restapi/v2.1/accounts/' . $account_id . '/envelopes', $request_data);
            Log::debug($response);

            // Envelopeレコードを作成
            Envelope::create([
                'envelope_id' => $response['envelopeId'],
                'uri' => $response['uri'],
                'status_date_time' => $response['statusDateTime'],
                'status' => $response['status']
            ]);
            DB::commit();
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

        return redirect('/envelopes');
    }

    // -----------------------------------------------
    // 封筒画面
    // -----------------------------------------------
    // 封筒一覧
    public function envelopeList() {
        $envelopes = Envelope::all();

        return view('envelopes', [
            'envelopes' => $envelopes
        ]);
    }

    // 個別の封筒情報(APIから取得)
    public function envelope($id) {
        try {
            $envelope = Envelope::find($id);
            $docusign_item = Docusign::first();
            $access_token = $docusign_item->access_token;
            $account_id = $docusign_item->account_id;
            $base_url = $docusign_item->base_url;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get($base_url . '/restapi/v2.1/accounts/' . $account_id . '/envelopes/' . $envelope->envelope_id);
            Log::debug($response);
            if ($response->ok()) {
                $params = [
                    'envelopeId' => $response['envelopeId'],
                    'status' => $response['status'],
                    'emailSubject' => $response['emailSubject'],
                    'signingLocation' => $response['signingLocation'],
                    'enableWetSign' => $response['enableWetSign'],
                    'allowMarkup' => $response['allowMarkup'],
                    'allowReassign' => $response['allowReassign'],
                    'createdDateTime' => $response['createdDateTime'],
                    'statusChangedDateTime' => $response['statusChangedDateTime'],
                    'expireDateTime' => $response['expireDateTime'],
                    'envelopeUri' => $response['envelopeUri'],
                ];
            } else {
                $params = [
                    'error' => true
                ];
            }
        } catch(\Throwable $e) {
            Log::error($e);
            throw $e;
        }

        return view('envelope', [
            'params' => $params
        ]);
    }

    // 封筒のドキュメントダウンロード
    public function downloadDocuments($envelopeId) {
        $docusign_item = Docusign::first();
        $access_token = $docusign_item->access_token;
        $account_id = $docusign_item->account_id;
        $base_url = $docusign_item->base_url;
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $access_token
        ])->get($base_url . '/restapi/v2.1/accounts/' . $account_id . '/envelopes/' . $envelopeId . '/documents/archive');

        $mime_type = 'application/zip';

        $headers = [
            'Content-Type' => $mime_type,
            'Content-Disposition' => 'attachment; filename="'. $envelopeId . '.zip' . '"'
        ];

        return response()->make($response, 200, $headers);
    }

}
