<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\PrettyPrinters;

use PhpParser\Node\Stmt\Namespace_;
use PhpParser\PrettyPrinter\Standard as BaseStandard;

class Standard extends BaseStandard
{
    #[\Override]
    protected function pStmt_Namespace(Namespace_ $node): string
    {
        return 'namespace'.(null !== $node->name ? ' '.$this->p($node->name) : '')
                 .' {'.$this->pStmts($node->stmts).$this->nl.'}';
    }
}
