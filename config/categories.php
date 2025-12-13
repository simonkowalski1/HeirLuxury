<?php
/**
 * HeirLuxury catalog taxonomy (route-friendly)
 */

return [

    // ========================= WOMEN =========================
    'women' => [

        // ---- Bags ----
        'Bags' => [
            ['name' => 'All Women Bags', 'href' => '#'],

            ['name' => 'Louis Vuitton Bags', 'route' => 'categories.show', 'params' => ['slug' => 'louis-vuitton-women-bags']],
            ['name' => 'Chanel Bags',        'route' => 'categories.show', 'params' => ['slug' => 'chanel-bags']],
            ['name' => 'Dior Bags',          'route' => 'categories.show', 'params' => ['slug' => 'dior-bags']],
            ['name' => 'Gucci Bags',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-bags']],
            ['name' => 'Celine Bags',        'route' => 'categories.show', 'params' => ['slug' => 'celine-bags']],
            ['name' => 'Prada Bags',         'route' => 'categories.show', 'params' => ['slug' => 'prada-bags']],
            ['name' => 'YSL Bags',           'route' => 'categories.show', 'params' => ['slug' => 'ysl-bags']],
        ],

        // ---- Shoes ----
        'Shoes' => [
            ['name' => 'All Women Shoes', 'href' => '#'],

            ['name' => 'Louis Vuitton Women Shoes', 'route' => 'categories.show', 'params' => ['slug' => 'louis-vuitton-women-shoes']],
            ['name' => 'Chanel Women Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'chanel-women-shoes']],
            ['name' => 'Dior Women Shoes',          'route' => 'categories.show', 'params' => ['slug' => 'dior-women-shoes']],
            ['name' => 'Gucci Women Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-women-shoes']],
            ['name' => 'Celine Women Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'celine-women-shoes']],
            ['name' => 'Prada Women Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'prada-women-shoes']],
        ],

        // ---- Clothing ----
        'Clothing' => [
            ['name' => 'All Women Clothing', 'href' => '#'],

            ['name' => 'Louis Vuitton Women Clothing', 'route' => 'categories.show', 'params' => ['slug' => 'lv-women-clothes']],
            ['name' => 'Chanel Women Clothing',        'route' => 'categories.show', 'params' => ['slug' => 'chanel-women-clothes']],
            ['name' => 'Dior Women Clothing',          'route' => 'categories.show', 'params' => ['slug' => 'dior-women-clothes']],
            ['name' => 'Gucci Women Clothing',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-women-clothes']],
        ],

        // ---- Small Leather Goods ----
        'Small Leather Goods' => [
            ['name' => 'All Women SLG', 'href' => '#'],

            ['name' => 'Wallets',      'route' => 'categories.show', 'params' => ['slug' => 'wallets']],
            ['name' => 'Card Holders', 'route' => 'categories.show', 'params' => ['slug' => 'card-holders']],
            ['name' => 'Phone Cases',  'route' => 'categories.show', 'params' => ['slug' => 'phone-cases']],
            ['name' => 'Key Chains',   'route' => 'categories.show', 'params' => ['slug' => 'key-chains']],
        ],

        // ---- Accessories ----
        'Accessories' => [
            ['name' => 'All Women Accessories', 'href' => '#'],

            ['name' => 'Scarves',             'route' => 'categories.show', 'params' => ['slug' => 'scarves']],
            ['name' => 'Hats',                'route' => 'categories.show', 'params' => ['slug' => 'hats']],
            ['name' => 'Gloves',              'route' => 'categories.show', 'params' => ['slug' => 'gloves']],
            ['name' => 'Hair Accessories',    'route' => 'categories.show', 'params' => ['slug' => 'hair-accessories']],
            ['name' => 'Home Decor',          'route' => 'categories.show', 'params' => ['slug' => 'home-decor']],
            ['name' => 'Thermos / Drinkware', 'route' => 'categories.show', 'params' => ['slug' => 'thermos']],
            ['name' => 'Luggage & Trolleys',  'route' => 'categories.show', 'params' => ['slug' => 'luggage']],
            ['name' => 'Ski Goggles',         'route' => 'categories.show', 'params' => ['slug' => 'ski-goggles']],
        ],

        // ---- Belts ----
        'Belts' => [
            ['name' => 'All Women Belts', 'href' => '#'],

            ['name' => 'Louis Vuitton Belts', 'route' => 'categories.show', 'params' => ['slug' => 'lv-belts']],
            ['name' => 'Gucci Belts',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-belts']],
            ['name' => 'YSL Belts',           'route' => 'categories.show', 'params' => ['slug' => 'ysl-belts']],
            ['name' => 'Celine Belts',        'route' => 'categories.show', 'params' => ['slug' => 'celine-belts']],
        ],

        // ---- Jewelry ----
        'Jewelry' => [
            ['name' => 'All Women Jewelry', 'href' => '#'],

            ['name' => 'Chanel Jewelry', 'route' => 'categories.show', 'params' => ['slug' => 'chanel-jewelry']],
            ['name' => 'Dior Jewelry',   'route' => 'categories.show', 'params' => ['slug' => 'dior-jewelry']],
        ],

        // ---- Glasses ----
        'Glasses' => [
            ['name' => 'All Women Glasses', 'href' => '#'],

            ['name' => 'Louis Vuitton Glasses', 'route' => 'categories.show', 'params' => ['slug' => 'lv-glasses']],
            ['name' => 'Gucci Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-glasses']],
            ['name' => 'Prada Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'prada-glasses']],
            ['name' => 'Celine Glasses',        'route' => 'categories.show', 'params' => ['slug' => 'celine-glasses']],
        ],

        // ---- Watches ----
        'Watches' => [
            ['name' => 'All Women Watches', 'href' => '#'],

            ['name' => 'Top Quality Watches', 'route' => 'categories.show', 'params' => ['slug' => 'top-quality-watches']],
        ],
    ],

    // ========================= MEN =========================
    'men' => [

        // ---- Bags ----
        'Bags' => [
            ['name' => 'All Men Bags', 'href' => '#'],

            ['name' => 'Gucci Men Bags', 'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-bags']],
        ],

        // ---- Shoes ----
        'Shoes' => [
            ['name' => 'All Men Shoes', 'href' => '#'],

            ['name' => 'Louis Vuitton Men Shoes',     'route' => 'categories.show', 'params' => ['slug' => 'lv-men-shoes']],
            ['name' => 'Chanel Men Shoes',            'route' => 'categories.show', 'params' => ['slug' => 'chanel-men-shoes']],
            ['name' => 'Dior Men Shoes',              'route' => 'categories.show', 'params' => ['slug' => 'dior-men-shoes']],
            ['name' => 'Gucci Men Shoes',             'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-shoes']],
            ['name' => 'Celine Men Shoes',            'route' => 'categories.show', 'params' => ['slug' => 'celine-men-shoes']],
            ['name' => 'Prada Men Shoes',             'route' => 'categories.show', 'params' => ['slug' => 'prada-men-shoes']],
            ['name' => 'Nike / AJ / Sports Shoes',    'route' => 'categories.show', 'params' => ['slug' => 'nike-aj-sneakers']],
        ],

        // ---- Clothing ----
        'Clothing' => [
            ['name' => 'All Men Clothing', 'href' => '#'],

            ['name' => 'Louis Vuitton Men Clothing', 'route' => 'categories.show', 'params' => ['slug' => 'lv-men-clothes']],
            ['name' => 'Chanel Men Clothing',        'route' => 'categories.show', 'params' => ['slug' => 'chanel-men-clothes']],
            ['name' => 'Dior Men Clothing',          'route' => 'categories.show', 'params' => ['slug' => 'dior-men-clothes']],
            ['name' => 'Gucci Men Clothing',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-clothes']],
            ['name' => 'Celine Men Clothing',        'route' => 'categories.show', 'params' => ['slug' => 'celine-men-clothes']],
            ['name' => 'Moncler Men Clothing',       'route' => 'categories.show', 'params' => ['slug' => 'moncler-men-clothes']],
        ],

        // ---- Small Leather Goods ----
        'Small Leather Goods' => [
            ['name' => 'All Men SLG', 'href' => '#'],

            ['name' => 'Wallets',      'route' => 'categories.show', 'params' => ['slug' => 'wallets-men']],
            ['name' => 'Card Holders', 'route' => 'categories.show', 'params' => ['slug' => 'card-holders-men']],
            ['name' => 'Phone Cases',  'route' => 'categories.show', 'params' => ['slug' => 'phone-cases-men']],
            ['name' => 'Key Chains',   'route' => 'categories.show', 'params' => ['slug' => 'key-chains-men']],
        ],

        // ---- Accessories ----
        'Accessories' => [
            ['name' => 'All Men Accessories', 'href' => '#'],

            ['name' => 'Scarves',            'route' => 'categories.show', 'params' => ['slug' => 'scarves-men']],
            ['name' => 'Hats',               'route' => 'categories.show', 'params' => ['slug' => 'hats-men']],
            ['name' => 'Gloves',             'route' => 'categories.show', 'params' => ['slug' => 'gloves-men']],
            ['name' => 'Socks',              'route' => 'categories.show', 'params' => ['slug' => 'socks-men']],
            ['name' => 'Home Decor',         'route' => 'categories.show', 'params' => ['slug' => 'home-decor-men']],
            ['name' => 'Luggage & Trolleys', 'route' => 'categories.show', 'params' => ['slug' => 'luggage-men']],
        ],

        // ---- Belts ----
        'Belts' => [
            ['name' => 'All Men Belts', 'href' => '#'],

            ['name' => 'Louis Vuitton Belts', 'route' => 'categories.show', 'params' => ['slug' => 'lv-belts-men']],
            ['name' => 'Gucci Belts',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-belts-men']],
            ['name' => 'Celine Belts',        'route' => 'categories.show', 'params' => ['slug' => 'celine-belts-men']],
            ['name' => 'Prada Belts',         'route' => 'categories.show', 'params' => ['slug' => 'prada-belts-men']],
        ],

        // ---- Jewelry ----
        'Jewelry' => [
            ['name' => 'All Men Jewelry', 'href' => '#'],

            ['name' => 'Louis Vuitton Jewelry', 'route' => 'categories.show', 'params' => ['slug' => 'lv-jewelry-men']],
            ['name' => 'Gucci Jewelry',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-jewelry-men']],
        ],

        // ---- Glasses ----
        'Glasses' => [
            ['name' => 'All Men Glasses', 'href' => '#'],

            ['name' => 'Louis Vuitton Glasses', 'route' => 'categories.show', 'params' => ['slug' => 'lv-glasses-men']],
            ['name' => 'Gucci Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-glasses-men']],
            ['name' => 'Celine Glasses',        'route' => 'categories.show', 'params' => ['slug' => 'celine-glasses-men']],
        ],

        // ---- Watches ----
        'Watches' => [
            ['name' => 'All Men Watches', 'href' => '#'],

            ['name' => 'Top Quality Watches', 'route' => 'categories.show', 'params' => ['slug' => 'top-quality-watches-men']],
        ],
    ],
];
