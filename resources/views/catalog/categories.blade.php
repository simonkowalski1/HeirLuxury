<?php
/**
 * HeirLuxury catalog taxonomy (route-friendly)
 *
 * Store only route names + params OR a direct href for each leaf link.
 * Blade will compute URLs (route() with try/catch, or url()/absolute href).
 *
 * Requires route: categories.show  -> /categories/{slug}
 */

return [

    // ===== WOMEN =====
    'women' => [

        'Bags' => [
            ['name' => 'Hermès Bags',            'route' => 'categories.show', 'params' => ['slug' => 'hermes-bags']],
            ['name' => 'Louis Vuitton Bags',     'route' => 'categories.show', 'params' => ['slug' => 'lv-bags']],
            ['name' => 'Chanel Bags',            'route' => 'categories.show', 'params' => ['slug' => 'chanel-bags']],
            ['name' => 'Dior Bags',              'route' => 'categories.show', 'params' => ['slug' => 'dior-bags']],
            ['name' => 'Gucci Bags',             'route' => 'categories.show', 'params' => ['slug' => 'gucci-bags']],
            ['name' => 'Fendi Bags',             'route' => 'categories.show', 'params' => ['slug' => 'fendi-bags']],
            ['name' => 'Celine Bags',            'route' => 'categories.show', 'params' => ['slug' => 'celine-bags']],
            ['name' => 'Prada Bags',             'route' => 'categories.show', 'params' => ['slug' => 'prada-bags']],
            ['name' => 'YSL Bags',               'route' => 'categories.show', 'params' => ['slug' => 'ysl-bags']],
            ['name' => 'Bottega Veneta Bags',    'route' => 'categories.show', 'params' => ['slug' => 'bottega-veneta-bags']],
            ['name' => 'Loewe Bags',             'route' => 'categories.show', 'params' => ['slug' => 'loewe-bags']],
            ['name' => 'Goyard Bags',            'route' => 'categories.show', 'params' => ['slug' => 'goyard-bags']],
            ['name' => 'Valentino Bags',         'route' => 'categories.show', 'params' => ['slug' => 'valentino-bags']],
            ['name' => 'Burberry Bags',          'route' => 'categories.show', 'params' => ['slug' => 'burberry-bags']],
            ['name' => 'Jacquemus Bags',         'route' => 'categories.show', 'params' => ['slug' => 'jacquemus-bags']],
            ['name' => 'Alaia Bags',             'route' => 'categories.show', 'params' => ['slug' => 'alaia-bags']],
            ['name' => 'Delvaux Bags',           'route' => 'categories.show', 'params' => ['slug' => 'delvaux-bags']],
            ['name' => 'Loro Piana Bags',        'route' => 'categories.show', 'params' => ['slug' => 'loro-piana-bags']],
        ],

        'Shoes' => [
            ['name' => 'Hermès Women Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'hermes-women-shoes']],
            ['name' => 'Louis Vuitton Women Shoes','route' => 'categories.show', 'params' => ['slug' => 'lv-women-shoes']],
            ['name' => 'Chanel Women Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'chanel-women-shoes']],
            ['name' => 'Dior Women Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'dior-women-shoes']],
            ['name' => 'Gucci Women Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'gucci-women-shoes']],
            ['name' => 'Fendi Women Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'fendi-women-shoes']],
            ['name' => 'Celine Women Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'celine-women-shoes']],
            ['name' => 'Prada Women Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'prada-women-shoes']],
            ['name' => 'YSL Women Shoes',          'route' => 'categories.show', 'params' => ['slug' => 'ysl-women-shoes']],
            ['name' => 'Miu Miu Women Shoes',      'route' => 'categories.show', 'params' => ['slug' => 'miu-miu-women-shoes']],
            ['name' => 'Amina Muaddi Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'amina-muaddi-shoes']],
            ['name' => 'Jimmy Choo Women Shoes',   'route' => 'categories.show', 'params' => ['slug' => 'jimmy-choo-women-shoes']],
            ['name' => 'Christian Louboutin Women Shoes', 'route' => 'categories.show', 'params' => ['slug' => 'louboutin-women-shoes']],
            ['name' => 'Aquazzura Shoes',          'route' => 'categories.show', 'params' => ['slug' => 'aquazzura-shoes']],
            ['name' => 'Golden Goose Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'golden-goose-shoes']],
        ],

        'Clothing' => [
            ['name' => 'Hermès Women Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'hermes-women-clothes']],
            ['name' => 'Louis Vuitton Women Clothing','route'=> 'categories.show', 'params' => ['slug' => 'lv-women-clothes']],
            ['name' => 'Chanel Women Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'chanel-women-clothes']],
            ['name' => 'Dior Women Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'dior-women-clothes']],
            ['name' => 'Gucci Women Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'gucci-women-clothes']],
            ['name' => 'Fendi Women Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'fendi-women-clothes']],
            ['name' => 'Celine Women Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'celine-women-clothes']],
            ['name' => 'Prada Women Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'prada-women-clothes']],
            ['name' => 'YSL Women Clothing',       'route' => 'categories.show', 'params' => ['slug' => 'ysl-women-clothes']],
            ['name' => 'Miu Miu Women Clothing',   'route' => 'categories.show', 'params' => ['slug' => 'miu-miu-women-clothes']],
            ['name' => 'Loro Piana Women Clothing','route'=> 'categories.show', 'params' => ['slug' => 'loro-piana-women-clothes']],
            ['name' => 'Zimmermann Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'zimmermann-women-clothes']],
            ['name' => 'The Row Women Clothing',   'route' => 'categories.show', 'params' => ['slug' => 'the-row-women-clothes']],
            ['name' => 'MM6 Women Clothing',       'route' => 'categories.show', 'params' => ['slug' => 'mm6-women-clothes']],
            ['name' => 'Max Mara Clothing',        'route' => 'categories.show', 'params' => ['slug' => 'max-mara-women-clothes']],
            ['name' => 'Alaia Women Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'alaia-women-clothes']],
        ],

        'Small Leather Goods' => [
            ['name' => 'Wallets',                  'route' => 'categories.show', 'params' => ['slug' => 'wallets']],
            ['name' => 'Card Holders',             'route' => 'categories.show', 'params' => ['slug' => 'card-holders']],
            ['name' => 'Phone Cases',              'route' => 'categories.show', 'params' => ['slug' => 'phone-cases']],
            ['name' => 'Key Chains',               'route' => 'categories.show', 'params' => ['slug' => 'key-chains']],
        ],

        'Accessories' => [
            ['name' => 'Scarves',                  'route' => 'categories.show', 'params' => ['slug' => 'scarves']],
            ['name' => 'Hats',                     'route' => 'categories.show', 'params' => ['slug' => 'hats']],
            ['name' => 'Gloves',                   'route' => 'categories.show', 'params' => ['slug' => 'gloves']],
            ['name' => 'Hair Accessories',         'route' => 'categories.show', 'params' => ['slug' => 'hair-accessories']],
            ['name' => 'Home Decor',               'route' => 'categories.show', 'params' => ['slug' => 'home-decor']],
            ['name' => 'Thermos / Drinkware',      'route' => 'categories.show', 'params' => ['slug' => 'thermos']],
            ['name' => 'Luggage & Trolleys',       'route' => 'categories.show', 'params' => ['slug' => 'luggage']],
            ['name' => 'Ski Goggles',              'route' => 'categories.show', 'params' => ['slug' => 'ski-goggles']],
        ],

        'Belts' => [
            ['name' => 'Hermès Belts',             'route' => 'categories.show', 'params' => ['slug' => 'hermes-belts']],
            ['name' => 'Louis Vuitton Belts',      'route' => 'categories.show', 'params' => ['slug' => 'lv-belts']],
            ['name' => 'Gucci Belts',              'route' => 'categories.show', 'params' => ['slug' => 'gucci-belts']],
            ['name' => 'YSL Belts',                'route' => 'categories.show', 'params' => ['slug' => 'ysl-belts']],
            ['name' => 'Celine Belts',             'route' => 'categories.show', 'params' => ['slug' => 'celine-belts']],
            ['name' => 'Prada Belts',              'route' => 'categories.show', 'params' => ['slug' => 'prada-belts']],
            ['name' => 'Tom Ford Belts',           'route' => 'categories.show', 'params' => ['slug' => 'tom-ford-belts']],
            ['name' => 'Valentino Belts',          'route' => 'categories.show', 'params' => ['slug' => 'valentino-belts']],
        ],

        'Jewelry' => [
            ['name' => 'Cartier Jewelry',          'route' => 'categories.show', 'params' => ['slug' => 'cartier-jewelry']],
            ['name' => 'Van Cleef & Arpels',       'route' => 'categories.show', 'params' => ['slug' => 'vca-jewelry']],
            ['name' => 'Tiffany & Co.',            'route' => 'categories.show', 'params' => ['slug' => 'tiffany-jewelry']],
            ['name' => 'Bvlgari Jewelry',          'route' => 'categories.show', 'params' => ['slug' => 'bvlgari-jewelry']],
            ['name' => 'Chanel Jewelry',           'route' => 'categories.show', 'params' => ['slug' => 'chanel-jewelry']],
            ['name' => 'Dior Jewelry',             'route' => 'categories.show', 'params' => ['slug' => 'dior-jewelry']],
            ['name' => 'Messika Jewelry',          'route' => 'categories.show', 'params' => ['slug' => 'messika-jewelry']],
            ['name' => 'Qeelin Jewelry',           'route' => 'categories.show', 'params' => ['slug' => 'qeelin-jewelry']],
            ['name' => 'Vivienne Westwood Jewelry','route' => 'categories.show', 'params' => ['slug' => 'vivienne-westwood-jewelry']],
        ],

        'Glasses' => [
            ['name' => 'Louis Vuitton Glasses',    'route' => 'categories.show', 'params' => ['slug' => 'lv-glasses']],
            ['name' => 'Gucci Glasses',            'route' => 'categories.show', 'params' => ['slug' => 'gucci-glasses']],
            ['name' => 'YSL Glasses',              'route' => 'categories.show', 'params' => ['slug' => 'ysl-glasses']],
            ['name' => 'Prada Glasses',            'route' => 'categories.show', 'params' => ['slug' => 'prada-glasses']],
            ['name' => 'Celine Glasses',           'route' => 'categories.show', 'params' => ['slug' => 'celine-glasses']],
            ['name' => 'Bvlgari Glasses',          'route' => 'categories.show', 'params' => ['slug' => 'bvlgari-glasses']],
            ['name' => 'Loewe Glasses',            'route' => 'categories.show', 'params' => ['slug' => 'loewe-glasses']],
            ['name' => 'Tom Ford Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'tom-ford-glasses']],
            ['name' => 'Miu Miu Glasses',          'route' => 'categories.show', 'params' => ['slug' => 'miu-miu-glasses']],
            ['name' => 'DITA Glasses',             'route' => 'categories.show', 'params' => ['slug' => 'dita-glasses']],
            ['name' => 'Montblanc Glasses',        'route' => 'categories.show', 'params' => ['slug' => 'montblanc-glasses']],
            ['name' => 'Maybach Glasses',          'route' => 'categories.show', 'params' => ['slug' => 'maybach-glasses']],
            ['name' => 'Beckham Glasses',          'route' => 'categories.show', 'params' => ['slug' => 'beckham-glasses']],
        ],

        'Watches' => [
            ['name' => 'Top Quality Watches',      'route' => 'categories.show', 'params' => ['slug' => 'top-quality-watches']],
        ],
    ],

    // ===== MEN =====
    'men' => [

        'Bags' => [
            ['name' => 'Louis Vuitton Men Bags',  'route' => 'categories.show', 'params' => ['slug' => 'lv-men-bags']],
            ['name' => 'Gucci Men Bags',          'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-bags']],
            ['name' => 'Prada Men Bags',          'route' => 'categories.show', 'params' => ['slug' => 'prada-men-bags']],
            ['name' => 'Burberry Men Bags',       'route' => 'categories.show', 'params' => ['slug' => 'burberry-men-bags']],
            ['name' => 'Fendi Men Bags',          'route' => 'categories.show', 'params' => ['slug' => 'fendi-men-bags']],
            ['name' => 'Bottega Veneta Men Bags', 'route' => 'categories.show', 'params' => ['slug' => 'bottega-veneta-men-bags']],
            ['name' => 'YSL Men Bags',            'route' => 'categories.show', 'params' => ['slug' => 'ysl-men-bags']],
            ['name' => 'Loewe Men Bags',          'route' => 'categories.show', 'params' => ['slug' => 'loewe-men-bags']],
            ['name' => 'Loro Piana Men Bags',     'route' => 'categories.show', 'params' => ['slug' => 'loro-piana-men-bags']],
        ],

        'Shoes' => [
            ['name' => 'Hermès Men Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'hermes-men-shoes']],
            ['name' => 'Louis Vuitton Men Shoes', 'route' => 'categories.show', 'params' => ['slug' => 'lv-men-shoes']],
            ['name' => 'Chanel Men Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'chanel-men-shoes']],
            ['name' => 'Dior Men Shoes',          'route' => 'categories.show', 'params' => ['slug' => 'dior-men-shoes']],
            ['name' => 'Gucci Men Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-shoes']],
            ['name' => 'Fendi Men Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'fendi-men-shoes']],
            ['name' => 'Celine Men Shoes',        'route' => 'categories.show', 'params' => ['slug' => 'celine-men-shoes']],
            ['name' => 'Prada Men Shoes',         'route' => 'categories.show', 'params' => ['slug' => 'prada-men-shoes']],
            ['name' => 'YSL Men Shoes',           'route' => 'categories.show', 'params' => ['slug' => 'ysl-men-shoes']],
            ['name' => 'Berluti Men Shoes',       'route' => 'categories.show', 'params' => ['slug' => 'berluti-men-shoes']],
            ['name' => 'Ferragamo Men Shoes',     'route' => 'categories.show', 'params' => ['slug' => 'ferragamo-men-shoes']],
            ['name' => 'Alexander McQueen Men Shoes', 'route' => 'categories.show', 'params' => ['slug' => 'mcqueen-men-shoes']],
            ['name' => 'Golden Goose Men Shoes',  'route' => 'categories.show', 'params' => ['slug' => 'golden-goose-men-shoes']],
            ['name' => 'Nike / AJ / Sports Shoes','route' => 'categories.show', 'params' => ['slug' => 'nike-aj-sneakers']],
            ['name' => 'Yeezy',                   'route' => 'categories.show', 'params' => ['slug' => 'yeezy']],
        ],

        'Clothing' => [
            ['name' => 'Hermès Men Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'hermes-men-clothes']],
            ['name' => 'Louis Vuitton Men Clothing','route'=> 'categories.show', 'params' => ['slug' => 'lv-men-clothes']],
            ['name' => 'Chanel Men Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'chanel-men-clothes']],
            ['name' => 'Dior Men Clothing',       'route' => 'categories.show', 'params' => ['slug' => 'dior-men-clothes']],
            ['name' => 'Gucci Men Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'gucci-men-clothes']],
            ['name' => 'Fendi Men Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'fendi-men-clothes']],
            ['name' => 'Celine Men Clothing',     'route' => 'categories.show', 'params' => ['slug' => 'celine-men-clothes']],
            ['name' => 'Prada Men Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'prada-men-clothes']],
            ['name' => 'YSL Men Clothing',        'route' => 'categories.show', 'params' => ['slug' => 'ysl-men-clothes']],
            ['name' => 'Loro Piana Men Clothing', 'route' => 'categories.show', 'params' => ['slug' => 'loro-piana-men-clothes']],
            ['name' => 'Zegna Men Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'zegna-men-clothes']],
            ['name' => 'Moncler Men Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'moncler-men-clothes']],
            ['name' => 'Amiri Men Clothing',      'route' => 'categories.show', 'params' => ['slug' => 'amiri-men-clothes']],
            ['name' => 'Balmain Men Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'balmain-men-clothes']],
            ['name' => 'Stone Island Clothing',   'route' => 'categories.show', 'params' => ['slug' => 'stone-island-clothes']],
            ['name' => 'Palm Angels Clothing',    'route' => 'categories.show', 'params' => ['slug' => 'palm-angels-clothes']],
        ],

        'Small Leather Goods' => [
            ['name' => 'Wallets',                 'route' => 'categories.show', 'params' => ['slug' => 'wallets-men']],
            ['name' => 'Card Holders',            'route' => 'categories.show', 'params' => ['slug' => 'card-holders-men']],
            ['name' => 'Phone Cases',             'route' => 'categories.show', 'params' => ['slug' => 'phone-cases-men']],
            ['name' => 'Key Chains',              'route' => 'categories.show', 'params' => ['slug' => 'key-chains-men']],
        ],

        'Accessories' => [
            ['name' => 'Scarves',                 'route' => 'categories.show', 'params' => ['slug' => 'scarves-men']],
            ['name' => 'Hats',                    'route' => 'categories.show', 'params' => ['slug' => 'hats-men']],
            ['name' => 'Gloves',                  'route' => 'categories.show', 'params' => ['slug' => 'gloves-men']],
            ['name' => 'Socks',                   'route' => 'categories.show', 'params' => ['slug' => 'socks-men']],
            ['name' => 'Home Decor',              'route' => 'categories.show', 'params' => ['slug' => 'home-decor-men']],
            ['name' => 'Luggage & Trolleys',      'route' => 'categories.show', 'params' => ['slug' => 'luggage-men']],
        ],

        'Belts' => [
            ['name' => 'Hermès Belts',            'route' => 'categories.show', 'params' => ['slug' => 'hermes-belts-men']],
            ['name' => 'Louis Vuitton Belts',     'route' => 'categories.show', 'params' => ['slug' => 'lv-belts-men']],
            ['name' => 'Gucci Belts',             'route' => 'categories.show', 'params' => ['slug' => 'gucci-belts-men']],
            ['name' => 'YSL Belts',               'route' => 'categories.show', 'params' => ['slug' => 'ysl-belts-men']],
            ['name' => 'Celine Belts',            'route' => 'categories.show', 'params' => ['slug' => 'celine-belts-men']],
            ['name' => 'Prada Belts',             'route' => 'categories.show', 'params' => ['slug' => 'prada-belts-men']],
            ['name' => 'Tom Ford Belts',          'route' => 'categories.show', 'params' => ['slug' => 'tom-ford-belts-men']],
            ['name' => 'Zegna Belts',             'route' => 'categories.show', 'params' => ['slug' => 'zegna-belts']],
        ],

        'Jewelry' => [
            ['name' => 'Cartier Jewelry',         'route' => 'categories.show', 'params' => ['slug' => 'cartier-jewelry-men']],
            ['name' => 'Bvlgari Jewelry',         'route' => 'categories.show', 'params' => ['slug' => 'bvlgari-jewelry-men']],
            ['name' => 'Louis Vuitton Jewelry',   'route' => 'categories.show', 'params' => ['slug' => 'lv-jewelry-men']],
            ['name' => 'Gucci Jewelry',           'route' => 'categories.show', 'params' => ['slug' => 'gucci-jewelry-men']],
            ['name' => 'Chrome Hearts Jewelry',   'route' => 'categories.show', 'params' => ['slug' => 'chrome-hearts-jewelry']],
        ],

        'Glasses' => [
            ['name' => 'Louis Vuitton Glasses',   'route' => 'categories.show', 'params' => ['slug' => 'lv-glasses-men']],
            ['name' => 'Gucci Glasses',           'route' => 'categories.show', 'params' => ['slug' => 'gucci-glasses-men']],
            ['name' => 'YSL Glasses',             'route' => 'categories.show', 'params' => ['slug' => 'ysl-glasses-men']],
            ['name' => 'Prada Glasses',           'route' => 'categories.show', 'params' => ['slug' => 'prada-glasses-men']],
            ['name' => 'Celine Glasses',          'route' => 'categories.show', 'params' => ['slug' => 'celine-glasses-men']],
            ['name' => 'Bvlgari Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'bvlgari-glasses-men']],
            ['name' => 'Tom Ford Glasses',        'route' => 'categories.show', 'params' => ['slug' => 'tom-ford-glasses-men']],
            ['name' => 'Montblanc Glasses',       'route' => 'categories.show', 'params' => ['slug' => 'montblanc-glasses-men']],
            ['name' => 'Maybach Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'maybach-glasses-men']],
            ['name' => 'Beckham Glasses',         'route' => 'categories.show', 'params' => ['slug' => 'beckham-glasses-men']],
            ['name' => 'DITA Glasses',            'route' => 'categories.show', 'params' => ['slug' => 'dita-glasses-men']],
        ],

        'Watches' => [
            ['name' => 'Top Quality Watches',     'route' => 'categories.show', 'params' => ['slug' => 'top-quality-watches-men']],
        ],
    ],
];
