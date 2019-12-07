<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\PDO;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use Sfp\PHPStan\PDO\DisallowPDOStatementExecuteCallWithParameterRule;

/**
 * @extends RuleTestCase<DisallowPDOStatementExecuteCallWithParameterRule>
 */
final class DisallowPDOStatementExecuteCallWithParameterRuleTest extends RuleTestCase
{
    protected function getRule() : Rule
    {
        return new DisallowPDOStatementExecuteCallWithParameterRule();
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
