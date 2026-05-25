<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('customers');

        Customer::query()->truncate();
        Address::query()->truncate();

        $customer = Customer::query()->create([
            'name' => 'John Smith',
            'email' => 'john.smith@botble.com',
            'password' => Hash::make('12345678'),
            'phone' => $this->faker()->e164PhoneNumber(),
            'avatar' => 'customers/1.jpg',
            'dob' => now()->subYears(rand(20, 50))->subDays(rand(1, 30)),
        ]);

        $customer->confirmed_at = now();
        $customer->save();

        Address::query()->create([
            'name' => $customer->name,
            'phone' => $this->faker()->e164PhoneNumber(),
            'email' => $customer->email,
            'country' => $this->faker()->countryCode(),
            'state' => $this->faker()->state(),
            'city' => $this->faker()->city(),
            'address' => $this->faker()->streetAddress(),
            'zip_code' => $this->faker()->postcode(),
            'customer_id' => $customer->id,
            'is_default' => true,
        ]);

        Address::query()->create([
            'name' => $customer->name,
            'phone' => $this->faker()->e164PhoneNumber(),
            'email' => $customer->email,
            'country' => $this->faker()->countryCode(),
            'state' => $this->faker()->state(),
            'city' => $this->faker()->city(),
            'address' => $this->faker()->streetAddress(),
            'zip_code' => $this->faker()->postcode(),
            'customer_id' => $customer->id,
            'is_default' => false,
        ]);

        for ($i = 0; $i < 10; $i++) {
            $customer = Customer::query()->create([
                'name' => $this->faker()->name(),
                'email' => $this->faker()->unique()->safeEmail(),
                'password' => Hash::make('12345678'),
                'phone' => $this->faker()->e164PhoneNumber(),
                'avatar' => 'customers/' . ($i + 1) . '.jpg',
                'dob' => now()->subYears(rand(20, 50))->subDays(rand(1, 30)),
            ]);

            $customer->confirmed_at = now();
            $customer->save();

            Address::query()->create([
                'name' => $customer->name,
                'phone' => $this->faker()->e164PhoneNumber(),
                'email' => $customer->email,
                'country' => $this->faker()->countryCode(),
                'state' => $this->faker()->state(),
                'city' => $this->faker()->city(),
                'address' => $this->faker()->streetAddress(),
                'zip_code' => $this->faker()->postcode(),
                'customer_id' => $customer->id,
                'is_default' => true,
            ]);
        }
    }
}
