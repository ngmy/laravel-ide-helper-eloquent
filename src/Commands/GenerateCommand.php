<?php

declare(strict_types=1);

namespace Ngmy\LaravelIdeHelperEloquent\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

final class GenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ide-helper:eloquent-stub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an Eloquent stub file.';

    /**
     * Execute the console command.
     *
     * @return int The exit code
     */
    public function handle(): int
    {
        $parser = (new ParserFactory())->createForHostVersion();

        $ideHelperPath = base_path('_ide_helper.php');

        $ast = $parser->parse(File::get($ideHelperPath));

        if (null === $ast) {
            throw new \RuntimeException('Failed to parse the AST');
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(
            new class extends NodeVisitorAbstract {
                public function enterNode(Node $node)
                {
                    if ($node instanceof Class_ && 'Eloquent' === (string) $node->name) {
                        $node->name = new Identifier('Model');
                        $node->extends = null;

                        $ast = new Namespace_(new Name('Illuminate\Database\Eloquent'), [
                            $node,
                        ]);

                        $eloquentIdeHelperPath = base_path('_ide_helper_eloquent.php');

                        $printer = new Standard();

                        File::put($eloquentIdeHelperPath, $printer->prettyPrintFile([$ast]));
                    }

                    return null;
                }
            }
        );
        $traverser->traverse($ast);

        $this->info('A new Eloquent stub file was written to _ide_helper_eloquent.php');

        return Command::SUCCESS;
    }
}
