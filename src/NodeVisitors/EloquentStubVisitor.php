<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\NodeVisitors;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\PrettyPrinters\Standard;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

class EloquentStubVisitor extends NodeVisitorAbstract
{
    /**
     * The output ASTs.
     *
     * @var list<Namespace_>
     */
    private array $outputAsts = [];

    /**
     * Constructor.
     *
     * @param string $outputFilePath The output file path
     */
    public function __construct(
        private readonly string $outputFilePath,
    ) {}

    #[\Override]
    public function enterNode(Node $node)
    {
        if ($node instanceof Class_ && 'Eloquent' === (string) $node->name) {
            $this->outputAsts[] = $this->createModelAst($node);
            $this->outputAsts[] = $this->createRelationAst($node);
        }

        return null;
    }

    #[\Override]
    public function afterTraverse(array $nodes)
    {
        $this->writeOutputFile();

        return null;
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

        foreach ($relationNode->getMethods() as $method) {
            $docComment = $method->getDocComment();

            if (null === $docComment) {
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
        }

        return new Namespace_(
            new Name('Illuminate\Database\Eloquent\Relations'),
            [
                $relationNode,
            ],
        );
    }

    /**
     * Write the output ASTs to the output file.
     */
    private function writeOutputFile(): void
    {
        if (empty($this->outputAsts)) {
            return;
        }

        $printer = new Standard();

        File::put($this->outputFilePath, $printer->prettyPrintFile($this->outputAsts));
    }
}
