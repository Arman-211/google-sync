<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['google_sheet_url'];

    public function extractSpreadsheetId(): ?string
    {
        preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $this->google_sheet_url, $matches);
        return $matches[1] ?? null;
    }
}
