<?php

namespace App\Enum;

enum CommentStatus: string
{
    case pending = 'pending';
    case published = 'published';
    case moderated = 'moderated';
    
    public function getLabel(): string
    {
        return match ($this) {
            self::pending => 'En attente',
            self::published => 'Publié',
            self::moderated => 'Modéré',
        };
    }
}