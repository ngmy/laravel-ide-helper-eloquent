<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\NodeVisitors\EloquentStubVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

final class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ide-helper:eloquent-stub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an Eloquent stub file.';

    /**
     * Execute the console command.
     *
     * @return int The exit code
     */
    public function handle(): int
    {
        $parser = (new ParserFactory())->createForHostVersion();

        $ideHelperPath = base_path('_ide_helper.php');

        $ast = $parser->parse(File::get($ideHelperPath));

        if (null === $ast) {
            throw new \RuntimeException('Failed to parse the AST');
        }

        $outputFileName = '_ide_helper_eloquent.php';
        $outputFilePath = base_path($outputFileName);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new EloquentStubVisitor($outputFilePath));
        $traverser->traverse($ast);

        $this->info("A new Eloquent stub file was written to {$outputFileName}");

        return Command::SUCCESS;
    }
}
