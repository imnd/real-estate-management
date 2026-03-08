<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('premise_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('premise_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->string('old_value');
            $table->string('new_value');
            $table->string('field');
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premise_history');
    }
};
