<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Docusign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class refreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refreshToken';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期限切れ間近のアクセストークンを更新します';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // 30分後以内に期限が切れるレコードを取得
            $limit_time_line = Carbon::now()->addMinutes(30);
            $require_refresh_items = Docusign::where('expires_at', '<', $limit_time_line)->get();

            if ($require_refresh_items) {
                $base_64_string = base64_encode(config('app.docusign_integration_key') . ':' . config('app.docusign_secret_key'));
                $header_authorization = 'Basic ' . $base_64_string;
                // 期限切れが近い全てのトークンを更新
                $require_refresh_items->each(function($docusign_item) use($header_authorization) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => $header_authorization,
                        ])->post(config('app.docusign_oauth_url') . '/token', [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $docusign_item->refresh_token
                        ]);

                        $access_token = $response['access_token'];
                        $expires_in = $response['expires_in'];
                        $expires_at = Carbon::now()->addSeconds($expires_in);
                        $refresh_token = $response['refresh_token'];

                        $docusign_item->update([
                            'access_token' => $access_token,
                            'refresh_token' => $refresh_token,
                            'expires_at' => $expires_at
                        ]);
                    } catch(\Throwable $e) {
                        Log::error($e);
                        // 更新時にエラーが発生したらレコードを削除して連携解除
                        $docusign_item->delete();
                    }
                });
            }
            Log::info(count($require_refresh_items) . '件のアクセストークンを更新しました');
            return Command::SUCCESS;
        } catch(\Throwable $e) {
            Log::error($e);
            return Command::FAILURE;
        }
    }
}
