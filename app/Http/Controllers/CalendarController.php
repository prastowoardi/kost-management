<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));

        $current = Carbon::create($year, $month, 1);

        $gridStart = $current->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $current->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $events = $this->buildEvents($current, $gridStart, $gridEnd);

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor <= $gridEnd) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $key = $cursor->format('Y-m-d');
                $week[] = [
                    'date' => $cursor->copy(),
                    'day' => $cursor->day,
                    'isToday' => $cursor->isToday(),
                    'inMonth' => $cursor->month === $month,
                    'events' => $events[$key] ?? [],
                    'eventCount' => count($events[$key] ?? []),
                ];
                $cursor->addDay();
            }
            $weeks[] = $week;
        }

        $prev = $current->copy()->subMonthNoOverflow();
        $next = $current->copy()->addMonthNoOverflow();

        $counts = [
            'move-in' => 0,
            'move-out' => 0,
            'payment' => 0,
            'complaint' => 0,
        ];

        foreach ($events as $dayEvents) {
            foreach ($dayEvents as $event) {
                $counts[$event['type']] = ($counts[$event['type']] ?? 0) + 1;
            }
        }

        $upcoming = $this->buildUpcoming();

        return view('calendar.index', compact(
            'current',
            'prev',
            'next',
            'weeks',
            'counts',
            'upcoming'
        ));
    }

    private function buildEvents(Carbon $current, Carbon $gridStart, Carbon $gridEnd): array
    {
        $events = [];

        foreach ($this->moveInTenants($gridStart, $gridEnd) as $tenant) {
            $events[$tenant->entry_date->format('Y-m-d')][] = [
                'type' => 'move-in',
                'label' => $tenant->name,
                'sub' => 'Check-in'.($tenant->room ? ' · Kamar '.$tenant->room->room_number : ''),
                'icon' => 'arrow-down-tray',
                'color' => 'emerald',
                'url' => route('tenants.index', ['search' => $tenant->name]),
            ];
        }

        foreach ($this->moveOutTenants($gridStart, $gridEnd) as $tenant) {
            $events[$tenant->exit_date->format('Y-m-d')][] = [
                'type' => 'move-out',
                'label' => $tenant->name,
                'sub' => 'Check-out'.($tenant->room ? ' · Kamar '.$tenant->room->room_number : ''),
                'icon' => 'arrow-up-tray',
                'color' => 'rose',
                'url' => route('tenants.index', ['search' => $tenant->name]),
            ];
        }

        foreach ($this->payments($gridStart, $gridEnd) as $payment) {
            $status = $payment->status ?? 'pending';
            $color = $status === 'paid' ? 'sky' : ($status === 'overdue' ? 'amber' : 'violet');

            $events[$payment->payment_date->format('Y-m-d')][] = [
                'type' => 'payment',
                'label' => $payment->tenant?->name ?? 'Pembayaran',
                'sub' => 'Rp '.number_format($payment->total ?? $payment->amount, 0, ',', '.'),
                'icon' => 'banknotes',
                'color' => $color,
                'url' => route('payments.index', ['invoice_number' => $payment->invoice_number]),
            ];
        }

        foreach ($this->complaints($gridStart, $gridEnd) as $complaint) {
            $events[$complaint->created_at->format('Y-m-d')][] = [
                'type' => 'complaint',
                'label' => $complaint->title,
                'sub' => 'Keluhan'.($complaint->room ? ' · Kamar '.$complaint->room->room_number : ''),
                'icon' => 'megaphone',
                'color' => 'orange',
                'url' => route('complaints.index', ['search' => $complaint->title]),
            ];

            if ($complaint->resolved_date) {
                $events[$complaint->resolved_date->format('Y-m-d')][] = [
                    'type' => 'complaint',
                    'label' => 'Selesai: '.$complaint->title,
                    'sub' => 'Keluhan ditindaklanjuti',
                    'icon' => 'check-circle',
                    'color' => 'teal',
                    'url' => route('complaints.index', ['search' => $complaint->title]),
                ];
            }
        }

        return $events;
    }

    private function buildUpcoming(): \Illuminate\Support\Collection
    {
        $from = now()->startOfMonth()->startOfDay();
        $to = now()->endOfMonth()->endOfDay();

        $items = collect();

        foreach (Tenant::whereBetween('entry_date', [$from, $to])->whereNotNull('entry_date')->with('room')->get() as $tenant) {
            $items->push([
                'type' => 'move-in',
                'date' => $tenant->entry_date,
                'color' => 'emerald',
                'icon' => 'arrow-down-tray',
                'title' => $tenant->name,
                'sub' => 'Check-in'.($tenant->room ? ' Kamar '.$tenant->room->room_number : ''),
                'url' => route('tenants.index', ['search' => $tenant->name]),
            ]);
        }

        foreach (Tenant::whereBetween('exit_date', [$from, $to])->whereNotNull('exit_date')->with('room')->get() as $tenant) {
            $items->push([
                'type' => 'move-out',
                'date' => $tenant->exit_date,
                'color' => 'rose',
                'icon' => 'arrow-up-tray',
                'title' => $tenant->name,
                'sub' => 'Check-out'.($tenant->room ? ' Kamar '.$tenant->room->room_number : ''),
                'url' => route('tenants.index', ['search' => $tenant->name]),
            ]);
        }

        foreach (Payment::with('tenant')->whereBetween('payment_date', [$from, $to])->get() as $payment) {
            $status = $payment->status ?? 'pending';
            $items->push([
                'type' => 'payment',
                'date' => $payment->payment_date,
                'color' => $status === 'paid' ? 'sky' : ($status === 'overdue' ? 'amber' : 'violet'),
                'icon' => 'banknotes',
                'title' => $payment->tenant?->name ?? 'Pembayaran',
                'sub' => ($status === 'paid' ? 'Dibayar · ' : '').'Rp '.number_format($payment->total ?? $payment->amount, 0, ',', '.'),
                'url' => route('payments.index', ['invoice_number' => $payment->invoice_number]),
            ]);
        }

        foreach (Complaint::with('room')->whereBetween('created_at', [$from, $to])->get() as $complaint) {
            $items->push([
                'type' => 'complaint',
                'date' => $complaint->created_at,
                'color' => 'orange',
                'icon' => 'megaphone',
                'title' => $complaint->title,
                'sub' => $complaint->room ? 'Kamar '.$complaint->room->room_number : 'Keluhan',
                'url' => route('complaints.index', ['search' => $complaint->title]),
            ]);
        }

        return $items->sortBy('date')->values()->take(20);
    }

    private function moveInTenants(Carbon $start, Carbon $end)
    {
        return Tenant::whereBetween('entry_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->whereNotNull('entry_date')
            ->with('room')
            ->get();
    }

    private function moveOutTenants(Carbon $start, Carbon $end)
    {
        return Tenant::whereBetween('exit_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->whereNotNull('exit_date')
            ->with('room')
            ->get();
    }

    private function payments(Carbon $start, Carbon $end)
    {
        return Payment::with('tenant')
            ->whereBetween('payment_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get();
    }

    private function complaints(Carbon $start, Carbon $end)
    {
        return Complaint::with('room')
            ->whereBetween('created_at', [$start->format('Y-m-d').' 00:00:00', $end->format('Y-m-d').' 23:59:59'])
            ->get();
    }
}
