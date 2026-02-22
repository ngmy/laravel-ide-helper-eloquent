<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\NodeVisitors;

use Ngmy\LaravelIdeHelperEloquent\Contracts\AstProvider;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class GenerateEloquentIdeHelperVisitor extends NodeVisitorAbstract implements AstProvider
{
    /**
     * The generated AST.
     *
     * @var Namespace_[]
     */
    private array $generatedAst = [];

    #[\Override]
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_ && 'Eloquent' === (string) $node->name) {
            $this->generatedAst[] = $this->createModelAst($node);
            $this->generatedAst[] = $this->createRelationAst($node);
        }

        return null;
    }

    /**
     * @return Namespace_[] The generated AST
     */
    #[\Override]
    public function getGeneratedAst(): array
    {
        return $this->generatedAst;
    }

    /**
     * Create the Model class AST.
     *
     * @param Class_ $node The original Eloquent class node
     *
     * @return Namespace_ The Model class AST
     */
    private function createModelAst(Class_ $node): Namespace_
    {
        $modelNode = clone $node;
        $modelNode->name = new Identifier('Model');
        $modelNode->extends = null;

        return new Namespace_(
            new Name('Illuminate\Database\Eloquent'),
            [
                $modelNode,
            ],
        );
    }

    /**
     * Create the Relation class AST.
     *
     * @param Class_ $node The original Eloquent class node
     *
     * @return Namespace_ The Relation class AST
     */
    private function createRelationAst(Class_ $node): Namespace_
    {
        $relationNode = clone $node;
        $relationNode->name = new Identifier('Relation');
        $relationNode->extends = null;

        $docComment = new Doc(
            <<<'PHP'
                /**
                 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
                 * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
                 * @template TResult
                 */
                PHP
        );
        $relationNode->setDocComment($docComment);

        $newStmts = [];

        foreach ($relationNode->stmts as $stmt) {
            if (!$stmt instanceof ClassMethod) {
                $newStmts[] = $stmt;

                continue;
            }

            $method = clone $stmt;

            $docComment = $method->getDocComment();

            if (null === $docComment) {
                $newStmts[] = $method;

                continue;
            }

            $docCommentText = $docComment->getText();

            $newDocCommentText = preg_replace(
                [
                    '/\bTModel\b/',
                    '/\b(?<!@)static\b/',
                ],
                [
                    'TRelatedModel',
                    'TRelatedModel',
                ],
                $docCommentText,
            );

            if (null === $newDocCommentText) {
                $pcreError = preg_last_error();

                throw new \RuntimeException(
                    \sprintf(
                        'Failed to replace DocComment for method %s due to PCRE error %d',
                        $method->name->name,
                        $pcreError,
                    )
                );
            }

            $newDocComment = new Doc($newDocCommentText);
            $method->setDocComment($newDocComment);

            $newStmts[] = $method;
        }

        $relationNode->stmts = $newStmts;

        return new Namespace_(
            new Name('Illuminate\Database\Eloquent\Relations'),
            [
                $relationNode,
            ],
        );
    }
}
