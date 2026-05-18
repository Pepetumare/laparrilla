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
        Schema::create('detalle_cierres_diarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cierre_diario_id')
                ->constrained('cierres_diarios')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->decimal('stock_disponible_kg', 10, 2);
            $table->decimal('kilos_restantes_kg', 10, 2);
            $table->decimal('kilos_vendidos_estimados', 10, 2);

            $table->text('observacion')->nullable();

            $table->timestamps();

            $table->unique(['cierre_diario_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cierre_diarios');
    }
};
