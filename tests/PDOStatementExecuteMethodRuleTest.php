<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\PDO;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\PDO\PDOStatementExecuteMethodRule;

/**
 * @extends RuleTestCase<PDOStatementExecuteMethodRule>
 */
final class PDOStatementExecuteMethodRuleTest extends RuleTestCase
{
    protected function getRule() : Rule
    {
        return new PDOStatementExecuteMethodRule();
    }

    /** @test */
    public function processNode() : void
    {
        $this->analyse(
            [
                __DIR__ . '/Asset/PdoExecutor.php',
            ],
            [
                [
                    'PDOStatement::execute() with parameters is risky',
                    28,
                ],
            ]
        );
    }
}
