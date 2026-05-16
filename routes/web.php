<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('home'))->name('home');
Route::get('/merge', fn() => view('merge'))->name('merge');
Route::get('/split', fn() => view('split'))->name('split');
Route::get('/pdf-to-image', fn() => view('pdf-to-image'))->name('pdf-to-image');
Route::get('/image-to-pdf', fn() => view('image-to-pdf'))->name('image-to-pdf');
