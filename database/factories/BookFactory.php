<?php

namespace Database\Factories;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->title()." ".$this->faker->lastName(),
            'author' =>
                $this->faker->firstName()." ".$this->faker->lastName(),
            'preview' => $this->faker->realText(20),
            'description' => $this->faker->realText(),
            'published_at' => random_int(1956, 2022)
        ];
    }
}
