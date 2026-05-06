<?php

namespace App\Enum;

enum EnumStatus: string
{
    case DRAFT = 'Brouillon';
    case PUBLISHED = 'Center aligned';
    case DELETED = 'RSupprimé';
}
