<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\Contracts\Task;

class DeleteVendorScopedIdeHelperFileTask implements Task
{
    #[\Override]
    public function shouldRun(): bool
    {
        return File::exists(base_path('vendor/mpyw/laravel-local-class-scope/_laravel_ide_helper.php'));
    }

    #[\Override]
    public function run(): void
    {
        File::delete(base_path('vendor/mpyw/laravel-local-class-scope/_laravel_ide_helper.php'));
    }

    #[\Override]
    public function getSuccessMessage(): string
    {
        return 'Conflicting helper file at vendor/mpyw/laravel-local-class-scope/_laravel_ide_helper.php was deleted';
    }
}
