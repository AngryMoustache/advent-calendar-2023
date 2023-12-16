<?php

namespace App\Entities;

use App\Enums\Cardinal;
use Illuminate\Support\Collection;

class MirrorCave extends BaseMap
{
    public Collection $beams;
    public array $seen = [];
    public BaseMap $lightMap;

    public function __construct(public Collection $map)
    {
        $this->beams = collect([[
            'x' => -1,
            'y' => 0,
            'direction' => Cardinal::E,
        ]]);

        $this->lightMap = new BaseMap(collect()->pad(
            $this->map->count(),
            collect()->pad($this->map->first()->count(), '.')
        )->map->values());
    }

    public function travel(): self
    {
        $this->beams = $this->beams
            ->map(function (array $beam) {
                match ($beam['direction']) {
                    Cardinal::N => $beam['y']--,
                    Cardinal::E => $beam['x']++,
                    Cardinal::S => $beam['y']++,
                    Cardinal::W => $beam['x']--,
                };

                if (
                    isset($this->seen[$beam['x']][$beam['y']][$beam['direction']->value]) ||
                    $this->point($beam['x'], $beam['y']) === null
                ) {
                    return [null];
                }

                $this->seen[$beam['x']][$beam['y']][$beam['direction']->value] = true;
                $point = $this->point($beam['x'], $beam['y']);
                $beams = [];

                if ($point === '/') {
                    $beam['direction'] = match ($beam['direction']) {
                        Cardinal::N => Cardinal::E,
                        Cardinal::W => Cardinal::S,
                        Cardinal::E => Cardinal::N,
                        Cardinal::S => Cardinal::W,
                    };
                } elseif ($point === '\\') {
                    $beam['direction'] = match ($beam['direction']) {
                        Cardinal::N => Cardinal::W,
                        Cardinal::E => Cardinal::S,
                        Cardinal::S => Cardinal::E,
                        Cardinal::W => Cardinal::N,
                    };
                } elseif ($point === '|' && in_array($beam['direction'], [Cardinal::E, Cardinal::W])) {
                    $beams = [
                        ['x' => $beam['x'], 'y' => $beam['y'], 'direction' => Cardinal::N],
                        ['x' => $beam['x'], 'y' => $beam['y'], 'direction' => Cardinal::S],
                    ];
                } elseif ($point === '-' && in_array($beam['direction'], [Cardinal::N, Cardinal::S])) {
                    $beams = [
                        ['x' => $beam['x'], 'y' => $beam['y'], 'direction' => Cardinal::W],
                        ['x' => $beam['x'], 'y' => $beam['y'], 'direction' => Cardinal::E],
                    ];
                }

                $this->lightMap->set($beam['x'], $beam['y'], '#');

                return empty($beams) ? [$beam] : $beams;
            })
            ->flatten(1)
            ->filter();

        return $this;
    }
}
