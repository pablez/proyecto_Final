<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'row_id';

    /**
     * Indica si la ID de la clave primaria es autoincremental.
     * Si 'row_id' es un identificador de tus datos fuente y no un autoincremental, ponlo en false.
     * Si 'row_id' DEBE ser autoincremental, la migración debería usar $table->id('row_id'); o similar.
     * Dado $table->integer('row_id')->primary();, es probable que no sea autoincremental por defecto.
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria.
     * Aunque es 'integer' en la migración, Laravel puede manejarlo bien, pero especificarlo no hace daño.
     * @var string
     */
    protected $keyType = 'int';


    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'row_id', // Si es asignable y no auto-generado. Si no, quitar de aquí.
        'order_id',
        'order_date',
        'ship_date',
        'ship_mode',
        'customer_id',
        'segment',
        'postal_code',
        'product_id',
        'sales',
        'quantity',
        'discount',
        'profit',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'date',
        'ship_date' => 'date',
        'sales' => 'decimal:4', // Preserva 4 decimales al castear
        'discount' => 'decimal:2',// Preserva 2 decimales
        'profit' => 'decimal:4',  // Preserva 4 decimales
    ];

    /**
     * Define la relación "pertenece a" con el modelo Customer.
     * Cada línea de pedido pertenece a un cliente.
     */
    public function customer()
    {
        // El primer argumento es el modelo relacionado.
        // El segundo argumento es la clave foránea en esta tabla ('orders').
        // El tercer argumento es la clave primaria en la tabla 'customers'.
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Define la relación "pertenece a" con el modelo Product.
     * Cada línea de pedido pertenece a un producto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Define la relación "pertenece a" con el modelo Location.
     * Cada línea de pedido está asociada a una ubicación (a través de postal_code).
     */
    public function location()
    {
        // El primer argumento es el modelo relacionado.
        // El segundo argumento es la clave foránea en esta tabla ('orders.postal_code').
        // El tercer argumento es la clave propietaria en la tabla 'locations' ('locations.postal_code').
        return $this->belongsTo(Location::class, 'postal_code', 'postal_code');
    }
}
