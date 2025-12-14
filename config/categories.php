<?php

/**
 * HeirLuxury Catalog Taxonomy Configuration
 *
 * This file defines the complete category hierarchy for the product catalog.
 * It's used by the CategoryController to build navigation menus and resolve
 * category slugs to database queries.
 *
 * Structure:
 * ----------
 * The taxonomy is organized as: Gender → Section → Categories
 *
 * - Gender: Top level ("women", "men")
 * - Section: Product type grouping ("Bags", "Shoes", "Clothing", etc.)
 * - Categories: Individual category entries with routing information
 *
 * Category Types:
 * ---------------
 * 1. "Synthetic" categories (UI groupings only):
 *    - "women-bags", "men-shoes" - Show ALL products in that section
 *    - These don't exist in the products table; the controller aggregates leaf categories
 *
 * 2. "Leaf" categories (actual product categories):
 *    - "louis-vuitton-women-bags" - Maps directly to products.category_slug
 *    - These are what's actually stored in the database
 *
 * Entry Format:
 * -------------
 * Each category entry is an array with:
 * - 'name'   => Display name shown in navigation
 * - 'route'  => Laravel route name (typically 'catalog.category')
 * - 'params' => Route parameters, including 'category' slug
 *
 * Adding New Categories:
 * ----------------------
 * 1. Add the entry here under the appropriate gender/section
 * 2. If it's a new brand, add the folder mapping in ImportLV.php
 * 3. Run the import command to populate products
 *
 * @see \App\Http\Controllers\CategoryController::show() For slug resolution logic
 * @see \App\Console\Commands\ImportLV For folder-to-category mapping
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Women's Categories
    |--------------------------------------------------------------------------
    |
    | All product categories for women, organized by section (Bags, Shoes, etc.)
    | The first entry in each section is typically the "All" aggregator.
    |
    */
    'women' => [

        // ---- Bags ----
        'Bags' => [
            ['name' => 'All Women Bags', 'route' => 'catalog.category', 'params' => ['category' => 'women-bags']],


            ['name' => 'Louis Vuitton Bags', 'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-women-bags']],
            ['name' => 'Chanel Bags',        'route' => 'catalog.category', 'params' => ['category' => 'chanel-bags']],
            ['name' => 'Dior Bags',          'route' => 'catalog.category', 'params' => ['category' => 'dior-bags']],
            ['name' => 'Gucci Bags',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-bags']],
            ['name' => 'Celine Bags',        'route' => 'catalog.category', 'params' => ['category' => 'celine-bags']],
            ['name' => 'Prada Bags',         'route' => 'catalog.category', 'params' => ['category' => 'prada-bags']],
            ['name' => 'YSL Bags',           'route' => 'catalog.category', 'params' => ['category' => 'ysl-bags']],
        ],

        // ---- Shoes ----
        'Shoes' => [
            ['name' => 'All Women Shoes', 'route' => 'catalog.category', 'params' => ['category' => 'women-shoes']],


            ['name' => 'Louis Vuitton Women Shoes', 'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-women-shoes']],
            ['name' => 'Chanel Women Shoes',        'route' => 'catalog.category', 'params' => ['category' => 'chanel-women-shoes']],
            ['name' => 'Dior Women Shoes',          'route' => 'catalog.category', 'params' => ['category' => 'dior-women-shoes']],
            ['name' => 'Gucci Women Shoes',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-women-shoes']],
            ['name' => 'Celine Women Shoes',        'route' => 'catalog.category', 'params' => ['category' => 'celine-women-shoes']],
            ['name' => 'Prada Women Shoes',         'route' => 'catalog.category', 'params' => ['category' => 'prada-women-shoes']],
        ],

        // ---- Clothing ----
        'Clothing' => [
            ['name' => 'All Women Clothing', 'route' => 'catalog.category', 'params' => ['category' => 'women-clothing']],

            ['name' => 'Louis Vuitton Women Clothing', 'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-women-clothes']],
            ['name' => 'Chanel Women Clothing',        'route' => 'catalog.category', 'params' => ['category' => 'chanel-women-clothes']],
            ['name' => 'Dior Women Clothing',          'route' => 'catalog.category', 'params' => ['category' => 'dior-women-clothes']],
            ['name' => 'Gucci Women Clothing',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-women-clothes']],
        ],

        // ---- Small Leather Goods ----
        'Small Leather Goods' => [
            ['name' => 'All Women SLG', 'route' => 'catalog.category', 'params' => ['category' => 'women-small-leather-goods']],

            ['name' => 'Wallets',      'route' => 'catalog.category', 'params' => ['category' => 'wallets']],
            ['name' => 'Card Holders', 'route' => 'catalog.category', 'params' => ['category' => 'card-holders']],
            ['name' => 'Phone Cases',  'route' => 'catalog.category', 'params' => ['category' => 'phone-cases']],
            ['name' => 'Key Chains',   'route' => 'catalog.category', 'params' => ['category' => 'key-chains']],
        ],

        // ---- Accessories ----
        'Accessories' => [
            ['name' => 'All Women Accessories', 'route' => 'catalog.category', 'params' => ['category' => 'women-accessories']],

            ['name' => 'Scarves',             'route' => 'catalog.category', 'params' => ['category' => 'scarves']],
            ['name' => 'Hats',                'route' => 'catalog.category', 'params' => ['category' => 'hats']],
            ['name' => 'Gloves',              'route' => 'catalog.category', 'params' => ['category' => 'gloves']],
            ['name' => 'Hair Accessories',    'route' => 'catalog.category', 'params' => ['category' => 'hair-accessories']],
            ['name' => 'Home Decor',          'route' => 'catalog.category', 'params' => ['category' => 'home-decor']],
            ['name' => 'Thermos / Drinkware', 'route' => 'catalog.category', 'params' => ['category' => 'thermos']],
            ['name' => 'Luggage & Trolleys',  'route' => 'catalog.category', 'params' => ['category' => 'luggage']],
            ['name' => 'Ski Goggles',         'route' => 'catalog.category', 'params' => ['category' => 'ski-goggles']],
        ],

        // ---- Belts ----
        'Belts' => [
            ['name' => 'All Women Belts', 'route' => 'catalog.category', 'params' => ['category' => 'women-belts']],

            ['name' => 'Louis Vuitton Belts', 'route' => 'catalog.category', 'params' => ['category' => 'lv-belts']],
            ['name' => 'Gucci Belts',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-belts']],
            ['name' => 'YSL Belts',           'route' => 'catalog.category', 'params' => ['category' => 'ysl-belts']],
            ['name' => 'Celine Belts',        'route' => 'catalog.category', 'params' => ['category' => 'celine-belts']],
        ],

        // ---- Jewelry ----
        'Jewelry' => [
            ['name' => 'All Women Jewelry', 'route' => 'catalog.category', 'params' => ['category' => 'women-jewelry']],

            ['name' => 'Chanel Jewelry', 'route' => 'catalog.category', 'params' => ['category' => 'chanel-jewelry']],
            ['name' => 'Dior Jewelry',   'route' => 'catalog.category', 'params' => ['category' => 'dior-jewelry']],
        ],

        // ---- Glasses ----
        'Glasses' => [
            ['name' => 'All Women Glasses', 'route' => 'catalog.category', 'params' => ['category' => 'women-glasses']],

            ['name' => 'Louis Vuitton Glasses', 'route' => 'catalog.category', 'params' => ['category' => 'lv-glasses']],
            ['name' => 'Gucci Glasses',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-glasses']],
            ['name' => 'Prada Glasses',         'route' => 'catalog.category', 'params' => ['category' => 'prada-glasses']],
            ['name' => 'Celine Glasses',        'route' => 'catalog.category', 'params' => ['category' => 'celine-glasses']],
        ],
        // ---- Watches ----
        'Watches' => [
            ['name' => 'All Women Watches', 'route' => 'catalog.category', 'params' => ['category' => 'women-watches']],
            ['name' => 'Top Quality Watches', 'route' => 'catalog.category', 'params' => ['category' => 'top-quality-watches']],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Men's Categories
    |--------------------------------------------------------------------------
    |
    | All product categories for men, organized by section (Bags, Shoes, etc.)
    | Follows the same structure as women's categories.
    |
    */
    'men' => [

        // ---- Bags ----
        'Bags' => [
            ['name' => 'All Men Bags', 'route' => 'catalog.category', 'params' => ['category' => 'men-bags']],
            ['name' => 'Gucci Men Bags', 'route' => 'catalog.category', 'params' => ['category' => 'gucci-men-bags']],
        ],

        // ---- Shoes ----
        'Shoes' => [
            ['name' => 'All Men Shoes', 'route' => 'catalog.category', 'params' => ['category' => 'men-shoes']],
            ['name' => 'Louis Vuitton Men Shoes',  'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-men-shoes']],
            ['name' => 'Chanel Men Shoes',         'route' => 'catalog.category', 'params' => ['category' => 'chanel-men-shoes']],
            ['name' => 'Dior Men Shoes',           'route' => 'catalog.category', 'params' => ['category' => 'dior-men-shoes']],
            ['name' => 'Gucci Men Shoes',          'route' => 'catalog.category', 'params' => ['category' => 'gucci-men-shoes']],
            ['name' => 'Celine Men Shoes',         'route' => 'catalog.category', 'params' => ['category' => 'celine-men-shoes']],
            ['name' => 'Prada Men Shoes',          'route' => 'catalog.category', 'params' => ['category' => 'prada-men-shoes']],
            ['name' => 'Nike / AJ / Sports Shoes', 'route' => 'catalog.category', 'params' => ['category' => 'nike-aj-sneakers']],
        ],

        // ---- Clothing ----
        'Clothing' => [
            ['name' => 'All Men Clothing', 'route' => 'catalog.category', 'params' => ['category' => 'men-clothing']],
            ['name' => 'Louis Vuitton Men Clothing', 'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-men-clothes']],
            ['name' => 'Chanel Men Clothing',        'route' => 'catalog.category', 'params' => ['category' => 'chanel-men-clothes']],
            ['name' => 'Dior Men Clothing',          'route' => 'catalog.category', 'params' => ['category' => 'dior-men-clothes']],
            ['name' => 'Gucci Men Clothing',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-men-clothes']],
            ['name' => 'Celine Men Clothing',        'route' => 'catalog.category', 'params' => ['category' => 'celine-men-clothes']],
            ['name' => 'Moncler Men Clothing',       'route' => 'catalog.category', 'params' => ['category' => 'moncler-men-clothes']],
        ],

        // ---- Small Leather Goods ----
        'Small Leather Goods' => [
            ['name' => 'All Men SLG', 'route' => 'catalog.category', 'params' => ['category' => 'men-small-leather-goods']],
            ['name' => 'Wallets',      'route' => 'catalog.category', 'params' => ['category' => 'wallets-men']],
            ['name' => 'Card Holders', 'route' => 'catalog.category', 'params' => ['category' => 'card-holders-men']],
            ['name' => 'Phone Cases',  'route' => 'catalog.category', 'params' => ['category' => 'phone-cases-men']],
            ['name' => 'Key Chains',   'route' => 'catalog.category', 'params' => ['category' => 'key-chains-men']],
        ],

        // ---- Accessories ----
        'Accessories' => [
            ['name' => 'All Men Accessories', 'route' => 'catalog.category', 'params' => ['category' => 'men-accessories']],
            ['name' => 'Scarves',            'route' => 'catalog.category', 'params' => ['category' => 'scarves-men']],
            ['name' => 'Hats',               'route' => 'catalog.category', 'params' => ['category' => 'hats-men']],
            ['name' => 'Gloves',             'route' => 'catalog.category', 'params' => ['category' => 'gloves-men']],
            ['name' => 'Socks',              'route' => 'catalog.category', 'params' => ['category' => 'socks-men']],
            ['name' => 'Home Decor',         'route' => 'catalog.category', 'params' => ['category' => 'home-decor-men']],
            ['name' => 'Luggage & Trolleys', 'route' => 'catalog.category', 'params' => ['category' => 'luggage-men']],
        ],

        // ---- Belts ----
        'Belts' => [
            ['name' => 'All Men Belts', 'route' => 'catalog.category', 'params' => ['category' => 'men-belts']],
            ['name' => 'Louis Vuitton Belts', 'route' => 'catalog.category', 'params' => ['category' => 'louis-vuitton-belts-men']],
            ['name' => 'Gucci Belts',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-belts-men']],
            ['name' => 'Celine Belts',        'route' => 'catalog.category', 'params' => ['category' => 'celine-belts-men']],
            ['name' => 'Prada Belts',         'route' => 'catalog.category', 'params' => ['category' => 'prada-belts-men']],
        ],

        // ---- Jewelry ----
        'Jewelry' => [
            ['name' => 'All Men Jewelry', 'route' => 'catalog.category', 'params' => ['category' => 'men-jewelry']],
            ['name' => 'Louis Vuitton Jewelry', 'route' => 'catalog.category', 'params' => ['category' => 'lv-jewelry-men']],
            ['name' => 'Gucci Jewelry',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-jewelry-men']],
        ],

        // ---- Glasses ----
        'Glasses' => [
            ['name' => 'All Men Glasses', 'route' => 'catalog.category', 'params' => ['category' => 'men-glasses']],
            ['name' => 'Louis Vuitton Glasses', 'route' => 'catalog.category', 'params' => ['category' => 'lv-glasses-men']],
            ['name' => 'Gucci Glasses',         'route' => 'catalog.category', 'params' => ['category' => 'gucci-glasses-men']],
            ['name' => 'Celine Glasses',        'route' => 'catalog.category', 'params' => ['category' => 'celine-glasses-men']],
        ],

        // ---- Watches ----
        'Watches' => [
            ['name' => 'All Men Watches', 'route' => 'catalog.category', 'params' => ['category' => 'men-watches']],
            ['name' => 'Top Quality Watches', 'route' => 'catalog.category', 'params' => ['category' => 'top-quality-watches-men']],
        ],
    ],
];
