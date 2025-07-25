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
        Schema::table('invoice_item', function (Blueprint $table) {
            Schema::create('invoice_item', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoice');
                $table->foreignId('product_id')->constrained('product');
                $table->integer('qty');
                $table->float('price');
                $table->timestamps();
                $table->softDeletes();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_item', function (Blueprint $table) {
            //
        });
    }
};
