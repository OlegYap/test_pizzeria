<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::get('/admin', fn() => view('welcome'))->middleware(['auth'])->name('admin');
