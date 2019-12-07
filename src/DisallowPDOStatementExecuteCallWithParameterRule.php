<?php

declare(strict_types=1);

namespace Sfp\PHPStan\PDO;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;

use function count;

/**
 * @implements Rule<MethodCall>
 */
class DisallowPDOStatementExecuteCallWithParameterRule implements Rule
{
    public function getNodeType() : string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope) : array
    {
        if (! $node->name instanceof Node\Identifier) {
            return [];
        }

        if ($node->name->toLowerString() !== 'execute') {
            return [];
        }

        $calledOnType = $scope->getType($node->var);

        if (! $calledOnType instanceof ObjectType) {
            return [];
        }

        if ($calledOnType->getClassName() !== 'PDOStatement') {
            return [];
        }

        if (count($node->args) === 0) {
            return [];
        }

        return [
            'PDOStatement::execute() with parameters is risky',
        ];
    }
}
