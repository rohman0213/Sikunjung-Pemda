<?php

namespace App\Models;

use Database\Factories\PemohonFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'nik', 'nama', 'no_telepon', 'alamat'])]
class Pemohon extends Model
{
    /** @use HasFactory<PemohonFactory> */
    use HasFactory;

    protected $table = 'pemohon';

    /**
     * Email akun login milik pemohon.
     */
    public function emailAkun(): ?string
    {
        return User::where('id', $this->user_id)->value('email');
    }
}
