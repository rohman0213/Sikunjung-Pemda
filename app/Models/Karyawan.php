<?php

namespace App\Models;

use Database\Factories\KaryawanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'nik', 'nama', 'jabatan', 'no_telepon'])]
class Karyawan extends Model
{
    /** @use HasFactory<KaryawanFactory> */
    use HasFactory;

    protected $table = 'karyawan';

    /**
     * Email akun login milik karyawan.
     */
    public function emailAkun(): ?string
    {
        return User::where('id', $this->user_id)->value('email');
    }
}
