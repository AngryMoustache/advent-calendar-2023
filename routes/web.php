<?php

use Illuminate\Support\Facades\Route;

for ($i = 1; $i <= 24; $i++) {
    Route::get("/day-{$i}/one", "\App\Http\Controllers\Day{$i}Controller@one");
    Route::get("/day-{$i}/two", "\App\Http\Controllers\Day{$i}Controller@two");
}
