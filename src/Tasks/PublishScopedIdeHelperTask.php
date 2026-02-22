<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\Contracts\Task;

class PublishScopedIdeHelperTask implements Task
{
    #[\Override]
    public function shouldRun(): bool
    {
        return File::exists(base_path('vendor/mpyw/laravel-local-class-scope'));
    }

    #[\Override]
    public function run(): void
    {
        $src = __DIR__.'/../../stubs/_ide_helper_eloquent_scoped.stub';
        $dest = base_path('_ide_helper_eloquent_scoped.php');

        File::copy($src, $dest);
    }

    #[\Override]
    public function getSuccessMessage(): string
    {
        return 'A new helper file for scoped() methods was written to _ide_helper_eloquent_scoped.php';
    }
}
