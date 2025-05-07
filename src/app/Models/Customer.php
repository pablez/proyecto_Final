<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

    /**
     * Indica si la ID es autoincremental.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'customer_name',
    ];

    /**
     * Define la relación "uno a muchos" con los pedidos (Order).
     * Un cliente puede tener muchos pedidos.
     */
    public function orders()
    {
        // El primer argumento es el modelo relacionado.
        // El segundo argumento es la clave foránea en la tabla 'orders' (customer_id).
        // El tercer argumento es la clave local en la tabla 'customers' (customer_id).
        return $this->hasMany(Order::class, 'customer_id', 'customer_id');
    }
}
