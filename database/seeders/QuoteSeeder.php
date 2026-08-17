<?php

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = [
            'Every cup tells a story worth savoring.',
            'Coffee first, kala always.',
            'Brewed with love, painted with intention.',
            'The best ideas arrive somewhere between the first sip and the last stroke.',
            'Art is the color; coffee is the canvas that holds the morning together.',
            'You can’t stop me unless I decide it’s time to.',
            'A quiet cup, a loud imagination.',
            'Where there is coffee, there is hope for one more masterpiece.',
            'Slow mornings make for bold afternoons.',
            'Paint like nobody is watching, brew like everybody is coming over.',
            'The world looks kinder after the first sip.',
            'Every masterpiece begins with a single, deliberate pour.',
            'Kala means art, and art means never running out of ideas — or coffee.',
            'Some days need two things: strong coffee and a stronger vision.',
            'Inspiration is just caffeine wearing a beret.',
            'Stir the cup, then stir the soul.',
            'A blank canvas and a full cup — anything is possible.',
            'Coffee doesn’t ask questions; it just gets to work.',
            'Behind every great artist is an even greater espresso machine.',
            'The aroma of coffee is the opening line of every good story.',
            'Creativity is brewed, not bought.',
            'Let the color run wild, let the coffee run hot.',
            'A cup of coffee is a small pause before a big idea.',
            'Art doesn’t wait, but coffee is always worth the wait.',
            'Find your rhythm in the ritual — the grind, the pour, the first brushstroke.',
            'What the canvas doesn’t say, the coffee often does.',
            'Good coffee, good company, good art — pick all three.',
            'Every sunrise is a fresh page and a fresh pot.',
            'The best conversations happen over the last drop of coffee.',
            'Make it strong, make it bold, make it yours.',
        ];

        foreach ($quotes as $text) {
            Quote::firstOrCreate(['text' => $text]);
        }
    }
}
