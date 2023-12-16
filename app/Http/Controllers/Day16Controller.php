<?php

namespace App\Http\Controllers;

use App\Entities\MirrorCave;
use App\Enums\Cardinal;
use Illuminate\Support\Facades\File;

class Day16Controller extends Controller
{
    public function one()
    {
        $map = $this->data();

        while ($map->beams->isNotEmpty()) {
            $map->travel();
        }

        echo $map->lightMap->count('#');
        $map->lightMap->render();
    }

    public function two()
    {
        $map = $this->data();
        $height = $map->map->count();
        $width = $map->map->first()->count();
        $count = -1;

        for ($w = 0; $w < $width; $w++) {
            $map = $this->data();

            $map->beams->transform(fn () => ['x' => $w, 'y' => -1, 'direction' => Cardinal::S]);
            while ($map->beams->isNotEmpty()) { $map->travel(); }
            $count = max($count, $map->lightMap->count('#'));

            $map->beams->transform(fn () => ['x' => $w, 'y' => $height + 1, 'direction' => Cardinal::N]);
            while ($map->beams->isNotEmpty()) { $map->travel(); }
            $count = max($count, $map->lightMap->count('#'));
        }

        for ($h = 0; $h < $height; $h++) {
            $map = $this->data();

            $map->beams->transform(fn () => ['x' => -1, 'y' => $h, 'direction' => Cardinal::E]);
            while ($map->beams->isNotEmpty()) { $map->travel(); }
            $count = max($count, $map->lightMap->count('#'));

            $map->beams->transform(fn () => ['x' => $width + 1, 'y' => $h, 'direction' => Cardinal::W]);
            while ($map->beams->isNotEmpty()) { $map->travel(); }
            $count = max($count, $map->lightMap->count('#'));
        }

        return $count;
    }

    private function data(): MirrorCave
    {
        $file = File::get(public_path("inputs/16-1.txt"), 'r');

        return new MirrorCave(
            collect(explode(PHP_EOL, $file))
                ->filter()
                ->map(fn (string $line) => collect(str_split($line)))
                ->filter()
        );
    }
}
