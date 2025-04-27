<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('news_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_site_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('short_story');
            $table->longText('full_story');
            $table->string('link');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('news_items');
    }
};
