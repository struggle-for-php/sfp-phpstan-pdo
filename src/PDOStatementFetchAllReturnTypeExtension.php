<?php

declare(strict_types=1);

namespace Sfp\PHPStan\PDO;

use PDO;
use PDOStatement;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use stdClass;

use function count;
//use PHPStan\Type\Type;

/**
 * @see http://php.net/pdostatement.fetchall
 */
final class PDOStatementFetchAllReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass() : string
    {
        return PDOStatement::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection) : bool
    {
        return $methodReflection->getName() === 'fetchAll';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope) : Type\Type
    {
        $fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE;

        if (count($methodCall->args) > 0) {
            $arg            = $methodCall->args[0]->value;
            $fetchStyleType = $scope->getType($arg);

            if ($fetchStyleType instanceof Type\Constant\ConstantIntegerType) {
                $fetchStyle = $fetchStyleType->getValue();
            }

            // todo handle other types
        }

        $className = stdClass::class;
        if (isset($methodCall->args[1])) {
            $classNameString = $methodCall->args[1]->value;
            assert($classNameString instanceof String_);
            $className = $classNameString->value;
        }

        // todo resolve Bitwise Or
        switch ($fetchStyle) {
            case PDO::FETCH_ASSOC: // 2
                return new Type\ArrayType(new Type\StringType(), new Type\StringType());
            case PDO::FETCH_NUM: // 3
                return new Type\ArrayType(new Type\IntegerType(), new Type\StringType());
            case PDO::FETCH_CLASS: //8
                return new Type\ArrayType(
                    new Type\IntegerType(), new Type\ObjectType($className)
                );
        }

        //        return $scope->getType($arg);
    }
}
