<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Docusign;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
    protected $description = 'アクセストークンを更新します';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $base_64_string = base64_encode(config('app.docusign_integration_key') . ':' . config('app.docusign_secret_key'));
            $header_authorization = 'Basic ' . $base_64_string;
            $docusign_item = Docusign::first();
            if ($docusign_item) {
                $response = Http::withHeaders([
                    'Authorization' => $header_authorization,
                ])->post('https://account-d.docusign.com/oauth/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $docusign_item->refresh_token
                ]);

                Log::debug($response);
                $access_token = $response['access_token'];
                $expires_in = $response['expires_in'];
                $expires_at = Carbon::now()->addSeconds($expires_in);
                $refresh_token = $response['refresh_token'];

                $docusign_item->update([
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_at' => $expires_at
                ]);
                DB::commit();
            }
            return Command::SUCCESS;
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::error($e);
            Docusign::first()->delete();
            return Command::FAILURE;
        }
    }
}
