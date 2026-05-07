<?php

namespace App\Enum;

enum EnumStatus: string
{
    case DRAFT = 'Brouillon';
    case PUBLISHED = 'Publié';
    case DELETED = 'Supprimé';
}
