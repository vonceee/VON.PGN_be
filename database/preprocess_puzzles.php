<?php

/**
 * Pre-processes the Lichess puzzle CSV into a compact JSON file
 * containing ~2000 well-distributed puzzles for seeding.
 *
 * Run locally: php database/preprocess_puzzles.php
 */

$csvPath = __DIR__ . '/../storage/app/lichess_db_puzzle.csv';
$jsonPath = __DIR__ . '/puzzles_selected.json';

if (!file_exists($csvPath)) {
    echo "CSV not found at: $csvPath\n";
    exit(1);
}

$bands = [
    ['min' => 0,    'max' => 600,   'count' => 600],
    ['min' => 600,  'max' => 800,   'count' => 1000],
    ['min' => 800,  'max' => 1000,  'count' => 1200],
    ['min' => 1000, 'max' => 1200,  'count' => 1500],
    ['min' => 1200, 'max' => 1400,  'count' => 1500],
    ['min' => 1400, 'max' => 1600,  'count' => 1200],
    ['min' => 1600, 'max' => 1800,  'count' => 1000],
    ['min' => 1800, 'max' => 2000,  'count' => 800],
    ['min' => 2000, 'max' => 2300,  'count' => 700],
    ['min' => 2300, 'max' => 9999,  'count' => 500],
];

echo "Reading CSV...\n";

$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle);

$colIndex = array_flip($header);
$idIdx    = $colIndex['PuzzleId'];
$fenIdx   = $colIndex['FEN'];
$movesIdx = $colIndex['Moves'];
$ratingIdx = $colIndex['Rating'];
$ratingDevIdx = $colIndex['RatingDeviation'];
$popIdx   = $colIndex['Popularity'] ?? null;
$playsIdx = $colIndex['NbPlays'];
$themesIdx = $colIndex['Themes'];
$gameUrlIdx = $colIndex['GameUrl'] ?? null;
$openingTagsIdx = $colIndex['OpeningTags'] ?? null;

$candidates = [];
foreach ($bands as $i => $band) {
    $candidates[$i] = [];
}

$processed = 0;
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 7) continue;

    $rating = (int) $row[$ratingIdx];
    $plays  = (int) $row[$playsIdx];

    if ($plays < 10) continue;

    $processed++;
    if ($processed % 500000 === 0) {
        echo "  Processed {$processed} rows...\n";
    }

    foreach ($bands as $i => $band) {
        if ($rating >= $band['min'] && $rating < $band['max']) {
            $score = $plays * 100 + (int) ($row[$popIdx ?? 5] ?? 50);
            $puzzle = [
                'id'     => $row[$idIdx],
                'fen'    => $row[$fenIdx],
                'moves'  => $row[$movesIdx],
                'rating' => $rating,
                'rating_deviation' => isset($row[$ratingDevIdx]) ? (int) $row[$ratingDevIdx] : null,
                'themes' => $row[$themesIdx],
                'popularity' => isset($row[$popIdx]) ? (int) $row[$popIdx] : null,
                'nb_plays' => $plays,
                'game_url' => $row[$gameUrlIdx] ?? null,
                'opening_tags' => $row[$openingTagsIdx] ?? null,
                'score'  => $score,
            ];

            if (count($candidates[$i]) < $band['count']) {
                $candidates[$i][] = $puzzle;
            } else {
                $minIdx = 0;
                $minScore = PHP_INT_MAX;
                foreach ($candidates[$i] as $ci => $c) {
                    if ($c['score'] < $minScore) {
                        $minScore = $c['score'];
                        $minIdx = $ci;
                    }
                }
                if ($score > $minScore) {
                    $candidates[$i][$minIdx] = $puzzle;
                }
            }
            break;
        }
    }
}

fclose($handle);

// Flatten
$selected = [];
foreach ($candidates as $bandPuzzles) {
    foreach ($bandPuzzles as $p) {
        unset($p['score']);
        $selected[] = $p;
    }
}

// Shuffle for randomness in the JSON file
shuffle($selected);

file_put_contents($jsonPath, json_encode($selected, JSON_PRETTY_PRINT));

echo "Done! {$processed} rows processed, " . count($selected) . " puzzles selected.\n";
echo "Written to: {$jsonPath} (" . round(filesize($jsonPath) / 1024) . " KB)\n";
