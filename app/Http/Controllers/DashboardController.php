<?php

namespace App\Http\Controllers;

use App\Models\Maintenance_Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property;

class DashboardController extends Controller
{
    public function index()
    {

        $requests = Maintenance_Request::orderBy('report_date', 'desc')->take(10)->get();

        // Datos de ingresos totales por mes
        $monthlyIncome = DB::table('payment_histories')
            ->select(
                DB::raw('EXTRACT(YEAR FROM payment_date) as year'),
                DB::raw('EXTRACT(MONTH FROM payment_date) as month'),
                DB::raw('SUM(amount_paid) as total_income')
            )
            ->groupBy(DB::raw('EXTRACT(YEAR FROM payment_date)'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM payment_date)'))
            ->orderByRaw('EXTRACT(YEAR FROM payment_date) ASC')
            ->orderByRaw('EXTRACT(MONTH FROM payment_date) ASC')
            ->get();

        $occupancy = DB::table('properties')
            ->select(
                DB::raw("COUNT(CASE WHEN availability = 'Available' THEN 1 END) as available"),
                DB::raw("COUNT(CASE WHEN availability = 'Not Available' THEN 1 END) as not_available"),
                DB::raw("EXTRACT(MONTH FROM updated_at) as month")
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $activeProperties = DB::table('properties')->count();

        // Tasa de ocupación
        $occupiedUnits = DB::table('properties')->where('availability', 'Not Available')->count();
        $occupancyRate = $activeProperties > 0
            ? round(($occupiedUnits / $activeProperties) * 100, 2)
            : 0;

        // Ingresos estimados del mes actual
        $currentMonthIncome = DB::table('payment_histories')
            ->whereMonth('payment_date', date('m'))
            ->whereYear('payment_date', date('Y'))
            ->sum('amount_paid');

        // Pagos pendientes
        $pendingPayments = DB::table('payment_histories')
            ->whereNull('payment_date')
            ->count();

        // Datos de las tarjetas
        $cardStats = [
            [
                'statSubtitle' => 'Active Properties',
                'statTitle' => $activeProperties,
                'statPercent' => '5', // Porcentaje de cambio mensual, puedes calcularlo dinámicamente si tienes datos históricos
                'statPercentColor' => 'text-emerald-500',
                'statDescripiron' => 'Change since last month',
                'statIconName' => 'fas fa-home',
                'statIconColor' => 'bg-blue-500',
            ],
            [
                'statSubtitle' => 'Occupancy Rate',
                'statTitle' => "{$occupancyRate}%",
                'statArrow' => 'up', // Dirección del cambio
                'statPercent' => '10',
                'statPercentColor' => 'text-emerald-500',
                'statDescripiron' => 'Compared to last month',
                'statIconName' => 'fas fa-chart-line',
                'statIconColor' => 'bg-green-500',
            ],
            [
                'statSubtitle' => 'Estimated Income',
                'statTitle' => "$" . number_format($currentMonthIncome, 2),
                'statArrow' => 'up',
                'statPercent' => '15',
                'statPercentColor' => 'text-emerald-500',
                'statDescripiron' => 'Estimated this month',
                'statIconName' => 'fas fa-dollar-sign',
                'statIconColor' => 'bg-yellow-500',
            ],
            [
                'statSubtitle' => 'Pending Payments',
                'statTitle' => $pendingPayments,
                'statArrow' => 'down',
                'statPercent' => '20',
                'statPercentColor' => 'text-red-500',
                'statDescripiron' => 'Overdue payments',
                'statIconName' => 'fas fa-exclamation-circle',
                'statIconColor' => 'bg-red-500',
            ],
        ];

        // Consultar propiedades con relaciones
        $properties = DB::table('properties as p')
            ->select(
                'p.id as property_id',
                'CONCAT(p.street,',
                ',p.number,',
                ',p.city,',
                ',p.state,.',
                ',p.postal_code) as property_address',
                DB::raw("CONCAT(u.first_name,' ', u.last_name) as tenant_name"),
                'c.rental_amount as rental_amount',
                'c.end_date as contract_end',
                'c.status as contract_status'
            )
            ->leftJoin('contracts as c', 'p.id', '=', 'c.property_id')
            ->leftJoin('users as u', 'c.tenant_user_id', '=', 'u.id')
            ->whereIn('c.status', ['Active', 'Pending Renewal', 'Terminated'])
            ->get();



        return inertia('Dashboard', [
            'childComponent' => 'Charts',
            'monthlyIncome' => $monthlyIncome,
            'occupancyData' => $occupancy,
            'cardStats' => $cardStats,
            'propertiesData' => $properties,
            'requests' => $requests,
            'user' => auth()->user(),
        ]);
    }
}
