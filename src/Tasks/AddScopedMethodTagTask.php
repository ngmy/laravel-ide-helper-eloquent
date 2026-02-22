<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\NodeVisitors\AddScopedMethodTagVisitor;
use PhpParser\NodeVisitor;

class AddScopedMethodTagTask extends AbstractAstTask
{
    #[\Override]
    public function shouldRun(): bool
    {
        return File::exists(base_path('vendor/mpyw/laravel-local-class-scope'))
            && File::exists(base_path('_ide_helper_models.php'));
    }

    #[\Override]
    public function getSuccessMessage(): string
    {
        return '@method tags for scoped() were added to _ide_helper_models.php';
    }

    #[\Override]
    protected function getInputPath(): string
    {
        return base_path('_ide_helper_models.php');
    }

    #[\Override]
    protected function getOutputPath(): string
    {
        return base_path('_ide_helper_models.php');
    }

    #[\Override]
    protected function getVisitor(): NodeVisitor
    {
        return new AddScopedMethodTagVisitor();
    }
}
