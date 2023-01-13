<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docusigns', function (Blueprint $table) {
            $table->id();
            $table->string('account_id');
            $table->string('account_name');
            $table->text('access_token');
            $table->text('refresh_token');
            $table->string('base_url');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('docusigns');
    }
};
