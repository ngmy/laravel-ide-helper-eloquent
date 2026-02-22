<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Contracts;

use PhpParser\Node;

interface AstProvider
{
    /**
     * Get the generated AST.
     *
     * @return Node[] The generated AST
     */
    public function getGeneratedAst(): array;
}
