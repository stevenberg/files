<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path')->unique();
            $table->string('url_key');
            $table->boolean('implicitly_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('name');
            $table->index('implicitly_deleted');
            $table->unique(['folder_id', 'name']);
            $table->unique(['folder_id', 'url_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
