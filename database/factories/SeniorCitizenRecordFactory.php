<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Senior\SeniorCitizenRecord;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Senior\SeniorCitizenRecord>
 */
class SeniorCitizenRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = SeniorCitizenRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $barangays = [
            "Acacia","Adlas","Anahaw I","Anahaw II","Balite I","Balite II","Balubad","Banaba","Batas",
            "Biga I","Biga II","Biluso","Bucal","Buho","Bulihan","Cabangaan","Carmen","Hoyo","Hukay","Iba",
            "Inchican","Ipil I","Ipil II","Kalubkob","Kaong","Lalaan I","Lalaan II","Litlit","Lucsuhin","Lumil",
            "Maguyam","Malabag","Malaking Tatyao","Mataas na Burol","Munting Ilog","Narra I","Narra II","Narra III",
            "Paligawan","Pasong Langka","Barangay I (Poblacion)","Barangay II (Poblacion)","Barangay III (Poblacion)",
            "Barangay IV (Poblacion)","Barangay V (Poblacion)","Pooc I","Pooc II","Pulong Bunga","Pulong Saging",
            "Puting Kahoy","Sabutan","San Miguel I","San Miguel II","San Vicente I","San Vicente II","Santol",
            "Tartaria","Tibig","Toledo","Tubuan I","Tubuan II","Tubuan III","Ulat","Yakal"
        ];

        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $middleName = $this->faker->optional()->middleName();
        $birthDate = $this->faker->dateTimeBetween('-90 years', '-60 years');
        $yearApplied = $birthDate->format('Y') + 60;

        return [
            'record_number' => 'SCR-' . $this->faker->unique()->numerify('######'),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'year_applied' => $yearApplied,
            'control_number' => 'SC-' . strtoupper(substr($barangays[array_rand($barangays)], 0, 3)) . '-' . $yearApplied . '-' . $this->faker->numerify('######'),
            'senior_id_number' => 'OSCA-' . $this->faker->numerify('########'),
            'address' => $this->faker->streetAddress(),
            'barangay' => $this->faker->randomElement($barangays),
            'birth_date' => $birthDate->format('Y-m-d'),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'contact_number' => $this->faker->phoneNumber(),
            'philsys_number' => $this->faker->optional()->numerify('##############'),
            'rrn_number' => $this->faker->optional()->numerify('##############'),
            'osca_id' => $this->faker->optional()->numerify('########'),
            'blood_type' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widowed', 'Separated', 'Divorced']),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_number' => $this->faker->phoneNumber(),
            'emergency_contact_relationship' => $this->faker->randomElement(['Spouse', 'Son', 'Daughter', 'Brother', 'Sister', 'Parent', 'Other']),
            'photo' => null,
            'avatar_image' => null,
            'qr_code' => null,
            'qr_code_image' => null,
            'date_issued' => $this->faker->optional()->date(),
            'last_printed_at' => $this->faker->optional()->dateTime(),
            'print_count' => $this->faker->numberBetween(0, 5),
            'remarks' => $this->faker->optional()->sentence(),
            'created_by' => User::inRandomOrder()->first()?->id ?? 1,
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'pending']),
        ];
    }
}
