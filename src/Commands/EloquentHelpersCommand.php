<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Commands;

use Illuminate\Console\Command;
use Ngmy\LaravelIdeHelperEloquent\Tasks\AddScopedMethodTagTask;
use Ngmy\LaravelIdeHelperEloquent\Tasks\DeleteVendorScopedIdeHelperFileTask;
use Ngmy\LaravelIdeHelperEloquent\Tasks\GenerateEloquentIdeHelperTask;
use Ngmy\LaravelIdeHelperEloquent\Tasks\PublishScopedIdeHelperTask;
use Ngmy\LaravelIdeHelperEloquent\Tasks\RemoveScopedMethodTask;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

final class EloquentHelpersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ide-helper:eloquent-helpers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and update IDE Helper files to enhance autocompletion for Eloquent';

    /**
     * Execute the console command.
     *
     * @return int The exit code
     */
    public function handle(): int
    {
        $parser = (new ParserFactory())->createForHostVersion();
        $printer = new Standard();

        $tasks = [
            new RemoveScopedMethodTask($parser, $printer),
            new GenerateEloquentIdeHelperTask($parser, $printer),
            new AddScopedMethodTagTask($parser, $printer),
            new PublishScopedIdeHelperTask(),
            new DeleteVendorScopedIdeHelperFileTask(),
        ];

        foreach ($tasks as $task) {
            if (!$task->shouldRun()) {
                continue;
            }

            $task->run();

            $this->info($task->getSuccessMessage());
        }

        return Command::SUCCESS;
    }
}
