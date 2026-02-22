<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\NodeVisitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

class RemoveScopedMethodVisitor extends NodeVisitorAbstract
{
    /**
     * Whether the visitor is currently inside the target Eloquent class.
     */
    private bool $inTargetClass = false;

    #[\Override]
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_ && 'Eloquent' === (string) $node->name) {
            $this->inTargetClass = true;
        }

        return null;
    }

    #[\Override]
    public function leaveNode(Node $node)
    {
        if ($this->inTargetClass && $node instanceof ClassMethod && 'scoped' === $node->name->toString()) {
            return NodeVisitor::REMOVE_NODE;
        }

        if ($this->inTargetClass && $node instanceof Class_ && 'Eloquent' === (string) $node->name) {
            $this->inTargetClass = false;
        }

        return null;
    }
}
