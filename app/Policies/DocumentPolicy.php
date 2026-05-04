<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function download(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function stream(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
