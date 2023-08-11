<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestUser {
    public function getAuthIdentifierName() {
        return 'id';
    }

    public function getAuthIdentifier() {
        return null;
    }

    // Other required methods...
}