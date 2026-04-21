<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_responses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('query_id')
                ->constrained('queries')
                ->cascadeOnDelete();

            $table->json('response_json');

            $table->integer('schema_version')->default(1);
            $table->string('model_used')->nullable();
            $table->integer('tokens_used')->nullable();

            $table->integer('prompt_version')->default(1);

            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index('query_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_responses');
    }
};
