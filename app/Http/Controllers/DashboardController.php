<?php

namespace App\Http\Controllers;

use App\Models\Maintenance_Request;
use App\Models\Payment_history;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $requests = DB::table('maintenance_requests as m')
            ->join('properties as p', 'm.property_id', '=', 'p.id')
            ->select('m.*')
            ->where('p.owner_user_id', auth()->id())
            ->where('m.status', 'Pending')
            ->where('m.priority', 'High')
            ->orderBy('m.priority', 'desc')
            ->orderBy('m.report_date', 'desc')
            ->take(10)
            ->get();

        // Datos de gasto de mantenimiento totales por mes
        $totalCostCurrentMonth = DB::table('maintenance_requests as m')
            ->join('properties as p', 'p.id', '=', 'm.property_id')
            ->join('users as u', 'u.id', '=', 'p.owner_user_id')
            ->select(DB::raw('SUM(m.maintenance_cost) as total_cost'))
            ->where('p.owner_user_id', auth()->id())
            ->where('m.status', 'Pending')
            ->whereMonth('m.date_review', Carbon::now()->month) // Filtra por el mes actual
            ->whereYear('m.date_review', Carbon::now()->year)   // Filtra por el año actual
            ->first();

        // Datos de ingresos totales por mes

        $monthlyIncome = DB::table('payment_histories as ph')
            ->join('invoices as i', 'ph.invoice_id', '=', 'i.id')
            ->join('contracts as c', 'i.contract_id', '=', 'c.id')
            ->join('properties as p', 'c.property_id', '=', 'p.id')
            ->selectRaw('EXTRACT(YEAR FROM ph.payment_date) as year, EXTRACT(MONTH FROM ph.payment_date) as month, SUM(ph.amount_paid) as total_income')
            ->where('p.owner_user_id', auth()->id())
            ->groupBy(DB::raw('EXTRACT(YEAR FROM ph.payment_date)'), DB::raw('EXTRACT(MONTH FROM ph.payment_date)'))
            ->orderByRaw('EXTRACT(YEAR FROM ph.payment_date) ASC')
            ->orderByRaw('EXTRACT(MONTH FROM ph.payment_date) ASC')
            ->get();


        $occupancy = DB::table('properties')
            ->select(
                DB::raw("COUNT(CASE WHEN availability = 'Available' THEN 1 END) as available"),
                DB::raw("COUNT(CASE WHEN availability = 'Not Available' THEN 1 END) as not_available"),
                DB::raw("EXTRACT(YEAR FROM updated_at) as year"),
                DB::raw("EXTRACT(MONTH FROM updated_at) as month")
            )
            ->where('owner_user_id', auth()->id())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $activeProperties = DB::table('properties')
            ->where('owner_user_id', auth()->id())
            ->where('availability', 'Available')
            ->count();

        $allProperties = DB::table('properties')
            ->where('owner_user_id', auth()->id())
            ->count();

        // Tasa de ocupación
        $occupiedUnits = DB::table('properties')->where('availability', 'Not Available')->where('owner_user_id', auth()->id())->count();
        $totalProperties = DB::table('properties')->where('owner_user_id', auth()->id())->count();
        $occupancyRate = $totalProperties > 0
            ? round(($occupiedUnits / $totalProperties) * 100, 2)
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
                'statTitle' => "{$activeProperties} of {$allProperties}",
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
                'statTitle' => "$" . number_format($totalCostCurrentMonth->total_cost ?? 0, 2),
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
            ->where(function ($query) {
                $query->whereIn('c.status', ['Active', 'Pending Renewal', 'Terminated'])
                    ->orWhereNull('c.status');
            })
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

    public function getNotifications($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->orderBy('sent_date', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        try {
            // Buscar la notificación por su ID
            $notification = Notification::findOrFail($id);

            // Actualizar el estado de lectura
            $notification->read_status = true;
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
                'data' => $notification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsUnread($id)
    {
        try {
            // Buscar la notificación por su ID
            $notification = Notification::findOrFail($id);

            // Actualizar el estado de lectura
            $notification->read_status = false;
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
                'data' => $notification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
