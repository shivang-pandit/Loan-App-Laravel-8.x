<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LoanPaymentsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payment_due_date' => $this->faker->numberBetween(10,100),
            'due_amount' => $this->faker->numberBetween(1,10),
        ];
    }
}
