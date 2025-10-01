<?php

namespace App\Models;

use CodeIgniter\Model;

class AnggotaModel extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'id';

    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nama_depan',
        'nama_belakang',
        'gelar_depan',
        'gelar_belakang',
        'jabatan',
        'status_pernikahan',
        'jumlah_anak'
    ];

    protected $useTimestamps = false;
}
