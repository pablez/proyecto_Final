<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * La clave primaria asociada con la tabla.
     * Laravel asume 'id' por defecto, por lo que no es estrictamente necesario declararlo.
     *
     * @var string
     */
    // protected $primaryKey = 'id'; // Opcional, ya que 'id' es el predeterminado

    /**
     * Indica si la ID es autoincremental.
     * Laravel asume true por defecto para claves primarias llamadas 'id'.
     *
     * @var bool
     */
    // public $incrementing = true; // Opcional, ya que es el predeterminado para 'id'

    /**
     * El tipo de dato de la clave primaria.
     * Laravel asume 'int' por defecto para 'id'.
     *
     * @var string
     */
    // protected $keyType = 'int'; // Opcional, ya que es el predeterminado para 'id'

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'postal_code',
        'city',
        'state',
        'region',
        'country_region',
    ];

    /**
     * Define la relación "uno a muchos" con las líneas de pedido (Order).
     * Una ubicación (basada en su postal_code único) puede tener muchas líneas de pedido.
     */
    public function orders()
    {
        // El primer argumento es el modelo relacionado (Order).
        // El segundo argumento es la clave foránea en la tabla 'orders' (postal_code).
        // El tercer argumento es la clave local en la tabla 'locations' a la que se refiere la FK (postal_code).
        // Esto asume que 'locations.postal_code' es único y es la columna que 'orders.postal_code' referencia.
        return $this->hasMany(Order::class, 'postal_code', 'postal_code');
    }
}
