<?php

declare(strict_types=1);

namespace MostlyStatic\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use MostlyStatic\Registry;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use function Illuminate\Filesystem\join_paths;

final class Generate extends Command
{
    protected $signature = 'mostly-static:generate
                            {--o|output= : The output directory (defaults to the Laravel public path)}';

    protected $description = 'Generate static html files for all routes that are marked "static"';

    public function handle(Filesystem $fs, Kernel $kernel, Registry $registry, UrlGenerator $url)
    {
        $outputDir = $this->option('output') ?? $this->laravel->publicPath();

        if (! str_starts_with($outputDir, '/')) {
            $outputDir = join_paths(getcwd(), $outputDir);
        }

        foreach ($registry->routes() as $route => $parameterProvider) {
            if (! is_callable($parameterProvider)) {
                $parameterProvider = $this->laravel->get($parameterProvider);
            }

            foreach ($parameterProvider() as $parameters) {
                $path = $url->toRoute($route, $parameters, false);

                $request = Request::createFromBase(SymfonyRequest::create($path, 'GET'));

                $response = $kernel->handle($request);
                // Should we be calling $kernel->terminate() ???

                if (200 !== $response->getStatusCode()) {
                    $this->error("Error generating HTML for {$path} - non-200 status code");
                    continue;
                }

                $path = join_paths($outputDir, ltrim(join_paths(rtrim($path, '/'), 'index.html')));
                $relativePath = str_replace(getcwd(), '.', $path);

                $fs->ensureDirectoryExists($fs->dirname($path));

                $success = (bool) $fs->put($path, $response->getContent());

                if (! $success) {
                    $this->error("Failed to write {$relativePath}");
                } else {
                    $this->info("Successfully wrote {$relativePath}");
                }
            }
        }
    }
}
