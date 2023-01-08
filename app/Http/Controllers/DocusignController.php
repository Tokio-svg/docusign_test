<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Docusign;
use Illuminate\Support\Facades\DB;

class DocusignController extends Controller
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function index() {
        $docusign = Docusign::first();
        $params = [
            'account_id' => null,
            'account_name' => null,
            'base_url' => null,
            'access_token' => null
        ];
        if ($docusign) {
            $params['account_id'] = $docusign->account_id;
            $params['account_name'] = $docusign->account_name;
            $params['base_url'] = $docusign->base_url;
            $params['access_token'] = $docusign->access_token;
        }
        return view('index', $params);
    }

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
            $header_authorization = 'Basic ' . base64_encode('2c52bf7a-0c4e-4372-9fdf-1e2e6b35e314:b7c8049d-641a-4380-868e-b987ae591a38');
            $response = Http::withHeaders([
                'Authorization' => $header_authorization
            ])->post('https://account-d.docusign.com/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);
            Log::debug($response);
            $access_token = $response['access_token'];

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
                    'access_token' => $access_token
                ]);
            } else {
                $docusign_item->update([
                    'account_id' => $account_id,
                    'account_name' => $account_name,
                    'base_url' => $base_url,
                    'access_token' => $access_token
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

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function getBrands() {

        try {
            $docusign_info = Docusign::first();
            $access_token = $docusign_info->access_token;
            $account_id = $docusign_info->account_id;
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $access_token
            ])->get('https://demo.docusign.net/restapi/v2.1/accounts/' . $account_id . '/users');
            Log::debug($response);
        } catch(\Throwable $e) {
            Log::error($e);
            throw $e;
        }
        return redirect('/');
    }
}
