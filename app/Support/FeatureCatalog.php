<?php

namespace App\Support;

class FeatureCatalog
{
    /**
     * @return list<array{
     *     id: string,
     *     name: string,
     *     number: string,
     *     eyebrow: string,
     *     icon: string,
     *     lead: string,
     *     tagline: string,
     *     quote: string,
     *     cover: string,
     *     banner: string,
     *     motifs: list<array{icon: string, label: string}>,
     *     articles: list<array{
     *         title: string,
     *         excerpt: string,
     *         tag: string,
     *         date: string,
     *         date_label: string,
     *         image: string,
     *         href: string
     *     }>
     * }>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'art-culture',
                'name' => 'Art & Culture',
                'number' => '01',
                'eyebrow' => 'Chapter 01',
                'icon' => 'fa-landmark',
                'lead' => 'Museums, makers, and the living archives of place.',
                'tagline' => 'Rooted in heritage. Expressed in a thousand ways.',
                'quote' => 'Heritage is not a relic — it is a practice of looking closely.',
                'cover' => 'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?q=80&w=1400',
                'banner' => 'images/features/categories/art-culture.png',
                'motifs' => [
                    ['icon' => 'fa-landmark', 'label' => 'Museums & Archives'],
                    ['icon' => 'fa-hands', 'label' => 'Living Craft'],
                    ['icon' => 'fa-om', 'label' => 'Sacred Forms'],
                    ['icon' => 'fa-palette', 'label' => 'Gallery Light'],
                ],
                'articles' => [
                    [
                        'title' => 'The Quiet Architecture of Old Courtyards',
                        'excerpt' => 'Stone, shade, and the soft politics of gathering — reading cities through their thresholds.',
                        'tag' => 'Essay',
                        'date' => '2026-06-12',
                        'date_label' => '12 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Ink, Cloth, and the Hands That Keep Them',
                        'excerpt' => 'A morning with artisans whose craft still refuses the rush of the market.',
                        'tag' => 'Profile',
                        'date' => '2026-05-03',
                        'date_label' => '03 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Gallery Light After Rain',
                        'excerpt' => 'What a washed sky does to colour on plaster walls.',
                        'tag' => 'Dispatch',
                        'date' => '2026-03-18',
                        'date_label' => '18 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'experiences',
                'name' => 'Experiences',
                'number' => '02',
                'eyebrow' => 'Chapter 02',
                'icon' => 'fa-mountain-sun',
                'lead' => 'Journeys you feel in the body — trails, rituals, and days that rearrange you.',
                'tagline' => 'Moments that stay. Stories that inspire.',
                'quote' => 'The best itinerary is the one that leaves room for wonder.',
                'cover' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1400',
                'banner' => 'images/features/categories/experiences.png',
                'motifs' => [
                    ['icon' => 'fa-location-dot', 'label' => 'Cultural Encounters'],
                    ['icon' => 'fa-camera', 'label' => 'Hidden Gems'],
                    ['icon' => 'fa-compass', 'label' => 'Local Stories'],
                    ['icon' => 'fa-mountain', 'label' => 'Adventures That Matter'],
                ],
                'articles' => [
                    [
                        'title' => 'Dawn on the Ridge Before the Buses Arrive',
                        'excerpt' => 'A walk timed to mist, silence, and the first tea stall of the day.',
                        'tag' => 'Travel',
                        'date' => '2026-06-01',
                        'date_label' => '01 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Learning the River by Canoe',
                        'excerpt' => 'Current, patience, and the small courage of leaving the bank.',
                        'tag' => 'Field note',
                        'date' => '2026-04-22',
                        'date_label' => '22 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'A Night Market That Refuses Maps',
                        'excerpt' => 'Follow the smell of charcoal and citrus — the route writes itself.',
                        'tag' => 'Wander',
                        'date' => '2026-02-14',
                        'date_label' => '14 Feb 2026',
                        'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'on-a-budget',
                'name' => 'On A Budget',
                'number' => '03',
                'eyebrow' => 'Chapter 03',
                'icon' => 'fa-wallet',
                'lead' => 'Rich days on thrifty means — trains, hostels, and meals that still matter.',
                'tagline' => 'Smart choices. Richer experiences.',
                'quote' => 'Collect moments, not things — and stretch every rupee with curiosity.',
                'cover' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1400',
                'banner' => 'images/features/categories/on-a-budget.png',
                'motifs' => [
                    ['icon' => 'fa-piggy-bank', 'label' => 'Save More Travel More'],
                    ['icon' => 'fa-suitcase', 'label' => 'Budget Friendly Stays'],
                    ['icon' => 'fa-location-dot', 'label' => 'Local Experiences'],
                    ['icon' => 'fa-utensils', 'label' => 'Eat Well Spend Less'],
                    ['icon' => 'fa-signs-post', 'label' => 'Plan Better Go Further'],
                ],
                'articles' => [
                    [
                        'title' => 'Three Cities, One Rail Pass, Soft Mornings',
                        'excerpt' => 'How to stretch a week without stretching the wallet — or the spirit.',
                        'tag' => 'Guide',
                        'date' => '2026-05-20',
                        'date_label' => '20 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'The Art of the Shared Table',
                        'excerpt' => 'Street stalls, set menus, and why the cheapest seat is often the best.',
                        'tag' => 'Food',
                        'date' => '2026-04-08',
                        'date_label' => '08 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Hostel Lobbies as Living Rooms',
                        'excerpt' => 'Where strangers become itineraries, and itineraries become friends.',
                        'tag' => 'Notes',
                        'date' => '2026-01-29',
                        'date_label' => '29 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Free Museums and the Long Lunch Break',
                        'excerpt' => 'When admission is zero, the afternoon belongs to you — and a shared sandwich.',
                        'tag' => 'Tip',
                        'date' => '2026-01-08',
                        'date_label' => '08 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'luxury-escapes',
                'name' => 'Luxury Escapes',
                'number' => '04',
                'eyebrow' => 'Chapter 04',
                'icon' => 'fa-gem',
                'lead' => 'Slow opulence — rooms with hush, baths with views, service that disappears.',
                'tagline' => 'Curated journeys. Extraordinary memories.',
                'quote' => 'True luxury is an empty afternoon that asks nothing of you.',
                'cover' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=1400',
                'banner' => 'images/features/categories/luxury-escapes.png',
                'motifs' => [
                    ['icon' => 'fa-gem', 'label' => 'Exclusive Destinations'],
                    ['icon' => 'fa-bell-concierge', 'label' => 'World Class Stays'],
                    ['icon' => 'fa-compass', 'label' => 'Curated Experiences'],
                    ['icon' => 'fa-crown', 'label' => 'Timeless Indulgence'],
                ],
                'articles' => [
                    [
                        'title' => 'A Villa That Listens to the Sea',
                        'excerpt' => 'Linen, salt air, and the rare luxury of an empty afternoon.',
                        'tag' => 'Stay',
                        'date' => '2026-06-18',
                        'date_label' => '18 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Spa Hours Measured in Steam',
                        'excerpt' => 'Thermal water, cedar, and the discipline of doing nothing well.',
                        'tag' => 'Wellness',
                        'date' => '2026-03-30',
                        'date_label' => '30 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Dining When the Lights Are Only Candles',
                        'excerpt' => 'A tasting menu that refuses spectacle — and still dazzles.',
                        'tag' => 'Table',
                        'date' => '2026-02-05',
                        'date_label' => '05 Feb 2026',
                        'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Private Courtyards After Midnight',
                        'excerpt' => 'A key, a quiet pool, and stars that feel arranged for you alone.',
                        'tag' => 'Retreat',
                        'date' => '2026-01-12',
                        'date_label' => '12 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'global-chapters',
                'name' => 'Global Chapters',
                'number' => '05',
                'eyebrow' => 'Chapter 05',
                'icon' => 'fa-globe',
                'lead' => 'Dispatches from elsewhere — cities, coastlines, and the people who hold them.',
                'tagline' => 'Stories from around the world. Cultures that connect us.',
                'quote' => 'Every city is a chapter — turn the page carefully.',
                'cover' => 'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?q=80&w=1400',
                'banner' => 'images/features/categories/global-chapters.png',
                'motifs' => [
                    ['icon' => 'fa-globe', 'label' => 'Diverse Cultures'],
                    ['icon' => 'fa-passport', 'label' => 'Voices from Every Corner'],
                    ['icon' => 'fa-signs-post', 'label' => 'Beyond Borders'],
                    ['icon' => 'fa-camera', 'label' => 'Places That Inspire'],
                    ['icon' => 'fa-people-group', 'label' => 'One World, Many Stories'],
                ],
                'articles' => [
                    [
                        'title' => 'Lisbon Light on Tiled Afternoons',
                        'excerpt' => 'Trams, azulejos, and the soft insistence of Atlantic weather.',
                        'tag' => 'City',
                        'date' => '2026-05-11',
                        'date_label' => '11 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Kyoto Before the Crowds Find the Alley',
                        'excerpt' => 'Wooden doors, morning incense, and a map folded smaller.',
                        'tag' => 'Dispatch',
                        'date' => '2026-04-02',
                        'date_label' => '02 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Marrakech at the Hour of Oranges',
                        'excerpt' => 'Colour as compass — reading a medina by scent and sound.',
                        'tag' => 'Travel',
                        'date' => '2026-01-16',
                        'date_label' => '16 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Istanbul Between Two Continents',
                        'excerpt' => 'Ferry horns, spice stalls, and a skyline that keeps rewriting itself.',
                        'tag' => 'City',
                        'date' => '2025-12-04',
                        'date_label' => '04 Dec 2025',
                        'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'not-on-the-atlas',
                'name' => 'Not On The Atlas',
                'number' => '06',
                'eyebrow' => 'Chapter 06',
                'icon' => 'fa-location-dot',
                'lead' => 'Places the guidebooks skim — villages, detours, and names you learn by asking.',
                'tagline' => 'Hidden places. Untold stories.',
                'quote' => 'Go where the map ends — not all who wander are lost.',
                'cover' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1400',
                'banner' => 'images/features/categories/not-on-the-atlas.png',
                'motifs' => [
                    ['icon' => 'fa-tree', 'label' => 'Offbeat Destinations'],
                    ['icon' => 'fa-compass', 'label' => 'Unknown Journeys'],
                    ['icon' => 'fa-map', 'label' => 'Beyond The Map'],
                    ['icon' => 'fa-camera', 'label' => 'Stories Untold'],
                ],
                'articles' => [
                    [
                        'title' => 'The Village That Marks Time by Harvest',
                        'excerpt' => 'No railway station — only a bus, a square, and evenings that linger.',
                        'tag' => 'Hidden',
                        'date' => '2026-06-08',
                        'date_label' => '08 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'A Coast Without Signage',
                        'excerpt' => 'Follow the fishermen’s path until the cliffs open like a secret.',
                        'tag' => 'Detour',
                        'date' => '2026-03-12',
                        'date_label' => '12 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Tea Houses Past the Last Streetlamp',
                        'excerpt' => 'Where the road softens and conversation becomes the only map.',
                        'tag' => 'Essay',
                        'date' => '2026-02-20',
                        'date_label' => '20 Feb 2026',
                        'image' => 'https://images.unsplash.com/photo-1528164344705-47542687000d?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'vineyard-tales',
                'name' => 'Vineyard Tales',
                'number' => '07',
                'eyebrow' => 'Chapter 07',
                'icon' => 'fa-wine-glass',
                'lead' => 'Rows of vines, cellar hush, and the stories that age alongside the bottles.',
                'tagline' => 'Sip. Savor. Stories rooted in the soil.',
                'quote' => 'Some of the best stories start with a glass of wine.',
                'cover' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=1400',
                'banner' => 'images/features/categories/vineyard-tales.png',
                'motifs' => [
                    ['icon' => 'fa-wine-bottle', 'label' => 'Fine Wines & Vineyards'],
                    ['icon' => 'fa-wine-glass', 'label' => 'Behind The Vines'],
                    ['icon' => 'fa-leaf', 'label' => 'Local Flavors & Traditions'],
                    ['icon' => 'fa-camera', 'label' => 'Journeys Worth Sharing'],
                ],
                'articles' => [
                    [
                        'title' => 'Autumn in a Family Cellar',
                        'excerpt' => 'Crush, oak, and the patience of people who measure years by vintage.',
                        'tag' => 'Wine',
                        'date' => '2026-05-28',
                        'date_label' => '28 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Walking the Rows at First Light',
                        'excerpt' => 'Dew on leaves, dogs at the gate, and a tasting that begins before breakfast.',
                        'tag' => 'Field',
                        'date' => '2026-04-15',
                        'date_label' => '15 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1474722883778-792e7990302f?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Notes from a Long Lunch Under the Arbor',
                        'excerpt' => 'Bread, cheese, and a bottle that refuses to be rushed.',
                        'tag' => 'Table',
                        'date' => '2026-03-01',
                        'date_label' => '01 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1510812431401-41d2bd2722f3?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
            [
                'id' => 'coffee-classics',
                'name' => 'Coffee & Classics',
                'number' => '08',
                'eyebrow' => 'Chapter 08',
                'icon' => 'fa-mug-hot',
                'lead' => 'Cups, books, and the rituals that make a morning feel like literature.',
                'tagline' => 'Timeless stories. Perfectly brewed moments.',
                'quote' => 'Good coffee. Great books. Better days.',
                'cover' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1400',
                'banner' => 'images/features/categories/coffee-classics.png',
                'motifs' => [
                    ['icon' => 'fa-mug-hot', 'label' => 'Rich Coffee'],
                    ['icon' => 'fa-book', 'label' => 'Great Books'],
                    ['icon' => 'fa-feather', 'label' => 'Iconic Authors'],
                    ['icon' => 'fa-chair', 'label' => 'Quiet Corners'],
                    ['icon' => 'fa-clock', 'label' => 'Time Well Spent'],
                ],
                'articles' => [
                    [
                        'title' => 'Brewing Between Pages on a Rainy Desk',
                        'excerpt' => 'Steam, serifs, and why some books only open properly with a mug beside them.',
                        'tag' => 'Essay',
                        'date' => '2026-06-22',
                        'date_label' => '22 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'The Classics That Travel Well',
                        'excerpt' => 'Paperbacks for trains — thin spines, thick weather, and lines that stay.',
                        'tag' => 'Reading',
                        'date' => '2026-04-28',
                        'date_label' => '28 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Café Corners That Still Feel Like Home',
                        'excerpt' => 'Worn wood, familiar baristas, and the luxury of being known by your order.',
                        'tag' => 'Notes',
                        'date' => '2026-02-11',
                        'date_label' => '11 Feb 2026',
                        'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=900',
                        'href' => '#',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_column(self::all(), 'id');
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     number: string,
     *     eyebrow: string,
     *     icon: string,
     *     lead: string,
     *     tagline: string,
     *     quote: string,
     *     cover: string,
     *     banner: string,
     *     motifs: list<array{icon: string, label: string}>,
     *     articles: list<array<string, string>>
     * }|null
     */
    public static function find(string $slug): ?array
    {
        foreach (self::all() as $category) {
            if ($category['id'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Pull a curated edition of articles from across every chapter and
     * arrange them the way the journal arranges its front page: the main
     * section carries exactly one article per chapter — one "Article of the
     * day" lead plus a feature card for every other chapter — while the
     * sidebar fills with whatever wasn't used, newest first.
     *
     * @return list<array{
     *     role: string,
     *     tag: string,
     *     category_id: string,
     *     category_name: string,
     *     title: string,
     *     excerpt: string,
     *     date: string,
     *     date_label: string,
     *     image: string,
     *     href: string
     * }>
     */
    public static function edition(): array
    {
        $categories = collect(self::all());

        $main = $categories->map(fn (array $category): array => [
            ...$category['articles'][0],
            'category_id' => $category['id'],
            'category_name' => $category['name'],
        ]);

        $lead = [...$main->first(), 'role' => 'lead'];

        $features = $main->skip(1)
            ->map(fn (array $entry): array => [...$entry, 'role' => 'feature'])
            ->values();

        // Whatever wasn't chosen for the main section fills the sidebar,
        // most recently dated first.
        $leftovers = $categories
            ->flatMap(fn (array $category): array => collect($category['articles'])
                ->skip(1)
                ->map(fn (array $article): array => [
                    ...$article,
                    'category_id' => $category['id'],
                    'category_name' => $category['name'],
                ])
                ->all())
            ->sortByDesc('date')
            ->values();

        $columns = $leftovers->take(2)
            ->map(fn (array $entry): array => [...$entry, 'role' => 'column']);

        $briefs = $leftovers->slice(2, 2)
            ->map(fn (array $entry): array => [...$entry, 'role' => 'brief']);

        // One more leftover article closes out the sidebar in place of a
        // static pullquote — its own heading and excerpt, not fixed copy.
        $spotlight = $leftovers->slice(4, 1)
            ->map(fn (array $entry): array => [...$entry, 'role' => 'spotlight']);

        return collect([$lead])
            ->concat($features)
            ->concat($columns)
            ->concat($briefs)
            ->concat($spotlight)
            ->all();
    }
}
