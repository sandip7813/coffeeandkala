<?php

namespace App\Support;

class PoetryCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'slug' => 'the-weight-of-rain',
                'number' => '01',
                'title' => 'The Weight Of Rain',
                'mood' => 'Longing',
                'excerpt' => 'It rained the day you left, and the sky hasn\'t stopped since.',
                'narration' => 'Written for the ones who measure time in weather — and know a sky doesn\'t have to be crying to be doing exactly that.',
                'src' => asset('images/poetry/doors/the-weight-of-rain-large.jpg'),
                'thumb' => asset('images/poetry/doors/the-weight-of-rain-thumb.jpg'),
                'stanzas' => [
                    [
                        'It rained the day you left,',
                        'and something in the gutters',
                        'has been grieving ever since —',
                        'a small, insistent sound',
                        'against the window I keep closed.',
                    ],
                    [
                        'I have learned the weather',
                        'the way one learns a name',
                        'spoken too often to forget:',
                        'each cloud a rehearsal,',
                        'each drop a syllable of you.',
                    ],
                    [
                        'Perhaps this is what longing is —',
                        'not the absence of sun,',
                        'but the patience of rain,',
                        'falling anyway,',
                        'believing something still needs growing.',
                    ],
                ],
            ],
            [
                'slug' => 'things-we-left-unspoken',
                'number' => '02',
                'title' => 'Things We Left Unspoken',
                'mood' => 'Unspoken',
                'excerpt' => 'Some words are not meant to be spoken. They are meant to stay.',
                'narration' => 'For the drawer in every house that keeps what was never sent — some letters were only ever meant to be written, not read.',
                'src' => asset('images/poetry/doors/things-we-left-unspoken-large.jpg'),
                'thumb' => asset('images/poetry/doors/things-we-left-unspoken-thumb.jpg'),
                'stanzas' => [
                    [
                        'There is a drawer in every house',
                        'where the letters go unsent,',
                        'where sorry sits beside I loved you',
                        'and neither dares to move.',
                    ],
                    [
                        'We are so careful with our silence,',
                        'polishing it like an heirloom,',
                        'passing it from year to year',
                        'as though it were a gift.',
                    ],
                    [
                        'But some nights the drawer creaks open',
                        'on its own, and I hear',
                        'everything I never said',
                        'introduce itself, softly, as regret.',
                    ],
                ],
            ],
            [
                'slug' => 'the-colour-of-silence',
                'number' => '03',
                'title' => 'The Colour Of Silence',
                'mood' => 'Reflections',
                'excerpt' => 'Silence isn\'t empty. It\'s full of everything we couldn\'t explain.',
                'narration' => 'A study of the hours after a room goes quiet — when nothing is said, and somehow everything still is.',
                'src' => asset('images/poetry/doors/the-colour-of-silence-large.jpg'),
                'thumb' => asset('images/poetry/doors/the-colour-of-silence-thumb.jpg'),
                'stanzas' => [
                    [
                        'Silence is not the absence of sound —',
                        'it is amber, it is dust',
                        'suspended in an afternoon,',
                        'the colour of a room',
                        'after someone has stopped speaking.',
                    ],
                    [
                        'I have sat inside it for hours,',
                        'turning it over like a stone,',
                        'looking for the words',
                        'that must be underneath somewhere,',
                        'still warm from being held.',
                    ],
                    [
                        'Some say silence has no colour.',
                        'I say they have never watched it',
                        'settle over a kitchen table',
                        'the way dusk settles over a field —',
                        'gold, then grey, then gone.',
                    ],
                ],
            ],
            [
                'slug' => 'before-the-morning-arrives',
                'number' => '04',
                'title' => 'Before The Morning Arrives',
                'mood' => 'Hope',
                'excerpt' => 'There is a quiet that lives between the dark and the dawn.',
                'narration' => 'For the last hour of the night, before the birds have decided anything. Quietly, it is the most hopeful hour there is.',
                'src' => asset('images/poetry/doors/before-the-morning-arrives-large.jpg'),
                'thumb' => asset('images/poetry/doors/before-the-morning-arrives-thumb.jpg'),
                'stanzas' => [
                    [
                        'There is a hush before the light,',
                        'a held breath in the house,',
                        'when the birds have not yet decided',
                        'whether the day is worth the singing.',
                    ],
                    [
                        'This is the hour I trust the most —',
                        'not yet disappointed,',
                        'not yet certain,',
                        'only the soft insistence',
                        'that morning is still coming.',
                    ],
                    [
                        'So I sit here in the almost-dark',
                        'and let hope be small and quiet,',
                        'a single lit window',
                        'waiting patiently',
                        'for the rest of the street to wake.',
                    ],
                ],
            ],
            [
                'slug' => 'paper-boats',
                'number' => '05',
                'title' => 'Paper Boats And Other Drifts',
                'mood' => 'Dreams',
                'excerpt' => 'We sent our dreams away, hoping they\'d find their own shore.',
                'narration' => 'A small elegy for every wish we ever set loose on water, trusting it to carry what we couldn\'t.',
                'src' => asset('images/poetry/doors/paper-boats-large.jpg'),
                'thumb' => asset('images/poetry/doors/paper-boats-thumb.jpg'),
                'stanzas' => [
                    [
                        'We used to fold our wishes',
                        'into paper boats,',
                        'set them loose on the monsoon drains,',
                        'and run beside them',
                        'as if we could outrun the current.',
                    ],
                    [
                        'Some sank before the corner.',
                        'Some we never saw again.',
                        'One, I like to think,',
                        'is still sailing somewhere,',
                        'carrying a wish I have forgotten I made.',
                    ],
                    [
                        'Grown now, I still fold things —',
                        'napkins, receipts, the corners of days —',
                        'into small boats of hope',
                        'and let them go,',
                        'trusting water more than certainty.',
                    ],
                ],
            ],
            [
                'slug' => 'the-door-i-kept-closed',
                'number' => '06',
                'title' => 'The Door I Kept Closed',
                'mood' => 'Threshold',
                'excerpt' => 'Every doorway holds a version of me I haven\'t met yet.',
                'narration' => 'On the rooms we avoid inside ourselves — and what happens the one night we\'re finally tired enough to knock.',
                'src' => asset('images/poetry/doors/the-door-i-kept-closed-large.jpg'),
                'thumb' => asset('images/poetry/doors/the-door-i-kept-closed-thumb.jpg'),
                'stanzas' => [
                    [
                        'There is a door at the end of the hall',
                        'I have walked past for years,',
                        'telling myself the light in there',
                        'is better left undisturbed.',
                    ],
                    [
                        'But tonight the house is quiet enough',
                        'to hear it breathing,',
                        'and I understand, finally,',
                        'that every room I have avoided',
                        'was only ever waiting for me to knock.',
                    ],
                    [
                        'So I open it —',
                        'not bravely, just tired',
                        'of choosing the same three rooms of myself,',
                        'again and again —',
                        'and step into whoever I am on the other side.',
                    ],
                ],
            ],
        ];
    }

    public static function count(): int
    {
        return count(self::all());
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_column(self::all(), 'slug');
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $slug): ?array
    {
        foreach (self::all() as $poem) {
            if ($poem['slug'] === $slug) {
                return $poem;
            }
        }

        return null;
    }

    /**
     * The poem before and after the given slug, wrapping at either end so
     * the reading experience never dead-ends.
     *
     * @return array{prev: array<string, mixed>, next: array<string, mixed>}
     */
    public static function neighbours(string $slug): array
    {
        $poems = self::all();
        $index = array_search($slug, array_column($poems, 'slug'), true);
        $count = count($poems);

        return [
            'prev' => $poems[($index - 1 + $count) % $count],
            'next' => $poems[($index + 1) % $count],
        ];
    }

    /**
     * The `$each` poems before and after the given slug, in reading order
     * (furthest-back first, nearest-ahead last), wrapping at either end.
     * Never returns the poem itself, and never repeats a poem even if the
     * catalog is smaller than `$each * 2`.
     *
     * @return array{prev: list<array<string, mixed>>, next: list<array<string, mixed>>}
     */
    public static function nearby(string $slug, int $each = 2): array
    {
        $poems = self::all();
        $index = array_search($slug, array_column($poems, 'slug'), true);
        $count = count($poems);
        $each = min($each, intdiv($count - 1, 2));

        $prev = [];
        for ($i = $each; $i >= 1; $i--) {
            $prev[] = $poems[($index - $i + $count) % $count];
        }

        $next = [];
        for ($i = 1; $i <= $each; $i++) {
            $next[] = $poems[($index + $i) % $count];
        }

        return ['prev' => $prev, 'next' => $next];
    }
}
