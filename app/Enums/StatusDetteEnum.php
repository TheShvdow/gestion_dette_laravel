<?php

namespace App\Enums;

class StatusDetteEnum
{
    const SOLDE = 'Solde';
    const NON_SOLDE = 'NonSolde';

    public static function getAllStatuses(): array
    {
        return [
            self::SOLDE,
            self::NON_SOLDE,
        ];
    }
}
