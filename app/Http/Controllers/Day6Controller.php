<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class Day6Controller extends Controller
{
    public function one()
    {
        return $this->data('6-1');
    }

    public function two()
    {
        return $this->data('6-2');
    }

    private function data(string $file)
    {
        $file = File::get(public_path("inputs/{$file}.txt"), 'r');

        return collect(json_decode($file))
            ->map(function (object $race) {
                for ($holding = 0; $holding <= $race->time; $holding++) {
                    if ($holding * ($race->time - $holding) > $race->distance) {
                        return $race->time - ($holding * 2) + 1;
                    }
                }

                return null;
            })
            ->filter()
            ->reduce(fn ($carry, $records) => $carry * $records, 1);
    }
}
