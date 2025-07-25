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
            Schema::create('position', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branch');
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('position', function (Blueprint $table) {
          Schema::dropIfExists('position');
        });
    }
};
