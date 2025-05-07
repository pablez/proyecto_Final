<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'product_id';

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
        'product_id',
        'category',
        'sub_category',
        'product_name',
    ];

    /**
     * Define la relación "uno a muchos" con las líneas de pedido (Order).
     * Un producto puede aparecer en muchas líneas de pedido.
     */
    public function orders()
    {
        // El primer argumento es el modelo relacionado.
        // El segundo argumento es la clave foránea en la tabla 'orders' (product_id).
        // El tercer argumento es la clave local en la tabla 'products' (product_id).
        return $this->hasMany(Order::class, 'product_id', 'product_id');
    }
}
