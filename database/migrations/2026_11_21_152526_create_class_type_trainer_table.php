<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_type_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_type_id')->constrained('class_types')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['trainer_id', 'class_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_type_trainer');
    }
};