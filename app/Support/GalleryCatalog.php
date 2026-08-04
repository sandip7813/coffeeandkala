<?php

namespace App\Support;

class GalleryCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'plate-01',
                'number' => '01',
                'title' => 'The Last Light Over the Northern Rail Corridor at Dusk',
                'description' => 'A silhouette held against gold — the quiet choreography of leaving and arriving.',
                'location' => 'Northern Rail Corridor',
                'src' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1474487548417-781cb71495f3?q=80&w=400',
            ],
            [
                'id' => 'plate-02',
                'number' => '02',
                'title' => 'Crimson Passage',
                'description' => 'Prayer flags and stone walls: a single figure walking into the afternoon light.',
                'location' => 'Old Quarter, Himalayan Foothills',
                'src' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?q=80&w=400',
            ],
            [
                'id' => 'plate-03',
                'number' => '03',
                'title' => 'Lone Oar',
                'description' => 'Mist on still water. One boat, one rower, and the soft insistence of dawn.',
                'location' => 'Backwaters at First Light',
                'src' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?q=80&w=400',
            ],
            [
                'id' => 'plate-04',
                'number' => '04',
                'title' => 'Weathered Light',
                'description' => 'A face that has kept seasons. Eyes that refuse to look away from the story.',
                'location' => 'Portrait Study',
                'src' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1566616213894-2d4e1baee5d8?q=80&w=400',
            ],
            [
                'id' => 'plate-05',
                'number' => '05',
                'title' => 'Morning Ritual',
                'description' => 'Steam rising between pages — where the day begins before the world asks for anything.',
                'location' => 'Studio Kitchen',
                'src' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=400',
            ],
            [
                'id' => 'plate-06',
                'number' => '06',
                'title' => 'Market Breath',
                'description' => 'Spice, cloth, and conversation layered like a living collage.',
                'location' => 'Bazaar Lane',
                'src' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1599661046289-e31897846e41?q=80&w=400',
            ],
            [
                'id' => 'plate-07',
                'number' => '07',
                'title' => 'Valley After Rain',
                'description' => 'Clouds lift like a curtain. The land remembers every drop.',
                'location' => 'Western Ghats',
                'src' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=400',
            ],
            [
                'id' => 'plate-08',
                'number' => '08',
                'title' => 'Ink & Ember',
                'description' => 'A desk mid-thought — typewriter keys waiting for the next true sentence.',
                'location' => 'Writer\'s Corner',
                'src' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=400',
            ],
            [
                'id' => 'plate-09',
                'number' => '09',
                'title' => 'Lantern Hour',
                'description' => 'Night softens the edges. Light becomes a companion rather than a spectacle.',
                'location' => 'Festival Street',
                'src' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=400',
            ],
            [
                'id' => 'plate-10',
                'number' => '10',
                'title' => 'Empty Road',
                'description' => 'Horizon as invitation. The kind of silence that makes room for wondering.',
                'location' => 'Desert Highway',
                'src' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?q=80&w=400',
            ],
            [
                'id' => 'plate-11',
                'number' => '11',
                'title' => 'Rain on Glass',
                'description' => 'The city dissolves into watercolor — travel as a soft blur of elsewhere.',
                'location' => null,
                'src' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?q=80&w=400',
            ],
            [
                'id' => 'plate-12',
                'number' => '12',
                'title' => 'Circle of Hands',
                'description' => 'Shared tables, shared stories — the community the frame cannot fully hold.',
                'location' => 'Gathering Table',
                'src' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?q=80&w=400',
            ],
        ];
    }

    public static function count(): int
    {
        return count(self::all());
    }
}
