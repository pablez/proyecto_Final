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
        Schema::create('orders', function (Blueprint $table) {
            // Columnas
            $table->integer('row_id')->primary(); // Mantenemos tu PK, pero en snake_case
            $table->string('order_id', 25)->index(); // Indexado
            $table->date('order_date');
            $table->date('ship_date');
            $table->string('ship_mode', 15);
            $table->string('customer_id', 50); // Longitud coincide con customers.customer_id
            $table->string('segment', 15);
            $table->string('postal_code', 20)->nullable(); // Longitud coincide con locations.postal_code
            $table->string('product_id', 20); // Longitud coincide con products.product_id
            $table->decimal('sales', 10, 4);
            $table->integer('quantity');
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('profit', 10, 4);
            $table->timestamps(); // Añadido created_at, updated_at
        
            // Llaves Foráneas
            $table->foreign('customer_id')
                  ->references('customer_id')->on('customers')
                  ->onDelete('restrict');
        
            // Nota: La FK para postal_code ahora referencia locations.id (la PK estándar)
            // O si prefieres referenciar la columna postal_code directamente (menos común si no es PK):
            // $table->foreign('postal_code')
            //       ->references('postal_code')->on('locations') // Asegúrate que postal_code esté indexado en locations
            //       ->onDelete('set null'); 
            // Vamos a mantener la referencia a postal_code como en el ejemplo anterior,
            // asumiendo que aunque no sea PK en locations, sí quieres esa relación directa.
            $table->foreign('postal_code')
                  ->references('postal_code')->on('locations') 
                  ->onDelete('set null'); // postal_code es nullable aquí
        
            $table->foreign('product_id')
                  ->references('product_id')->on('products')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
