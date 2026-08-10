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
                'src' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800',
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
                'src' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=800',
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
                'src' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=800',
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
                'src' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=800',
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
                'src' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=800',
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
                'src' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=800',
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
}
