<?php

namespace App\Enums\Training;

enum Type: string
{
    case TRAINING_CREATED = 'TRAINING_CREATED';
    case TRAINING_UPDATED = 'TRAINING_UPDATED';
    case TRAINING_COMPLETED = 'TRAINING_COMPLETED';
}
