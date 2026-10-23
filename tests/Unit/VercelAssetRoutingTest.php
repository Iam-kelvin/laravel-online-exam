<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VercelAssetRoutingTest extends TestCase
{
    #[DataProvider('assetPaths')]
    public function test_public_assets_reach_files_before_the_laravel_catch_all(string $path): void
    {
        $root = dirname(__DIR__, 2);
        $configuration = json_decode(file_get_contents($root . '/vercel.json'), true, 512, JSON_THROW_ON_ERROR);
        $destination = null;
        foreach ($configuration['routes'] as $route) {
            $pattern = '#^' . $route['src'] . '$#';
            if (preg_match($pattern, $path)) {
                $destination = preg_replace($pattern, $route['dest'], $path);
                break;
            }
        }
        $this->assertStringStartsWith('/public/', $destination ?? '', 'Static assets must not fall through to the PHP application.');
        $this->assertFileExists($root . $destination);
    }

    public static function assetPaths(): array
    {
        return array_map(fn ($path) => [$path], [
            '/sounds/rush/intro/lets-have-it-lets-have-it.mp3',
            '/sounds/rush/wrong/faaaa.mp3',
            '/sounds/rush/results/studio-audience-awwww-sound-fx.mp3',
            '/puzzles/rebus/puzzle-01.svg',
            '/css/exam-setup.css',
            '/js/feature-carousel.js',
        ]);
    }
}
