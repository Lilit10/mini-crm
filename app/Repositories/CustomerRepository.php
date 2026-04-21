<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository
{
    public function findOrCreateByPhone(string $phoneE164, string $name, ?string $email): Customer
    {
        $customer = Customer::query()->firstOrCreate(
            ['phone_e164' => $phoneE164],
            [
                'name' => $name,
                'email' => $email,
            ],
        );

        $customer->fill([
            'name' => $name,
            'email' => $email ?: $customer->email,
        ]);

        if ($customer->isDirty()) {
            $customer->save();
        }

        return $customer->fresh();
    }
}
