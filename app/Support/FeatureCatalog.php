<?php

namespace App\Support;

use Illuminate\Support\Str;

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
     *         slug: string,
     *         href: string
     *     }>
     * }>
     */
    public static function all(): array
    {
        return self::withArticleRouting(self::rawCategories());
    }

    /**
     * Assigns a stable, unique slug to every article within its chapter and
     * points its `href` at the article detail route — kept as a separate
     * pass so the raw catalog below stays easy to read/edit.
     *
     * @param  list<array<string, mixed>>  $categories
     * @return list<array<string, mixed>>
     */
    private static function withArticleRouting(array $categories): array
    {
        return array_map(function (array $category): array {
            $seen = [];

            $category['articles'] = array_map(function (array $article) use ($category, &$seen): array {
                $base = Str::slug($article['title']);
                $slug = $base;

                for ($suffix = 2; isset($seen[$slug]); $suffix++) {
                    $slug = "{$base}-{$suffix}";
                }

                $seen[$slug] = true;

                return [
                    ...$article,
                    'slug' => $slug,
                    // A plain relative URL rather than route() — this file is
                    // also read by pure Unit tests with no app container, and
                    // it must match GET /features/{category}/{article} exactly.
                    'href' => "/features/{$category['id']}/{$slug}",
                ];
            }, $category['articles']);

            return $category;
        }, $categories);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function rawCategories(): array
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
                        'excerpt' => 'Stone, shade, and the soft politics of gathering — reading cities through their thresholds. A doorway, a courtyard, a shaded step, a weathered wall: these are not merely passages between places, but quiet stages where everyday life unfolds. Cities reveal themselves in these in-between spaces, where strangers pause, neighbours exchange glances, and time seems to move differently. Architecture becomes less about walls and more about invitation — who is welcomed, who lingers, who passes through, and who is asked to remain outside.',
                        'tag' => 'Essay',
                        'date' => '2026-06-12',
                        'date_label' => '12 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Ink, Cloth, and the Hands That Keep Them',
                        'excerpt' => 'A morning with artisans whose craft still refuses the rush of the market. In the quiet rhythm of practiced hands, every gesture carries the memory of another time — measured, deliberate, and deeply attentive. Tools move slowly, materials reveal themselves patiently, and the work grows not from urgency but from understanding. Here, making is less about producing and more about preserving a conversation between hand, material, and tradition.',
                        'tag' => 'Profile',
                        'date' => '2026-05-03',
                        'date_label' => '03 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Gallery Light After Rain',
                        'excerpt' => 'What a washed sky does to colour on plaster walls. Under the pale light of morning, familiar colours lose their certainty — ochres soften, blues turn almost grey, and faded reds seem to breathe against the worn surface. Cracks, stains, and uneven textures become part of the palette, revealing a beauty that feels accidental yet quietly composed. For a brief moment, the wall holds the sky as much as it holds its own history.',
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
                        'excerpt' => 'A walk timed to mist, silence, and the first tea stall of the day. The streets emerge slowly from the pale morning, their edges softened by fog and the quiet footsteps of those already awake. Somewhere ahead, a kettle begins to sing, steam gathers beneath a corrugated roof, and the first cups of tea pass from one hand to another. For a while, the city feels unhurried — suspended between night and day, held together by mist, warmth, and the simple ritual of stopping for tea.',
                        'tag' => 'Travel',
                        'date' => '2026-06-01',
                        'date_label' => '01 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Learning the River by Canoe',
                        'excerpt' => 'Current, patience, and the small courage of leaving the bank. The river does not ask to be understood before it carries you forward; it simply moves, slow in some places, insistent in others. To step away from the familiar shore is to trust that movement — to accept uncertainty, read the changing light, and let the current redraw the distance between where you were and where you might arrive.',
                        'tag' => 'Field note',
                        'date' => '2026-04-22',
                        'date_label' => '22 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'A Night Market That Refuses Maps',
                        'excerpt' => 'Follow the smell of charcoal and citrus — the route writes itself. Around the next corner, smoke curls above a roadside grill, brightened by the sharp scent of lime pressed over something still sizzling. There is no map for these detours, only instinct: a turn toward warmth, a pause beside a crowded counter, a meal discovered because the air carried its own invitation. In cities, sometimes the best journeys begin with simply following what smells good.',
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
                        'excerpt' => 'How to stretch a week without stretching the wallet — or the spirit. Travel does not always need more money, more distance, or a crowded itinerary; sometimes it asks for less. A slower train, a modest room, a neighbourhood walk, a meal shared at an unassuming table — these small choices can make a journey feel fuller rather than smaller. The art is not in fitting more into seven days, but in leaving enough space to notice where you are.',
                        'tag' => 'Guide',
                        'date' => '2026-05-20',
                        'date_label' => '20 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'The Art of the Shared Table',
                        'excerpt' => 'Street stalls, set menus, and why the cheapest seat is often the best. There is something revealing about eating close to the pavement, where the city arrives without ceremony and the menu is shaped by whatever is freshest, fastest, and most loved. A plastic stool beside a crowded stall can offer more than a carefully designed dining room — a view of daily life, a conversation with strangers, and flavours that belong unmistakably to the place. Sometimes the best seat is simply the one that lets you watch the city eat.',
                        'tag' => 'Food',
                        'date' => '2026-04-08',
                        'date_label' => '08 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Hostel Lobbies as Living Rooms',
                        'excerpt' => 'Where strangers become itineraries, and itineraries become friends. A shared table, a missed turn, a conversation started while waiting for a train — travel has a way of turning small encounters into unexpected chapters. The people we meet often change the shape of a journey more than any carefully planned route, offering places to see, stories to follow, and reasons to linger a little longer. By the time the map begins to feel familiar, the strangers who coloured it may no longer feel like strangers at all.',
                        'tag' => 'Notes',
                        'date' => '2026-01-29',
                        'date_label' => '29 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Free Museums and the Long Lunch Break',
                        'excerpt' => 'When admission is zero, the afternoon belongs to you — and a shared sandwich. There is a particular freedom in wandering through a city without a ticket, a reservation, or a schedule waiting to be followed. A public garden, an open courtyard, a quiet gallery, or a shaded square can easily fill the hours, especially when there is nowhere you need to be. Add a sandwich split between two people, and an ordinary afternoon begins to feel like the kind of luxury money rarely manages to buy.',
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
                        'excerpt' => 'Linen, salt air, and the rare luxury of an empty afternoon. The day moves slowly by the water, carried by warm wind through open windows and the faint rhythm of waves beyond the street. There is nowhere to hurry to, nothing waiting to be crossed off — only a chair in the shade, a book left open, and the quiet pleasure of letting time pass without asking anything from it.',
                        'tag' => 'Stay',
                        'date' => '2026-06-18',
                        'date_label' => '18 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Spa Hours Measured in Steam',
                        'excerpt' => 'Thermal water, cedar, and the discipline of doing nothing well. Steam rises slowly into the cool air, carrying the scent of wet stone and cedar while the day remains deliberately out of reach. There are no plans to optimise, no places to rush toward — only warm water, quiet wood, and the gentle awareness of being present. Perhaps rest is its own kind of craft: learning to pause without guilt, to let the body soften, and to discover that an unhurried hour can be enough.',
                        'tag' => 'Wellness',
                        'date' => '2026-03-30',
                        'date_label' => '30 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Dining When the Lights Are Only Candles',
                        'excerpt' => 'A tasting menu that refuses spectacle — and still dazzles. Nothing arrives trying too hard to impress; each plate is restrained, precise, and quietly confident. A few ingredients, treated with care, reveal more than elaborate presentations ever could — a bitter leaf, a perfectly charred edge, a sauce reduced to its essence. The surprise comes slowly, in the details, where technique disappears behind flavour and the meal becomes less a performance than a conversation between the kitchen and the table.',
                        'tag' => 'Table',
                        'date' => '2026-02-05',
                        'date_label' => '05 Feb 2026',
                        'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Private Courtyards After Midnight',
                        'excerpt' => 'A key, a quiet pool, and stars that feel arranged for you alone. The door closes softly behind you, the last traces of the day dissolve into the still water, and the night settles over everything without interruption. There is no music, no crowd, no reason to check the time — only the faint ripple of the pool and a sky scattered with impossible clarity. For a few hours, solitude feels less like being alone and more like being given a world entirely to yourself.',
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
                        'excerpt' => 'Trams, azulejos, and the soft insistence of Atlantic weather. The city moves at its own gentle pace, rattling through narrow streets as rain gathers on tiled facades and wind slips in from the sea. Blue-and-white patterns catch the shifting light, shopfronts glow beneath awnings, and every sudden shower seems less like an interruption than part of the rhythm. Here, weather is not something to escape — it is woven into the character of the streets, making the city feel softer, slower, and unmistakably alive.',
                        'tag' => 'City',
                        'date' => '2026-05-11',
                        'date_label' => '11 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Kyoto Before the Crowds Find the Alley',
                        'excerpt' => 'Wooden doors, morning incense, and a map folded smaller. The day begins quietly, behind old thresholds where the scent of incense drifts into streets still waking from sleep. There is little need to know every turn when the city reveals itself slowly — a carved doorway, a courtyard glimpsed through a half-open gate, a bell somewhere beyond the next lane. Eventually the map disappears into a pocket, replaced by the simpler pleasure of wandering without knowing exactly where the morning will lead.',
                        'tag' => 'Dispatch',
                        'date' => '2026-04-02',
                        'date_label' => '02 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Marrakech at the Hour of Oranges',
                        'excerpt' => 'Colour as compass — reading a medina by scent and sound. In the maze of narrow lanes, direction comes less from signs than from the senses: saffron and smoke drifting from a stall, metal striking metal in a distant workshop, bright textiles catching the light between shadowed walls. Every turn offers another clue, another texture, another fragment of the city’s rhythm. Soon the map becomes unnecessary; the medina is something you learn not by measuring distance, but by listening, smelling, and following whatever colour catches your eye.',
                        'tag' => 'Travel',
                        'date' => '2026-01-16',
                        'date_label' => '16 Jan 2026',
                        'image' => 'https://images.unsplash.com/photo-1539020140153-e479b8c22e70?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Istanbul Between Two Continents',
                        'excerpt' => 'Ferry horns, spice stalls, and a skyline that keeps rewriting itself. The day unfolds between water and street, where the low call of boats meets the sharp fragrance of cardamom, pepper, and dried citrus rising from crowded stalls. Across the harbour, towers and rooftops shift with every change of light, reflected in the moving surface before disappearing behind the next ferry. Nothing stays still for long, yet the city feels strangely familiar — held together by the sounds, scents, and daily crossings that give it shape.',
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
                        'excerpt' => 'No railway station — only a bus, a square, and evenings that linger. The journey begins without ceremony, with a worn seat by the window and the slow unfolding of towns along the road. By evening, the bus gives way to a quiet square where chairs spill onto the pavement, conversations stretch across café tables, and the last light settles softly on old facades. Nothing is scheduled beyond this moment, and perhaps that is what makes the place feel worth arriving at.',
                        'tag' => 'Hidden',
                        'date' => '2026-06-08',
                        'date_label' => '08 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'A Coast Without Signage',
                        'excerpt' => 'Follow the fishermen’s path until the cliffs open like a secret. The trail winds quietly above the water, marked by weathered stone, salt-stained grass, and the traces of footsteps that seem to belong to another rhythm of life. Below, fishing boats drift in the morning haze while the sea keeps its patient distance from the shore. Then, almost without warning, the land falls away and the cliffs reveal a wide horizon — a hidden room of sky and water, earned simply by walking a little farther.',
                        'tag' => 'Detour',
                        'date' => '2026-03-12',
                        'date_label' => '12 Mar 2026',
                        'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Tea Houses Past the Last Streetlamp',
                        'excerpt' => 'Where the road softens and conversation becomes the only map. The journey slows as the hard edges of the highway give way to smaller roads, shaded turns, and places where nobody seems in a hurry to arrive. A question asked at a roadside stall leads to a story, the story leads to another road, and suddenly the planned route feels less important than the people along it. Sometimes the best way forward is simply to keep talking, keep listening, and let the journey redraw itself around the voices you meet.',
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
                        'excerpt' => 'Crush, oak, and the patience of people who measure years by vintage. In the cool quiet of the cellar, time is treated less as something to be spent than something to be allowed. Barrels rest in the dim light, grapes become memory, and every season leaves its trace in the glass. Here, patience is not an abstract virtue but part of the craft — an understanding that some things deepen only when given the years they ask for.',
                        'tag' => 'Wine',
                        'date' => '2026-05-28',
                        'date_label' => '28 May 2026',
                        'image' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Walking the Rows at First Light',
                        'excerpt' => 'Dew on leaves, dogs at the gate, and a tasting that begins before breakfast. The vineyard is still cool with morning when the first glasses are poured, the vines catching pale light and the earth carrying the scent of yesterday’s rain. Somewhere nearby, dogs wander between the rows, greeting familiar footsteps as if they too know the ritual. There is no rush to dress the day up — just fresh bread, quiet conversation, and the first taste of something shaped by the soil beneath your feet.',
                        'tag' => 'Field',
                        'date' => '2026-04-15',
                        'date_label' => '15 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1474722883778-792e7990302f?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Notes from a Long Lunch Under the Arbor',
                        'excerpt' => 'Bread, cheese, and a bottle that refuses to be rushed. The table asks for almost nothing — a loaf torn by hand, a wedge of cheese, a little salt, and wine poured slowly into waiting glasses. Outside, the afternoon drifts past without urgency, while conversation stretches between bites and silence becomes part of the meal. Some pleasures are better when left alone, given enough time to open, soften, and become exactly what they were meant to be.',
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
                        'excerpt' => 'Steam, serifs, and why some books only open properly with a mug beside them. There is a particular comfort in the ritual: warm coffee breathing beside the page, soft light falling across old type, and the quiet pause between one paragraph and the next. Some books resist being hurried, asking to be read slowly, with interruptions that feel less like distractions than part of the experience. A mug keeps the hands warm, the mind wandering, and the afternoon gently tethered to the page.',
                        'tag' => 'Essay',
                        'date' => '2026-06-22',
                        'date_label' => '22 Jun 2026',
                        'image' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1200',
                        'href' => '#',
                    ],
                    [
                        'title' => 'The Classics That Travel Well',
                        'excerpt' => 'Paperbacks for trains — thin spines, thick weather, and lines that stay. The best travel books are light enough to slip into a coat pocket and sturdy enough to survive rain, crowded platforms, and the soft wear of a long journey. Pages turn between stations, sometimes beneath a window blurred by monsoon or winter fog, and certain sentences begin to attach themselves to the landscape outside. Long after the ticket is forgotten, those lines remain — carrying the weather, the motion, and the particular silence of the journey with them.',
                        'tag' => 'Reading',
                        'date' => '2026-04-28',
                        'date_label' => '28 Apr 2026',
                        'image' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?q=80&w=900',
                        'href' => '#',
                    ],
                    [
                        'title' => 'Café Corners That Still Feel Like Home',
                        'excerpt' => 'Worn wood, familiar baristas, and the luxury of being known by your order. There is a quiet comfort in returning to a place where the chair feels familiar, the morning light falls just right, and your coffee arrives before you need to explain it. Nothing remarkable has to happen; a nod across the counter, the sound of the grinder, and a few unhurried minutes can be enough. In a world that constantly asks us to introduce ourselves, being recognised by something as simple as a daily order feels like its own small form of belonging.',
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
        return array_column(self::rawCategories(), 'id');
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
     * Locate a single article by its chapter and slug.
     *
     * @return array{category: array<string, mixed>, article: array<string, mixed>}|null
     */
    public static function findArticle(string $categorySlug, string $articleSlug): ?array
    {
        $category = self::find($categorySlug);

        if ($category === null) {
            return null;
        }

        foreach ($category['articles'] as $article) {
            if ($article['slug'] === $articleSlug) {
                return ['category' => $category, 'article' => $article];
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
