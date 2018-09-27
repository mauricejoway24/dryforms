<?php
namespace App\Services\Dew;

use Illuminate\Database\Connection;

class CalculationService
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * CalculationService constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $temperature
     * @param int $rh
     * @return int
     */
    public function calculate(int $temperature, int $rh): ?int
    {
        $calculationResult = $this->connection->table('dew_calculations')
            ->where([
                'temperature' => $temperature,
                'rh' => $rh
            ])->first();

        return $calculationResult ? $calculationResult->result : null;
    }
}