<?php

return [
    'required' => ':attribute は必須です。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'date' => ':attribute は有効な日付ではありません。',
    'exists' => '選択された :attribute は無効です。',
    'unique' => ':attribute は既に使用されています。',
    'image' => ':attribute は画像である必要があります。',
    'max' => [
        'numeric' => ':attribute は :max 以下である必要があります。',
        'file' => ':attribute は :max キロバイト以下である必要があります。',
        'string' => ':attribute は :max 文字以下である必要があります。',
        'array' => ':attribute は :max 個以下である必要があります。',
    ],
    'min' => [
        'numeric' => ':attribute は少なくとも :min である必要があります。',
        'file' => ':attribute は少なくとも :min キロバイトである必要があります。',
        'string' => ':attribute は少なくとも :min 文字である必要があります。',
        'array' => ':attribute は少なくとも :min 個である必要があります。',
    ],
];
