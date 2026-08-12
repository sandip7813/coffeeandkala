<?php

namespace App\Support;

use Illuminate\Support\Str;

class JournalCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return self::withEntryRouting(self::rawEntries());
    }

    /**
     * Assigns a stable, unique (per category) slug to every entry and points
     * its `href` at the entry detail route.
     *
     * @param  list<array<string, mixed>>  $entries
     * @return list<array<string, mixed>>
     */
    private static function withEntryRouting(array $entries): array
    {
        $seen = [];

        return array_map(function (array $entry) use (&$seen): array {
            $base = Str::slug($entry['title']);
            $slug = $base;

            for ($suffix = 2; isset($seen[$entry['category_id']][$slug]); $suffix++) {
                $slug = "{$base}-{$suffix}";
            }

            $seen[$entry['category_id']][$slug] = true;

            return [
                ...$entry,
                'slug' => $slug,
                // A plain relative URL rather than route() — this file is
                // also read by pure Unit tests with no app container, and it
                // must match GET /journal/{category}/{article} exactly.
                'href' => "/journal/{$entry['category_id']}/{$slug}",
            ];
        }, $entries);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rawEntries(): array
    {
        return [
            [
                'role' => 'lead',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Letters from a Slow Train',
                'excerpt' => 'Windows blur into watercolour somewhere between stations. A story finds its pace in the clatter of rails, the steam of a borrowed thermos, and the quiet courage of going nowhere in a hurry. This dispatch follows the long way — the seats that face backwards, the towns that pass without announcement, and the sentences that only arrive when the landscape softens.',
                'caption' => 'Afternoon light along the northern corridor.',
                'date' => '2026-02-28',
                'date_label' => '28 Feb 2026',
                'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1400',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'Where the Coast Still Speaks Softly',
                'excerpt' => 'Cliff light, salt air, and the kind of afternoon that asks you to put the map away. A guide for those who prefer the quieter shore.',
                'date' => '2026-01-30',
                'date_label' => '30 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'The Colour of Quiet Markets',
                'excerpt' => 'Spice, cloth, and conversation — a living collage of mornings that refuse haste, and vendors who know your order before you speak.',
                'date' => '2026-02-14',
                'date_label' => '14 Feb 2026',
                'image' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Life on the Road',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Brewing Between Pages',
                'excerpt' => 'Steam rising over unfinished sentences. The day begins before the world asks for anything, one pour at a time.',
                'date' => '2026-02-02',
                'date_label' => '02 Feb 2026',
                'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Photography',
                'category_id' => 'the-bigger-picture',
                'title' => 'Valley After Rain',
                'excerpt' => 'Clouds lift like a curtain. The land remembers every drop, and the road shines just long enough to invite another mile.',
                'date' => '2026-01-05',
                'date_label' => '05 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Culture',
                'category_id' => 'the-bigger-picture',
                'title' => 'Lantern Hour in the Old Quarter',
                'excerpt' => 'Night softens the edges. Light becomes a companion rather than a spectacle, and conversation returns to the courtyard.',
                'date' => '2025-12-29',
                'date_label' => '29 Dec 2025',
                'image' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Empty Road, Full Horizon',
                'excerpt' => 'Horizon as invitation. The kind of silence that makes room for wondering what the next town will smell like.',
                'date' => '2025-12-12',
                'date_label' => '12 Dec 2025',
                'image' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Photo Essay',
                'category_id' => 'the-bigger-picture',
                'title' => 'Weathered Light',
                'excerpt' => 'A portrait study from the road — eyes that refuse to look away from the story, hands that have held weather and work.',
                'date' => '2025-11-08',
                'date_label' => '08 Nov 2025',
                'image' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Dispatch',
                'category_id' => 'the-bigger-picture',
                'title' => 'Rain on Glass',
                'excerpt' => 'The city dissolves into watercolour — travel as a soft blur of elsewhere, seen from a window seat.',
                'date' => '2025-10-30',
                'date_label' => '30 Oct 2025',
                'image' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'Market Breath at First Light',
                'excerpt' => 'Spice, cloth, and conversation layered like a living collage — the city waking one stall at a time.',
                'date' => '2025-11-22',
                'date_label' => '22 Nov 2025',
                'image' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'column',
                'tag' => 'Essay',
                'category_id' => 'the-bigger-picture',
                'title' => 'A Note from a Rainy Evening',
                'excerpt' => 'Raindrops, old songs and a notebook. The perfect recipe for clarity when the world softens outside the window.',
                'date' => '2026-03-12',
                'date_label' => '12 Mar 2026',
                'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'column',
                'tag' => 'Notes',
                'category_id' => 'the-bigger-picture',
                'title' => 'Midnight Margins',
                'excerpt' => 'Ink still drying. Thoughts that only arrive when the house has gone quiet and the kettle has one last whisper left.',
                'date' => '2026-01-09',
                'date_label' => '09 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'brief',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Holding Soft Light',
                'excerpt' => 'A frame that waits. The kind of silence that makes room for wondering.',
                'date' => '2026-01-21',
                'date_label' => '21 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'brief',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'Bicycle Against the Old Door',
                'excerpt' => 'A street corner that remembers every departure.',
                'date' => '2025-12-18',
                'date_label' => '18 Dec 2025',
                'image' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?q=80&w=900',
                'href' => '#',
            ],

            // Chapters Over Coffee — the rest of the shelf
            [
                'role' => 'feature',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Two Borders Before Breakfast',
                'excerpt' => 'A passport stamp, a cold coffee, and the strange lightness of leaving one country before the sun is fully up.',
                'date' => '2026-03-05',
                'date_label' => '05 Mar 2026',
                'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'The Sleeper Car at Midnight',
                'excerpt' => "Rhythm of the tracks, a stranger's borrowed blanket, and a notebook that fills itself somewhere past the third station.",
                'date' => '2025-11-30',
                'date_label' => '30 Nov 2025',
                'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'A Postcard I Never Sent',
                'excerpt' => 'Some places are better kept folded in a pocket than mailed home — this is one of the reasons why.',
                'date' => '2026-01-14',
                'date_label' => '14 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Travel Diaries',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Losing the Guidebook on Purpose',
                'excerpt' => 'The best afternoon of the trip started the moment the map stayed in the bag.',
                'date' => '2025-10-09',
                'date_label' => '09 Oct 2025',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1000',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Life on the Road',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Gas Station Coffee, Ranked',
                'excerpt' => 'An unscientific, deeply caffeinated survey of the long way home.',
                'date' => '2026-03-30',
                'date_label' => '30 Mar 2026',
                'image' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Life on the Road',
                'category_id' => 'chapters-over-coffee',
                'title' => 'The Playlist That Only Makes Sense at 80km/h',
                'excerpt' => 'Some songs only work with a window down and a horizon that keeps moving.',
                'date' => '2025-12-05',
                'date_label' => '05 Dec 2025',
                'image' => 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Life on the Road',
                'category_id' => 'chapters-over-coffee',
                'title' => 'Sleeping in the Car Outside a National Park',
                'excerpt' => 'Stars through a fogged windscreen, and a dawn that made the cramped night worth it.',
                'date' => '2026-06-01',
                'date_label' => '01 Jun 2026',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Life on the Road',
                'category_id' => 'chapters-over-coffee',
                'title' => 'A Thermos, a Map, and No Real Plan',
                'excerpt' => 'The itinerary that worked best was the one written in pencil, not pen.',
                'date' => '2025-10-25',
                'date_label' => '25 Oct 2025',
                'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=900',
                'href' => '#',
            ],

            // Worth Knowing — the rest of the shelf
            [
                'role' => 'feature',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'A Weekend Guide to the Hill Towns',
                'excerpt' => 'Three villages, one winding road, and exactly enough time for two long lunches.',
                'date' => '2026-02-20',
                'date_label' => '20 Feb 2026',
                'image' => 'https://images.unsplash.com/photo-1474722883778-792e7990302f?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'Where to Stay Near the Old Harbour',
                'excerpt' => 'Three small inns, one honest comparison, and the view worth paying extra for.',
                'date' => '2025-12-30',
                'date_label' => '30 Dec 2025',
                'image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'Getting Around Without a Rental Car',
                'excerpt' => 'Buses, bicycles, and the occasional kindness of a stranger with a boat.',
                'date' => '2026-04-11',
                'date_label' => '11 Apr 2026',
                'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Destination Guides',
                'category_id' => 'worth-knowing',
                'title' => 'Five Viewpoints Worth the Climb',
                'excerpt' => 'Where to go before dawn, and which one to save for your last evening.',
                'date' => '2025-09-19',
                'date_label' => '19 Sep 2025',
                'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?q=80&w=900',
                'href' => '#',
            ],

            // The Bigger Picture — the rest of the shelf
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => "The Tailor Who Remembers Everyone's Shoulders",
                'excerpt' => 'Chalk, thread, and forty years of measurements kept only in memory.',
                'date' => '2026-03-22',
                'date_label' => '22 Mar 2026',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'Bread Ovens Before the City Wakes',
                'excerpt' => 'Flour dust catching the first light, long before the shutters lift on the street.',
                'date' => '2025-08-30',
                'date_label' => '30 Aug 2025',
                'image' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'A Corner Shop That Sells Everything and Nothing',
                'excerpt' => 'Candles, string, a single cold drink — and the owner who knows the whole street by name.',
                'date' => '2026-01-03',
                'date_label' => '03 Jan 2026',
                'image' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'The Barber Chair as Town Square',
                'excerpt' => 'Where the news travels faster than the scissors, and everyone waits their turn gladly.',
                'date' => '2025-07-14',
                'date_label' => '14 Jul 2025',
                'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=900',
                'href' => '#',
            ],
            [
                'role' => 'feature',
                'tag' => 'Local Stories',
                'category_id' => 'the-bigger-picture',
                'title' => 'What the Fishmonger Knows by Sunrise',
                'excerpt' => 'The tide, the weather, and which table will sell out before nine.',
                'date' => '2026-05-02',
                'date_label' => '02 May 2026',
                'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=900',
                'href' => '#',
            ],
        ];
    }

    public static function count(): int
    {
        return count(self::all());
    }

    /**
     * The Journal's named categories, in display order — the same three the
     * header, mobile drawer, and footer nav already link under "Journal".
     * Every entry's `category_id` points at one of these.
     *
     * @return list<array{id: string, name: string}>
     */
    public static function categories(): array
    {
        return [
            ['id' => 'the-bigger-picture', 'name' => 'The Bigger Picture'],
            ['id' => 'worth-knowing', 'name' => 'Worth Knowing'],
            ['id' => 'chapters-over-coffee', 'name' => 'Chapters Over Coffee'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function categorySlugs(): array
    {
        return array_column(self::categories(), 'id');
    }

    /**
     * @return array{id: string, name: string}|null
     */
    public static function findCategory(string $slug): ?array
    {
        foreach (self::categories() as $category) {
            if ($category['id'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Every entry filed under a category, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public static function forCategory(string $slug): array
    {
        return collect(self::all())
            ->where('category_id', $slug)
            ->sortByDesc('date')
            ->values()
            ->all();
    }

    /**
     * Locate a single entry by its category and slug.
     *
     * @return array{category: array{id: string, name: string}, article: array<string, mixed>}|null
     */
    public static function findEntry(string $categorySlug, string $entrySlug): ?array
    {
        $category = self::findCategory($categorySlug);

        if ($category === null) {
            return null;
        }

        foreach (self::forCategory($categorySlug) as $entry) {
            if ($entry['slug'] === $entrySlug) {
                return ['category' => $category, 'article' => $entry];
            }
        }

        return null;
    }

    /**
     * One representative entry (the most recent) for every named category,
     * in canonical category order. Each entry carries the category's real
     * name (not its own free-text tag), so the link text on the highlight
     * always matches the Journal's own category naming.
     *
     * @return list<array<string, mixed>>
     */
    public static function categoryHighlights(): array
    {
        return collect(self::categories())
            ->map(function (array $category): ?array {
                $entry = self::forCategory($category['id'])[0] ?? null;

                return $entry === null ? null : [...$entry, 'category_name' => $category['name']];
            })
            ->filter()
            ->values()
            ->all();
    }
}
