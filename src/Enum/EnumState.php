<?php

namespace App\Enum;

enum EnumState: string
{
    case OPEN       = 'Rejoignable';
    case IN_PROGRESS = 'En cours';
    case FULL       = 'Complet';
    case CLOSED     = 'Terminé';
}
