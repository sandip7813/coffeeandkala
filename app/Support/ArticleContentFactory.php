<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Builds the full editorial body for an article/entry detail page —
 * Introduction, Table of Contents, Editor's Note, Words & Images sections,
 * FAQ, and Author's Note — from data already sitting in FeatureCatalog /
 * JournalCatalog, so every one of the 58 articles gets real, on-brand,
 * non-repeating content without hand authoring each one.
 *
 * Nothing here is persisted: everything is derived deterministically from
 * the article's own title (used as a seed), so a given article always
 * renders the same content. Swapping this factory out for a database read
 * later is a one-line change in the controllers that call it.
 */
class ArticleContentFactory
{
    /**
     * Flavour data for the three Journal categories, which — unlike Features
     * chapters — don't carry a lead/tagline/quote/motifs of their own.
     *
     * @var array<string, array{lead: string, tagline: string, quote: string, motifs: list<string>}>
     */
    private const JOURNAL_VOICE = [
        'the-bigger-picture' => [
            'lead' => 'Local life, caught mid-gesture — markets, workshops, and the quiet choreography of a street that knows itself.',
            'tagline' => 'Ordinary mornings, observed closely.',
            'quote' => 'The bigger picture is just a lot of small ones, looked at slowly.',
            'motifs' => ['Street Life', 'Craft & Trade', 'Photo Essay', 'Everyday Ritual'],
        ],
        'worth-knowing' => [
            'lead' => 'Practical routes and honest tips for travelling well without travelling loudly.',
            'tagline' => 'Useful, unfussy, well tested.',
            'quote' => 'The best guide is the one that tells you what to skip.',
            'motifs' => ['Getting There', 'Where to Stay', 'What to Skip', 'Local Know-how'],
        ],
        'chapters-over-coffee' => [
            'lead' => 'Long journeys, slow mornings, and the notebook entries that only make sense once the kettle is on.',
            'tagline' => 'Stories brewed the long way.',
            'quote' => 'Every good journey eventually needs a second cup.',
            'motifs' => ['The Road', 'The Ritual', 'The Company', 'The Notebook'],
        ],
    ];

    /**
     * @var list<string>
     */
    private const LAYOUTS = ['image-left', 'content-only', 'image-right', 'image-top', 'content-only'];

    /**
     * @var list<string>
     */
    private const SECTION_OPENERS = [
        '{motif} sits close to the centre of this story — not as a checklist item, but as a way of paying attention.',
        'There is a particular rhythm to {motifLower}, one that rewards patience over hurry.',
        'Ask anyone who has spent real time with {motifLower} and they tend to say the same thing: it is less about the destination and more about the noticing.',
        '{tag} pieces like this one live in the details — a texture, a sound, a smell that outlasts the itinerary. {motif} is usually where that detail hides.',
        'What makes {motifLower} worth returning to is exactly what makes it hard to summarise: it changes with the hour, the season, the company you keep.',
        'Coffee & Kala keeps circling back to {motifLower} for one simple reason — it never quite reads the same way twice.',
    ];

    /**
     * @var list<string>
     */
    private const SECTION_FOLLOWUPS = [
        'In {category}, that shows up in small, specific ways — {lead}',
        'It is the kind of thread that runs quietly under everything else in {category}: {lead}',
        'None of this needs to be dramatic to matter. {lead}',
        'That is the promise {category} keeps returning to. {lead}',
    ];

    /**
     * @var list<string>
     */
    private const FAQ_QUESTIONS = [
        'What makes "{title}" worth the read?',
        'How much time should I set aside for this one?',
        'Does this connect to other stories in {category}?',
        'Is there a best time or mood to read this?',
        'What should I expect if I try to retrace this myself?',
        'Why does {motif} matter here?',
    ];

    /**
     * @var list<string>
     */
    private const FAQ_ANSWERS = [
        'Mostly for the texture of it — {excerptSnippet} It is a small, specific story rather than a broad survey, and that is deliberate.',
        'Give it a slow ten minutes, ideally with something warm in hand. It is written to be read once for the story and once more for the details.',
        'Yes — it sits inside {category}, which gathers pieces built on {tagline_lower} If this one resonates, that chapter is the natural next stop.',
        'Any unhurried moment works, though most of the Coffee & Kala desk reads pieces like this one in the early morning or the last hour before sleep.',
        'Treat it as a starting point rather than an itinerary. Details shift with season and circumstance; the feeling described here is the part worth chasing.',
        '{motif} keeps recurring across this chapter because it is a reliable way in — a specific enough detail to be true, general enough to be useful.',
    ];

    /**
     * @return array{
     *     intro: list<string>,
     *     toc: list<array{id: string, label: string}>,
     *     editors_note: array{body: string, signature: string},
     *     sections: list<array{id: string, heading: string, layout: string, image: ?string, body: list<string>}>,
     *     faq: list<array{question: string, answer: string}>,
     *     authors_note: array{title: string, body: string, signature: string},
     *     read_minutes: int
     * }
     */
    public static function build(array $article, array $category, string $source): array
    {
        $voice = $source === 'journal'
            ? [...self::JOURNAL_VOICE[$category['id']], 'name' => $category['name'], 'cover' => null]
            : [
                'lead' => $category['lead'],
                'tagline' => $category['tagline'],
                'quote' => $category['quote'],
                'motifs' => array_column($category['motifs'], 'label'),
                'name' => $category['name'],
                'cover' => $category['cover'],
            ];

        $seed = crc32($article['title']);

        $sections = self::buildSections($article, $voice, $seed);

        return [
            'intro' => [
                $article['excerpt'],
                sprintf(
                    '%s %s That is the thread this piece follows.',
                    $voice['lead'],
                    $voice['quote']
                ),
            ],
            // Every landmark on the page, in document order, not just the
            // Words & Images sections — the Introduction, Editor's Note, FAQ
            // and Author's Note all get their own anchor too.
            'toc' => collect([['id' => 'introduction', 'label' => 'Introduction']])
                ->concat([['id' => 'editors-note', 'label' => "Editor's Note"]])
                ->concat(collect($sections)->map(fn (array $section): array => [
                    'id' => $section['id'],
                    'label' => $section['heading'],
                ]))
                ->concat([['id' => 'faq', 'label' => 'Frequently Asked Questions']])
                ->concat([['id' => 'authors-note', 'label' => "Author's Note"]])
                ->all(),
            'editors_note' => [
                'body' => sprintf(
                    'We keep this one close to the desk. "%s" is filed under %s — the kind of piece that earns its place in %s not by covering everything, but by paying real attention to one small, true thing. Read it the way it was written: unhurried.',
                    $article['title'],
                    $article['tag'],
                    $voice['name']
                ),
                'signature' => '— The Coffee & Kala Desk',
            ],
            'sections' => $sections,
            'faq' => self::buildFaq($article, $voice, $seed),
            'authors_note' => [
                // The category's own tagline doubles as the note's title —
                // short, punchy, and already written in the chapter's voice.
                'title' => $voice['tagline'],
                'body' => sprintf(
                    'Coffee & Kala is written under a single editorial voice, but every piece passes through many hands before it reaches this page — reported, re-read, and sat with until it feels honest. "%s" is offered in that spirit.',
                    $article['title']
                ),
                'signature' => '— Coffee & Kala Editorial',
            ],
            'read_minutes' => self::estimateReadMinutes($article, $sections),
        ];
    }

    /**
     * @param  array{lead: string, tagline: string, quote: string, motifs: list<string>, name: string, cover: ?string}  $voice
     * @return list<array{id: string, heading: string, layout: string, image: ?string, body: list<string>}>
     */
    private static function buildSections(array $article, array $voice, int $seed): array
    {
        $images = array_values(array_filter([$article['image'], $voice['cover']]));

        return collect($voice['motifs'])->values()->map(function (string $motif, int $index) use ($article, $voice, $seed, $images): array {
            $opener = self::pick(self::SECTION_OPENERS, $seed + $index);
            $followup = self::pick(self::SECTION_FOLLOWUPS, $seed + $index * 7);

            $layout = self::LAYOUTS[($index + intdiv($seed, 100)) % count(self::LAYOUTS)];

            $body = [
                self::fill($opener, $article, $voice, $motif),
                self::fill($followup, $article, $voice, $motif),
            ];

            return [
                'id' => Str::slug($motif),
                'heading' => $motif,
                'layout' => $layout,
                'image' => $layout === 'content-only' ? null : $images[$index % count($images)],
                'body' => $body,
            ];
        })->all();
    }

    /**
     * @param  array{lead: string, tagline: string, quote: string, motifs: list<string>, name: string}  $voice
     * @return list<array{question: string, answer: string}>
     */
    private static function buildFaq(array $article, array $voice, int $seed): array
    {
        $motifs = $voice['motifs'];
        $snippet = Str::of($article['excerpt'])->words(14, '…')->toString();

        return collect(range(0, 3))->map(function (int $i) use ($article, $voice, $seed, $motifs, $snippet): array {
            $q = self::FAQ_QUESTIONS[($seed + $i * 5) % count(self::FAQ_QUESTIONS)];
            $a = self::FAQ_ANSWERS[($seed + $i * 5) % count(self::FAQ_ANSWERS)];
            $motif = $motifs[($seed + $i) % count($motifs)];

            return [
                'question' => self::fill($q, $article, $voice, $motif),
                'answer' => str_replace(
                    ['{excerptSnippet}', '{tagline_lower}'],
                    [$snippet.' ', self::lowerFirst($voice['tagline'])],
                    self::fill($a, $article, $voice, $motif)
                ),
            ];
        })->unique('question')->values()->all();
    }

    private static function fill(string $template, array $article, array $voice, string $motif): string
    {
        return str_replace(
            ['{motif}', '{motifLower}', '{tag}', '{title}', '{category}', '{lead}'],
            [$motif, $motif, $article['tag'], $article['title'], $voice['name'], $voice['lead']],
            $template
        );
    }

    private static function pick(array $pool, int $seed): string
    {
        return $pool[$seed % count($pool)];
    }

    private static function lowerFirst(string $value): string
    {
        return Str::lower(Str::substr($value, 0, 1)).Str::substr($value, 1);
    }

    private static function estimateReadMinutes(array $article, array $sections): int
    {
        $wordCount = str_word_count($article['excerpt']);

        foreach ($sections as $section) {
            foreach ($section['body'] as $paragraph) {
                $wordCount += str_word_count($paragraph);
            }
        }

        return max(2, (int) ceil($wordCount / 200));
    }
}
