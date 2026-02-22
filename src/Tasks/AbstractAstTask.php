<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Tasks;

use Illuminate\Support\Facades\File;
use Ngmy\LaravelIdeHelperEloquent\Contracts\AstProvider;
use Ngmy\LaravelIdeHelperEloquent\Contracts\Task;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

abstract class AbstractAstTask implements Task
{
    /**
     * Constructor.
     *
     * @param Parser   $parser  The PHP parser to use for parsing code
     * @param Standard $printer The pretty printer to use for generating code
     */
    public function __construct(
        protected readonly Parser $parser,
        protected readonly Standard $printer,
    ) {}

    #[\Override]
    public function run(): void
    {
        $inputPath = $this->getInputPath();

        if (!File::exists($inputPath)) {
            return;
        }

        $code = File::get($inputPath);
        $ast = $this->parser->parse($code);

        if (null === $ast) {
            throw new \RuntimeException("Failed to parse: {$inputPath}");
        }

        $visitor = $this->getVisitor();
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);

        $traversedAst = $traverser->traverse($ast);

        // Normally, we use the traversed AST, but for visitors that generate new ASTs, we can extract the result
        $finalAst = $this->getFinalAst($traversedAst, $visitor);

        File::put($this->getOutputPath(), $this->printer->prettyPrintFile($finalAst));
    }

    /**
     * Get the input file path.
     *
     * @return string The input file path
     */
    abstract protected function getInputPath(): string;

    /**
     * Get the output file path.
     *
     * @return string The output file path
     */
    abstract protected function getOutputPath(): string;

    /**
     * Get the NodeVisitor instance to use for traversal.
     *
     * @return NodeVisitor The NodeVisitor instance to use for traversal
     */
    abstract protected function getVisitor(): NodeVisitor;

    /**
     * Get the final AST to be pretty-printed after traversal.
     *
     * @param Node[]      $traversedAst The AST after traversal
     * @param NodeVisitor $visitor      The visitor used for traversal
     *
     * @return Node[] The final AST to be pretty-printed after traversal
     */
    protected function getFinalAst(array $traversedAst, NodeVisitor $visitor): array
    {
        if ($visitor instanceof AstProvider) {
            return $visitor->getGeneratedAst();
        }

        return $traversedAst;
    }
}
