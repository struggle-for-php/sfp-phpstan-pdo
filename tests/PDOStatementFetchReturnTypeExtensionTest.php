<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\PDO;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Testing\TestCase;
use Sfp\PHPStan\PDO\PDOStatementFetchReturnTypeExtension;

final class PDOStatementFetchReturnTypeExtensionTest extends TestCase
{
    /** @var PDOStatementFetchReturnTypeExtension */
    private $extension;

    protected function setUp(): void
    {
        $this->extension = new PDOStatementFetchReturnTypeExtension();
    }

    /**
     * @test
     * @dataProvider provideFetchMethodCallPatterns
     */
    public function fetchMethodShouldReturn(array $args, callable $scopeResolveReturn, $expectedType) : void
    {
        $methodReflection = $this->createMock(MethodReflection::class);
        $scope = $this->createMock(Scope::class);
        $scope->method('getType')->will(
            self::returnCallback($scopeResolveReturn)
        );

        $methodCall = $this->createMock(Expr\MethodCall::class);
        $methodCall->var = $this->createMock(Expr::class);
        $methodCall->name = 'fetch';
        $methodCall->args = $args;

        $resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

        self::assertInstanceOf($expectedType, $resultType);
    }

    public function provideFetchMethodCallPatterns() : iterable
    {
        return [
            'PDO::FETCH_ASSOC' => [
                'args' => [
                    new Arg(new Expr\ClassConstFetch(
                        new FullyQualified('PDO'),
                        new Identifier('FETCH_ASSOC')
                    ))
                ],
                'scopeResolveReturn' => function (): Type {
                    return new ConstantIntegerType(\PDO::FETCH_ASSOC);
                },
                'expectedType' => ArrayType::class
            ],
            'PDO::FETCH_CLASS' => [
                'args' => [
                    new Arg(new Expr\ClassConstFetch(
                        new FullyQualified('PDO'),
                        new Identifier('FETCH_CLASS')
                    ))
                ],
                'scopeResolveReturn' => function (): Type {
                    return new ConstantIntegerType(\PDO::FETCH_CLASS);
                },
                'expectedType' => ObjectType::class
            ],
        ];
    }
}
