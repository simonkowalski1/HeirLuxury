<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $genders = ['women', 'men'];
        $sections = ['bags', 'shoes', 'clothing', 'belts', 'jewelry', 'glasses'];

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'gender' => fake()->randomElement($genders),
            'section' => fake()->randomElement($sections),
            'brand' => fake()->company(),
            'display_order' => 0,
            'is_active' => true,
        ];
    }
}
