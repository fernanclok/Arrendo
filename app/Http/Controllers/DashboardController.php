<?php

namespace App\Http\Controllers;

use App\Models\Maintenance_Request;
use App\Models\Payment_history;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
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
            ->where('owner_user_id', auth()->id())
            ->get();

        $activeProperties = DB::table('properties')
        ->where('owner_user_id', auth()->id())
        ->where('availability', 'Available')
        ->count();

        // Tasa de ocupación
        $occupiedUnits = DB::table('properties')->where('availability', 'Not Available')->where('owner_user_id',auth()->id())->count();
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
                'statIconName' => 'mdi mdi-home',
                'statIconColor' => 'bg-blue-500',
            ],
            [
                'statSubtitle' => 'Occupancy Rate',
                'statTitle' => "{$occupancyRate}%",
                'statArrow' => 'up', // Dirección del cambio
                'statPercent' => '10',
                'statPercentColor' => 'text-emerald-500',
                'statDescripiron' => 'Compared to last month',
                'statIconName' => 'mdi mdi-chart-line',
                'statIconColor' => 'bg-green-500',
            ],
            [
                'statSubtitle' => 'Estimated Income',
                'statTitle' => "$" . number_format($currentMonthIncome, 2),
                'statArrow' => 'up',
                'statPercent' => '15',
                'statPercentColor' => 'text-emerald-500',
                'statDescripiron' => 'Estimated this month',
                'statIconName' => 'mdi mdi-currency-usd',
                'statIconColor' => 'bg-yellow-500',
            ],
            [
                'statSubtitle' => 'Maintenance Payments',
                'statTitle' => "$" . number_format($pendingPayments, 2),
                'statArrow' => 'down',
                'statPercent' => '20',
                'statPercentColor' => 'text-red-500',
                'statDescripiron' => 'Overdue payments',
                'statIconName' => 'mdi mdi-currency-usd-off',
                'statIconColor' => 'bg-red-500',
            ],
        ];


        // Consultar propiedades con relaciones
        $properties = DB::table('properties as p')
            ->select(
                'p.id as property_id',
                DB::raw("
        CONCAT(
            p.street, ', ',
            p.number, ', ',
            p.city, ', ',
            p.state, ', ',
            p.postal_code
        ) as property_address
    "),
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as tenant_name"),
                'c.rental_amount as rental_amount',
                'c.end_date as contract_end',
                'c.status as contract_status'
            )
            ->leftJoin('contracts as c', 'p.id', '=', 'c.property_id')
            ->leftJoin('users as u', 'c.tenant_user_id', '=', 'u.id')
            ->whereIn('c.status', ['Active', 'Pending Renewal', 'Terminated'])
            ->where('p.owner_user_id', auth()->id())
            ->get();

        return inertia('Dashboard', [
            'childComponent' => $request->routeIs('dashboard.settings') ? 'Settings' : 'Charts',
            'monthlyIncome' => $monthlyIncome,
            'occupancyData' => $occupancy,
            'cardStats' => $cardStats,
            'propertiesData' => $properties,
            'requests' => $requests,
            'user' => auth()->user(),
        ]);
    }

    public function getPaymentHistory($tenantUserId)
    {
        try {
            $history = DB::table('users as u')
                ->join('contracts as c', 'u.id', '=', 'c.tenant_user_id')
                ->join('invoices as i', 'c.id', '=', 'i.contract_id')
                ->leftJoin('payment_histories as p', 'i.id', '=', 'p.invoice_id')
                ->select(
                    'u.id as tenant_user_id',
                    DB::raw("CONCAT(u.first_name, ' ', u.last_name) as tenant_name"),
                    'c.id as contract_id',
                    'i.id as invoice_id',
                    'i.issue_date as invoice_date',
                    'i.total_amount as invoice_total',
                    'i.payment_status',
                    'p.payment_date',
                    'p.amount_paid'
                )
                ->where('c.tenant_user_id', $tenantUserId)
                ->orderBy('i.issue_date', 'DESC')
                ->orderBy('p.payment_date', 'DESC')
                ->get();

            return response()->json($history, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving payment history', 'details' => $e->getMessage()], 500);
        }
    }

    public function getRentedProperty($tenantUserId)
    {


        $property = DB::table('contracts')
            ->join('properties', 'contracts.property_id', '=', 'properties.id')
            ->select(
                'properties.id as property_id',

                DB::raw("
                CONCAT(
                    properties.street, ', ',
                    properties.number, ', ',
                    properties.city, ', ',
                    properties.state, ', ',
                    properties.postal_code
                ) as property_address
            "),
                'properties.rental_rate',
                'contracts.start_date',
                'contracts.end_date',
                'contracts.status'
            )
            ->where('contracts.tenant_user_id', $tenantUserId)
            ->where('contracts.status', 'Active')
            ->first();

        return response()->json($property);
    }
}
