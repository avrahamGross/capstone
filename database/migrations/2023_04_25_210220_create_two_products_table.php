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
        Schema::create('two_products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('apple')->default(0);
            $table->tinyInteger('bread')->default(0);
            $table->tinyInteger('butter')->default(0);
            $table->tinyInteger('cheese')->default(0);
            $table->tinyInteger('corn')->default(0);
            $table->tinyInteger('dill')->default(0);
            $table->tinyInteger('eggs')->default(0);
            $table->tinyInteger('ice_cream')->default(0);
            $table->tinyInteger('kidney_bean')->default(0);
            $table->tinyInteger('milk')->default(0);
            $table->tinyInteger('nutmeg')->default(0);
            $table->tinyInteger('onion')->default(0);
            $table->tinyInteger('sugar')->default(0);
            $table->tinyInteger('unicorn')->default(0);
            $table->tinyInteger('yogurt')->default(0);
            $table->tinyInteger('chocolate')->default(0);
            $table->decimal('support', 5, 3, true);
            $table->decimal('confidence', 5, 3, true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_products');
    }
};
