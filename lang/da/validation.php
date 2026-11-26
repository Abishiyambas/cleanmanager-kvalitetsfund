<?php

return [
    'after_or_equal' => ':attribute skal være :date eller senere.',
    'array' => ':attribute skal være en liste.',
    'confirmed' => ':attribute matcher ikke.',
    'date' => ':attribute er ikke en gyldig dato.',
    'email' => ':attribute skal være en gyldig e-mailadresse.',
    'exists' => 'Det valgte :attribute findes ikke.',
    'image' => ':attribute skal være et billede.',
    'in' => 'Det valgte :attribute er ugyldigt.',
    'max' => [
        'file' => ':attribute må højst være :max kB.',
        'string' => ':attribute må højst være :max tegn.',
    ],
    'min' => [
        'array' => ':attribute skal have mindst :min element.',
        'string' => ':attribute skal være mindst :min tegn.',
    ],
    'required' => ':attribute skal udfyldes.',
    'string' => ':attribute skal være tekst.',
    'unique' => ':attribute er allerede i brug.',

    'attributes' => [
        'name' => 'navn',
        'email' => 'e-mail',
        'password' => 'adgangskode',
        'description' => 'beskrivelse',
        'comment' => 'kommentar',
        'note' => 'dokumentation',
        'photo' => 'foto',
        'due_date' => 'frist',
        'responsible_id' => 'ansvarlig',
        'location_id' => 'lokation',
        'category_id' => 'kategori',
        'severity' => 'alvorlighed',
        'items' => 'kontrolpunkter',
        'items.*.text' => 'kontrolpunkt',
        'items.*.category_id' => 'kategori',
        'role' => 'rolle',
    ],
];
