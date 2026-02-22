<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\NodeVisitors\GenerateEloquentIdeHelperVisitor;
use PhpParser\NodeVisitor;

class GenerateEloquentIdeHelperTask extends AbstractAstTask
{
    #[\Override]
    public function shouldRun(): bool
    {
        return File::exists(base_path('_ide_helper.php'));
    }

    #[\Override]
    public function getSuccessMessage(): string
    {
        return 'A new helper file for Eloquent was written to _ide_helper_eloquent.php';
    }

    #[\Override]
    protected function getInputPath(): string
    {
        return base_path('_ide_helper.php');
    }

    #[\Override]
    protected function getOutputPath(): string
    {
        return base_path('_ide_helper_eloquent.php');
    }

    #[\Override]
    protected function getVisitor(): NodeVisitor
    {
        return new GenerateEloquentIdeHelperVisitor();
    }
}
