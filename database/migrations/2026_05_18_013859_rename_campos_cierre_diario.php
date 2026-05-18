<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_cierres_diarios', function (Blueprint $table) {
            $table->renameColumn('kilos_restantes_kg', 'kilos_vendidos_kg');
            $table->renameColumn('kilos_vendidos_estimados', 'stock_restante_calculado_kg');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_cierres_diarios', function (Blueprint $table) {
            $table->renameColumn('kilos_vendidos_kg', 'kilos_restantes_kg');
            $table->renameColumn('stock_restante_calculado_kg', 'kilos_vendidos_estimados');
        });
    }
};
