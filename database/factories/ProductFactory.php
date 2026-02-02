<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);
        $brands = ['Louis Vuitton', 'Chanel', 'Dior', 'Hermès', 'Gucci', 'Celine', 'Prada', 'YSL'];
        $sections = ['bags', 'shoes', 'clothes', 'accessories'];
        $genders = ['women', 'men'];

        $brand = fake()->randomElement($brands);
        $gender = fake()->randomElement($genders);
        $section = fake()->randomElement($sections);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'brand' => $brand,
            'gender' => $gender,
            'section' => $section,
            'category_slug' => Str::slug($brand).'-'.$gender.'-'.$section,
            'folder' => Str::slug($brand).'-'.$section.'-'.$gender.'/'.Str::slug($name),
            'image' => '0000.jpg',
        ];
    }

    public function forBrand(string $brand): static
    {
        return $this->state(fn (array $attributes) => [
            'brand' => $brand,
        ]);
    }

    public function forGender(string $gender): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => $gender,
        ]);
    }

    public function forSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'section' => $section,
        ]);
    }
}
