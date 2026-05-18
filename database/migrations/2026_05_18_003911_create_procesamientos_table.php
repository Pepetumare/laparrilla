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
        Schema::create('procesamientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ingreso_mercaderia_id')
                ->constrained('ingresos_mercaderia')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->foreignId('sucursal_id')
                ->nullable()
                ->constrained('sucursales')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha_procesamiento');

            $table->decimal('peso_inicial_kg', 10, 2);
            $table->decimal('peso_util_kg', 10, 2)->default(0);
            $table->decimal('merma_kg', 10, 2)->default(0);

            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesamientos');
    }
};
