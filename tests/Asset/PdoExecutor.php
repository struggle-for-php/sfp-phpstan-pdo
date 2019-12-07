<?php

declare(strict_types=1);

namespace SfpTest\PHPStan\PDO\Asset;

use PDO;

class PdoExecutor
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function executeWithoutParameters() : void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM dual');
        $stmt->execute();
    }

    public function executeWithParameters() : void
    {
        $stmt = $this->pdo->prepare('SELECT * FROM dual');
        $stmt->execute(['bind' => 'var']);
    }
}
