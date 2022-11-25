<?php

namespace App\Common\Validation;

enum UniqueRecordMode
{
    case CREATE;
    case UPDATE;
}
