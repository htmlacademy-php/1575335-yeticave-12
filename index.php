<?php
require './helpers.php';

$is_auth = rand(0, 1);

$user_name = 'Dinar'; // укажите здесь ваше имя

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$items = [
['name'=>'2014 Rossignol District Snowboard',
'category'=> 'Доски и лыжи',
'price'=>'10999',
'img_url'=>'img/lot-1.jpg',
'expiration_date'=>'2020-08-30'
],
['name'=>'DC Ply Mens 2016/2017 Snowboard',
'category'=> 'Доски и лыжи',
'price'=>'159999',
'img_url'=>'img/lot-2.jpg',
'expiration_date'=>'2020-08-29'
],
['name'=>'Крепления Union Contact Pro 2015 года размер L/XL',
'category'=> 'Крепления',
'price'=>'8000',
'img_url'=>'img/lot-3.jpg',
'expiration_date'=>'2020-08-31'
],
['name'=>'Ботинки для сноуборда DC Mutiny Charocal',
'category'=> 'Ботинки',
'price'=>'10999',
'img_url'=>'img/lot-4.jpg',
'expiration_date'=>'2020-08-28'
],
['name'=>'Куртка для сноуборда DC Mutiny Charocal',
'category'=> 'Одежда',
'price'=>'7500',
'img_url'=>'img/lot-5.jpg',
'expiration_date'=>'2020-09-01'
],
['name'=>'Маска Oakley Canopy',
'category'=> 'Разное',
'price'=>'5400',
'img_url'=>'img/lot-6.jpg',
'expiration_date'=>'2020-08-27'
]
];

$page_content = include_template('/main.php', ['categories'=>$categories, 'items'=>$items ]);

$layout_content = include_template('/layout.php', ['content' =>$page_content, 'title' => 'Главная' ,'categories' => $categories, 'is_auth' => $is_auth, 'user_name' => $user_name ]); 

print($layout_content);
