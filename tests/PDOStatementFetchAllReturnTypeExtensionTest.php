<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\PDO;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\TestCase;
use PHPStan\Type\ArrayType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use Sfp\PHPStan\PDO\PDOStatementFetchAllReturnTypeExtension;
use SfpTest\PHPStan\PDO\Asset\Entity;

final class PDOStatementFetchAllReturnTypeExtensionTest extends TestCase
{
    /** @var \PhpParser\PrettyPrinter\Standard() */
    private $printer;
    /** @var \PHPStan\Analyser\TypeSpecifier */
    private $typeSpecifier;
    /** @var Scope */
    private $scope;
    /** @var PDOStatementFetchAllReturnTypeExtension */
    private $extension;
    /** @var MethodReflection|\PHPUnit\Framework\MockObject\MockObject */
    private $methodReflection;

    protected function setUp() : void
    {
        $broker = $this->createBroker();
        $this->printer = new \PhpParser\PrettyPrinter\Standard();
        $this->typeSpecifier = $this->createTypeSpecifier($this->printer, $broker);
        $scope = $this->createScopeFactory($broker, $this->typeSpecifier)->create(ScopeContext::create(''));
        $scope = $scope->assignVariable('pdoStatement', new ObjectType(\PDOStatement::class));
        $scope = $scope->assignVariable('entityClassName', new StringType(Entity::class));

        $this->scope = $scope;

        $this->extension = new PDOStatementFetchAllReturnTypeExtension();
        $this->methodReflection = $this->createMock(MethodReflection::class);
    }

    /**
     * @test
     * @dataProvider provideFetchMethodCallPatterns
     */
    public function fetchMethodShouldReturn(Expr\MethodCall $methodCall, $expectedType) : void
    {
        $resultType = $this->extension->getTypeFromMethodCall($this->methodReflection, $methodCall, $this->scope);

        self::assertInstanceOf($expectedType, $resultType);
    }

    public function provideFetchMethodCallPatterns() : iterable
    {
        return [
            'PDO::FETCH_CLASS-with-string-class-name' => [
                new Expr\MethodCall(
                    new Expr\Variable('pdoStatement'),
                    'fetchAll',
                    [
                        new Arg(
                            new Expr\ClassConstFetch(
                                new FullyQualified('PDO'),
                                new Identifier('FETCH_CLASS')
                            )
                        ),
                        new Arg(
                            new String_(
                                'SfpTest\PHPStan\PDO\Asset\Entity'
                            )
                        )
                    ]
                ),
                'expectedType'       => ArrayType::class,
            ],
            'PDO::FETCH_CLASS-with-class-const' => [
                new Expr\MethodCall(
                    new Expr\Variable('pdoStatement'),
                    'fetchAll',
                    [
                        new Arg(
                            new Expr\ClassConstFetch(
                                new FullyQualified('PDO'),
                                new Identifier('FETCH_CLASS')
                            )
                        ),
                        new Arg(
                            new Expr\ClassConstFetch(
                                new FullyQualified(Entity::class),
                                new Identifier('class'),
                            )
                        )
                    ]
                ),
                'expectedType'       => ArrayType::class,
            ],
            'PDO::FETCH_CLASS-with-variable-class-string' => [
                new Expr\MethodCall(
                    new Expr\Variable('pdoStatement'),
                    'fetchAll',
                    [
                        new Arg(
                            new Expr\ClassConstFetch(
                                new FullyQualified('PDO'),
                                new Identifier('FETCH_CLASS')
                            )
                        ),
                        new Arg(
                            new Expr\Variable('entityClassName')
                        )
                    ]
                ),
                'expectedType'       => ArrayType::class,
            ]
        ];
    }
}
