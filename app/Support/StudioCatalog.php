<?php

namespace App\Support;

class StudioCatalog
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 'work-01',
                'number' => '01',
                'title' => 'Ember Fields',
                'description' => 'Thick pigment laid in blocks — orange, charcoal, and the pale quiet between them.',
                'medium' => 'Acrylic on canvas',
                'src' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1541961017774-22349e4a1262?q=80&w=400',
            ],
            [
                'id' => 'work-02',
                'number' => '02',
                'title' => 'Clay Memory',
                'description' => 'A vessel that remembers the wheel — soft curves holding shadow like breath.',
                'medium' => 'Stoneware',
                'src' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=400',
            ],
            [
                'id' => 'work-03',
                'number' => '03',
                'title' => 'Paper Weather',
                'description' => 'Torn edges and wash — colour behaving like cloud over unfinished ground.',
                'medium' => 'Ink & wash on paper',
                'src' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?q=80&w=400',
            ],
            [
                'id' => 'work-04',
                'number' => '04',
                'title' => 'Ochre Gesture',
                'description' => 'One decisive stroke across linen — experiment held still long enough to look.',
                'medium' => 'Oil on linen',
                'src' => 'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1547891654-e66ed7ebb968?q=80&w=400',
            ],
            [
                'id' => 'work-05',
                'number' => '05',
                'title' => 'Still Life, Unstill',
                'description' => 'Fruit, cloth, and afternoon light negotiating who gets to speak first.',
                'medium' => 'Oil study',
                'src' => 'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1578301978693-85fa9c0320b9?q=80&w=400',
            ],
            [
                'id' => 'work-06',
                'number' => '06',
                'title' => 'Blue Threshold',
                'description' => 'A cool field that opens like a door — colour as invitation, not spectacle.',
                'medium' => 'Mixed media',
                'src' => 'https://images.unsplash.com/photo-1549490349-8643362247b5?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1549490349-8643362247b5?q=80&w=400',
            ],
            [
                'id' => 'work-07',
                'number' => '07',
                'title' => 'Line & Breath',
                'description' => 'Minimal marks on warm paper — the studio learning how little is enough.',
                'medium' => 'Graphite on paper',
                'src' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?q=80&w=400',
            ],
            [
                'id' => 'work-08',
                'number' => '08',
                'title' => 'Fired Silence',
                'description' => 'Ceramic forms in conversation — matte skins catching soft gallery light.',
                'medium' => 'Ceramic sculpture',
                'src' => 'https://images.unsplash.com/photo-1605721911519-3dfeb3be25e7?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1605721911519-3dfeb3be25e7?q=80&w=400',
            ],
            [
                'id' => 'work-09',
                'number' => '09',
                'title' => 'Palette Afternoon',
                'description' => 'Pigments waiting their turn — imagination still wet on the board.',
                'medium' => 'Studio study',
                'src' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1513364776144-60967b0f800f?q=80&w=400',
            ],
            [
                'id' => 'work-10',
                'number' => '10',
                'title' => 'Warm Geometry',
                'description' => 'Planes of terracotta and cream — contemporary calm with a handmade edge.',
                'medium' => 'Acrylic on panel',
                'src' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?q=80&w=400',
            ],
            [
                'id' => 'work-11',
                'number' => '11',
                'title' => 'Leaf Shadow',
                'description' => 'Natural light drawing on plaster — the studio itself as a temporary work.',
                'medium' => 'Light study',
                'src' => 'https://images.unsplash.com/photo-1482160549825-59d1b23cb208?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1482160549825-59d1b23cb208?q=80&w=400',
            ],
            [
                'id' => 'work-12',
                'number' => '12',
                'title' => 'After Colour',
                'description' => 'What remains when the brush is rinsed — residue as quiet expression.',
                'medium' => 'Watercolour',
                'src' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=1600',
                'thumb' => 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?q=80&w=400',
            ],
        ];
    }

    public static function count(): int
    {
        return count(self::all());
    }
}
