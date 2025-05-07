<?php

namespace App\Http\Controllers; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Location; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function pregunta1()
    {
        $shipMode = 'First Class';
        $totalPedidosUnicos = Order::where('ship_mode', $shipMode)
                                    ->distinct()
                                    ->count('order_id');

        return response()->json([
            'descripcion' => "Número total de pedidos únicos enviados (total o parcialmente) utilizando el modo de envío especificado.",
            'modo_de_envio_consultado' => $shipMode,
            'total_pedidos_unicos' => $totalPedidosUnicos,
        ]);
    }

    public function pregunta2()
    {
        $customerId = 'DP-13000';
        $count = Order::where('customer_id', $customerId)
                        ->distinct('order_id')
                        ->count('order_id');
        
        return response()->json([
            'descripcion' => "Número total de pedidos únicos realizados por el cliente con ID '{$customerId}'",
            'customer_id' => $customerId,
            'total_pedidos_unicos' => $count,
        ]);
    }

    public function pregunta3()
    {
        $rentabilidadPorProducto = Order::select('product_id', DB::raw('SUM(profit) as total_profit'))
            ->groupBy('product_id');

        $productosMasRentables = Product::select(
                'products.category',
                'products.product_id',
                'products.product_name',
                'rentabilidad.total_profit'
            )
            ->joinSub($rentabilidadPorProducto, 'rentabilidad', function ($join) {
                $join->on('products.product_id', '=', 'rentabilidad.product_id');
            })
            ->orderBy('products.category')
            ->orderByDesc('rentabilidad.total_profit')
            ->get()
            ->groupBy('category')
            ->map(function ($group) {
                return $group->first();
            });
            
        return response()->json([
            'descripcion' => "Producto más rentable en cada categoría",
            'productos_mas_rentables_por_categoria' => $productosMasRentables,
        ]);
    }

    public function pregunta4(Request $request)
    {
        $fechaFin = Carbon::now();
        $fechaInicio = Carbon::now()->subYear();
        $limiteClientes = $request->input('limit', 10);

        $clientesConMasCompras = Order::select(
                'orders.customer_id', 
                'customers.customer_name',
                DB::raw('COUNT(DISTINCT orders.order_id) as total_pedidos_unicos')
            )
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->whereBetween('orders.order_date', [$fechaInicio, $fechaFin])
            ->groupBy('orders.customer_id', 'customers.customer_name')
            ->orderByDesc('total_pedidos_unicos')
            ->take($limiteClientes)
            ->get();
        
        return response()->json([
            'descripcion' => "Clientes que han realizado más compras (pedidos únicos) en el último año (desde {$fechaInicio->toDateString()} hasta {$fechaFin->toDateString()})",
            'top_clientes' => $clientesConMasCompras,
        ]);
    }

    public function pregunta5()
    {
        $promediosPorSegmento = Order::select('segment', 
                DB::raw('AVG(sales) as promedio_ventas'),
                DB::raw('AVG(profit) as promedio_ganancias')
            )
            ->groupBy('segment')
            ->get();
        
        return response()->json([
            'descripcion' => "Promedio de ventas y ganancias por segmento de clientes.",
            'promedios_por_segmento' => $promediosPorSegmento,
        ]);
    }

    public function pregunta6()
    {
        $cantidadVendidaPorProducto = Order::select('product_id', DB::raw('SUM(quantity) as total_quantity_sold'))
            ->groupBy('product_id');

        $productosMasVendidos = Product::select(
                'products.sub_category', 
                'products.product_id', 
                'products.product_name', 
                'cantidad_vendida.total_quantity_sold'
            )
            ->joinSub($cantidadVendidaPorProducto, 'cantidad_vendida', function ($join) {
                $join->on('products.product_id', '=', 'cantidad_vendida.product_id');
            })
            ->orderBy('products.sub_category')
            ->orderByDesc('cantidad_vendida.total_quantity_sold')
            ->get()
            ->groupBy('sub_category')
            ->map(function ($group) {
                return $group->first(); 
            });

        return response()->json([
            'descripcion' => "Productos más vendidos (por cantidad total) en cada subcategoría.",
            'productos_mas_vendidos_por_subcategoria' => $productosMasVendidos,
        ]);
    }

    public function pregunta7()
    {
        $tendencia = Order::select(
                'products.category',
                DB::raw("YEAR(orders.order_date) as anio"),
                DB::raw("MONTH(orders.order_date) as mes"),
                DB::raw("SUM(orders.sales) as ventas_totales"),
                DB::raw("SUM(orders.profit) as ganancias_totales")
            )
            ->join('products', 'orders.product_id', '=', 'products.product_id')
            ->groupBy('products.category', 'anio', 'mes')
            ->orderBy('products.category')
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        return response()->json([
            'descripcion' => "Tendencia de ventas y ganancias por categoría de productos a lo largo del tiempo (año/mes).",
            'tendencia_por_categoria_tiempo' => $tendencia,
        ]);
    }

    public function pregunta8()
    {
        $rentabilidadPorModoEnvio = Order::select('ship_mode',
                DB::raw('AVG(profit) as promedio_ganancias'),
                DB::raw('SUM(profit) as ganancia_total'),
                DB::raw('COUNT(DISTINCT order_id) as total_pedidos_unicos')
            )
            ->groupBy('ship_mode')
            ->orderByDesc('promedio_ganancias')
            ->get();
        
        return response()->json([
            'descripcion' => "Relación entre modo de envío y rentabilidad (promedio de ganancia).",
            'rentabilidad_por_modo_envio' => $rentabilidadPorModoEnvio,
        ]);
    }

    public function pregunta9()
    {

        $totalCategoriasDistintas = Product::distinct('category')->count('category');

        $clientesTodasCategorias = Order::select('orders.customer_id', 'customers.customer_name')
            ->join('products', 'orders.product_id', '=', 'products.product_id')
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->selectRaw('orders.customer_id, customers.customer_name, COUNT(DISTINCT products.category) as categorias_compradas')
            ->groupBy('orders.customer_id', 'customers.customer_name')
            ->having('categorias_compradas', '=', $totalCategoriasDistintas)
            ->get();

        return response()->json([
            'descripcion' => "Clientes que han comprado productos de todas las categorías disponibles.",
            'total_categorias_distintas_disponibles' => $totalCategoriasDistintas,
            'clientes_todas_categorias' => $clientesTodasCategorias,
        ]);
    }

    public function pregunta10()
    {
        $distribucion = Order::select(
                'locations.region', 
                'orders.segment',
                DB::raw('SUM(orders.sales) as ventas_totales'),
                DB::raw('SUM(orders.profit) as ganancias_totales'),
                DB::raw('COUNT(DISTINCT orders.order_id) as total_pedidos_unicos')
            )
            ->join('locations', 'orders.postal_code', '=', 'locations.postal_code') 
            ->groupBy('locations.region', 'orders.segment')
            ->orderBy('locations.region')
            ->orderBy('orders.segment')
            ->get();
        
        return response()->json([
            'descripcion' => "Distribución de ventas y ganancias por región y segmento de clientes.",
            'distribucion_ventas_ganancias' => $distribucion,
        ]);
    }

    public function pregunta11()
    {
        $years = [2020, 2021, 2022, 2023];
        $resultadoFinal = [];

        foreach ($years as $year) {
            $clientesPorAnio = Order::select(
                    'customer_id',
                    DB::raw("YEAR(order_date) as anio_pedido"),
                    DB::raw('COUNT(DISTINCT order_id) as total_pedidos_cliente_anio')
                )
                ->whereYear('order_date', $year)
                ->groupBy('customer_id', DB::raw("YEAR(order_date)")) 
                ->having('total_pedidos_cliente_anio', '>', 10)
                ->orderBy('customer_id')
                ->get();
            
            if ($clientesPorAnio->isNotEmpty()) {
                $resultadoFinal[$year] = $clientesPorAnio;
            }
        }
        return response()->json([
            'descripcion' => "Clientes con más de 10 pedidos únicos en un año específico (2020-2023).",
            'clientes_por_anio_mas_de_10_pedidos' => $resultadoFinal,
        ]);
    }

    public function examen() 
    {
        $minimo = 3;

        $clientes = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
            ->select(
                'orders.customer_id',
                'customers.customer_name as firstname' 
            )
            ->groupBy('orders.customer_id', 'customers.customer_name')
            ->havingRaw('COUNT(DISTINCT orders.ship_mode) >= ?', [$minimo])
            ->orderBy('customers.customer_name')
            ->get();

        return response()->json([
            'descripcion' => "Clientes {$minimo} de pedidos",
            'criterio_modos_envio_distintos' => $minimo,
            'clientes_encontrados' => $clientes,
        ]);
    }
}
