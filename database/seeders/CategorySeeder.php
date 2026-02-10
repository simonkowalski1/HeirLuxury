<?php

// ABOUTME: Seeder that imports all categories from config/categories.php into the database.
// ABOUTME: Parses the config structure to extract gender, section, brand, and slug fields.

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table from config/categories.php.
     *
     * Parses each config entry to extract the brand name from the display name,
     * and maps the section label to a lowercase section value.
     */
    public function run(): void
    {
        $config = config('categories', []);
        $order = 0;

        foreach ($config as $gender => $sections) {
            foreach ($sections as $sectionLabel => $items) {
                $section = $this->normalizeSectionLabel($sectionLabel);

                foreach ($items as $item) {
                    $name = $item['name'] ?? $item['label'] ?? 'Unnamed';
                    $params = $item['params'] ?? [];
                    $slug = $params['category'] ?? ($params['slug'] ?? Str::slug($name));

                    // Extract brand from name by removing section/gender words
                    $brand = $this->extractBrand($name, $sectionLabel, $gender);

                    Category::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'gender' => $gender,
                            'section' => $section,
                            'brand' => $brand,
                            'display_order' => $order++,
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Normalize section label to lowercase slug format.
     *
     * @param  string  $label  e.g. "Bags", "Clothing", "Glasses"
     * @return string e.g. "bags", "clothing", "glasses"
     */
    protected function normalizeSectionLabel(string $label): string
    {
        return strtolower($label);
    }

    /**
     * Extract the brand name from a category display name.
     *
     * Removes section words (Bags, Shoes, etc.) and gender words (Women, Men)
     * to isolate the brand portion.
     *
     * @param  string  $name  e.g. "Louis Vuitton Women Shoes"
     * @param  string  $sectionLabel  e.g. "Shoes"
     * @param  string  $gender  e.g. "women"
     * @return string e.g. "Louis Vuitton"
     */
    protected function extractBrand(string $name, string $sectionLabel, string $gender): string
    {
        $remove = [$sectionLabel, ucfirst($gender), 'Women', 'Men'];

        $brand = $name;
        foreach ($remove as $word) {
            $brand = str_replace($word, '', $brand);
        }

        return trim($brand);
    }
}
