<?php

return [
    'required' => 'Il campo :attribute è obbligatorio.',
    'email' => 'Il campo :attribute deve essere un indirizzo e-mail valido.',
    'date' => 'Il campo :attribute non è una data valida.',
    'exists' => 'Il campo :attribute selezionato non è valido.',
    'unique' => 'Il campo :attribute è già in uso.',
    'image' => 'Il campo :attribute deve essere un’immagine.',
    'max' => [
        'numeric' => 'Il campo :attribute non può essere maggiore di :max.',
        'file' => 'Il campo :attribute non può essere maggiore di :max kilobyte.',
        'string' => 'Il campo :attribute non può superare :max caratteri.',
        'array' => 'Il campo :attribute non può contenere più di :max elementi.',
    ],
    'min' => [
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'file' => 'Il campo :attribute deve essere almeno :min kilobyte.',
        'string' => 'Il campo :attribute deve contenere almeno :min caratteri.',
        'array' => 'Il campo :attribute deve contenere almeno :min elementi.',
    ],
];
