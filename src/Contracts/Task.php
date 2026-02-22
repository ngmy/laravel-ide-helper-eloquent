<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Contracts;

interface Task
{
    /**
     * Determine if the task should be run.
     *
     * @return bool True if the task should be run, false otherwise
     */
    public function shouldRun(): bool;

    /**
     * Run the task.
     */
    public function run(): void;

    /**
     * Get the success message to display after the task is completed.
     */
    public function getSuccessMessage(): string;
}
