<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\NodeVisitors;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;

class AddScopedMethodTagVisitor extends NodeVisitorAbstract
{
    #[\Override]
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_) {
            $this->addScopedMethodTag($node);
        }

        return null;
    }

    /**
     * Add the @method tag for the scoped method to the model class's doc comment.
     *
     * @param Class_ $node The original model class node
     */
    private function addScopedMethodTag(Class_ $node): void
    {
        $doc = $node->getDocComment();

        $methodLine = ' * @method static \Illuminate\Database\Eloquent\Builder<static> scoped(\Illuminate\Database\Eloquent\Scope|class-string<\Illuminate\Database\Eloquent\Scope> $scope, mixed ...$parameters) Apply Scope to Eloquent\Builder.';

        if (null === $doc) {
            // If there is no doc comment, create a new one with the method tag
            $newDocText = "/**\n{$methodLine}\n */";
        } else {
            $docText = $doc->getText();

            // Remove existing scoped lines (remove all if there are multiple)
            $newDocText = preg_replace(
                '/^\s*\*\s*@method\s+static\s+\\\?Illuminate\\\Database\\\Eloquent\\\Builder<static>\s+scoped\([^)]*\).*$/m',
                '',
                $docText,
            ) ?? $docText;

            // Add the new method line just before the closing */ of the doc comment
            $newDocText = preg_replace(
                '/\s*\*\/$/',
                "\n{$methodLine}\n */",
                $newDocText,
            ) ?? $newDocText;
        }

        $node->setDocComment(new Doc($newDocText));
    }
}
