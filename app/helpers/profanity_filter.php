<?php

function containsProfanity(string $text): bool {
    $badWords = [
        'anjing', 'bangsat', 'brengsek', 'bajingan', 'kontol', 'memek',
        'tolol', 'goblok', 'idiot', 'babi', 'setan', 'keparat', 'sialan',
        'asu', 'jancok', 'cok', 'tai', 'kampret', 'kurang ajar'
    ];
    $lower = mb_strtolower($text);
    foreach ($badWords as $word) {
        if (str_contains($lower, $word)) return true;
    }
    return false;
}
