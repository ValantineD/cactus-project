<?php

namespace App\Enum;

enum EnumParticipationStatus: string
{
    case PENDING   = 'En cours';
    case CANCELLED  = 'Annulée';
    case CONFIRMED  = 'Validée';

}
