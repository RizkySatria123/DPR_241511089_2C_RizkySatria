<?php

namespace App\Models;

use CodeIgniter\Model;

class KomponenGajiModel extends Model
{
    protected $table          = 'komponen_gaji';
    protected $primaryKey     = 'id_komponen_gaji';
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nama_komponen',
        'kategori',
        'jabatan',
        'nominal',
        'satuan',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nama_komponen' => 'required|string|min_length[3]|max_length[150]',
        'kategori'      => 'required|string|max_length[50]',
        'jabatan'       => 'required|string|max_length[50]',
        'nominal'       => 'required|decimal',
        'satuan'        => 'required|in_list[Hari,Bulan,Periode]',
    ];

    protected $validationMessages = [
        'nominal' => [
            'decimal' => 'Nominal harus berupa angka yang valid.',
        ],
    ];
}
