<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\NodeVisitors\RemoveScopedMethodVisitor;
use PhpParser\NodeVisitor;

class RemoveScopedMethodTask extends AbstractAstTask
{
    #[\Override]
    public function shouldRun(): bool
    {
        return File::exists(base_path('vendor/mpyw/laravel-local-class-scope'))
            && File::exists(base_path('_ide_helper.php'));
    }

    #[\Override]
    public function getSuccessMessage(): string
    {
        return 'Eloquent::scoped() method was removed from _ide_helper.php';
    }

    #[\Override]
    protected function getInputPath(): string
    {
        return base_path('_ide_helper.php');
    }

    #[\Override]
    protected function getOutputPath(): string
    {
        return base_path('_ide_helper.php');
    }

    #[\Override]
    protected function getVisitor(): NodeVisitor
    {
        return new RemoveScopedMethodVisitor();
    }
}
