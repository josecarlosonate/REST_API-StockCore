<?php

namespace App\Enums;

enum CustomerDocumentType: string
{
    case CC = 'CC';
    case CE = 'CE';
    case NIT = 'NIT';
    case PASSPORT = 'PASSPORT';
    case DNI = 'DNI';
}
