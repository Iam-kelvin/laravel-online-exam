<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class RushSounds
{
    public function wrongAnswers(): array
    {
        $directory = public_path('sounds/rush/wrong');

        return File::isDirectory($directory)
            ? collect(File::files($directory))
                ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['mp3', 'wav', 'ogg', 'm4a']))
                ->map(fn ($file) => asset('sounds/rush/wrong/' . rawurlencode($file->getFilename())))
                ->values()->all()
            : [];
    }

    public function intro(): ?string
    {
        return $this->url('intro/lets-have-it-lets-have-it.mp3');
    }

    public function result(int $correct, int $answered): ?string
    {
        return $this->url($answered > 0 && $correct * 2 > $answered
            ? 'results/studio-audience-awwww-sound-fx.mp3'
            : 'results/you-dey-go-na.mp3');
    }

    private function url(string $path): ?string
    {
        return File::isFile(public_path('sounds/rush/' . $path)) ? asset('sounds/rush/' . $path) : null;
    }
}
