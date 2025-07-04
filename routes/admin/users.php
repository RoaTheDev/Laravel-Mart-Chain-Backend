<?php

use Illuminate\Support\Facades\Route;

Route::get('/users/lists', function () {
    $data = [
        [
            'id'=>1,
            'name'=>'dara',
            'gender'=>'male',
        ],
        [
            'id'=>1,
            'name'=>'dara',
            'gender'=>'male',
        ],[
            'id'=>1,
            'name'=>'dara',
            'gender'=>'male',
        ]
    ];
    return response()->json($data);
});
