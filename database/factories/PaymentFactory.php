<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = fake()->randomElement([750000, 1000000, 1500000]);

        return [
            'payment_date' => now(),
            'period_month' => now()->startOfMonth(),
            'amount' => $amount,
            'late_fee' => 0,
            'total' => $amount,
            'status' => 'paid',
            'payment_method' => fake()->randomElement(['cash', 'transfer', 'e-wallet']),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Payment $payment) {
            if (empty($payment->tenant_id)) {
                $tenant = Tenant::factory()->create();
                $payment->tenant_id = $tenant->id;
                $payment->room_id = $tenant->room_id;
            }
        });
    }

    public function withLateFee(int $lateFee): static
    {
        return $this->state(function (array $attributes) use ($lateFee) {
            return [
                'late_fee' => $lateFee,
                'total' => $attributes['amount'] + $lateFee,
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
        ]);
    }

    public function forPeriod(string $period): static
    {
        return $this->state(fn (array $attributes) => [
            'period_month' => $period,
        ]);
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
        ]);
    }
}
