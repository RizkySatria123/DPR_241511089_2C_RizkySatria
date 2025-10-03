<?php

namespace App\Models;

use CodeIgniter\Model;

class KomponenGajiModel extends Model
{
    protected $table            = 'komponen_gaji';
    protected $primaryKey       = 'id_komponen';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nama', 'kategori', 'nominal_default', 'deskripsi'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'nama'            => 'required|string|min_length[3]|max_length[100]',
        'kategori'        => 'required|in_list[gaji,tunjangan]',
        'nominal_default' => 'required|decimal',
        'deskripsi'       => 'permit_empty|string',
    ];

    protected $validationMessages = [
        'nominal_default' => [
            'decimal' => 'Nominal harus berupa angka yang valid.',
        ],
    ];
}
