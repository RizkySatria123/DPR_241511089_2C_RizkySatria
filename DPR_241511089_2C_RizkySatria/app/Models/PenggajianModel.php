<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class PenggajianModel extends Model
{
    protected $table            = 'penggajian';
    protected $primaryKey       = null;
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_anggota',
        'id_komponen_gaji',
    ];
    protected $useTimestamps    = false;

    private function penggajianBuilder(): BaseBuilder
    {
        return $this->db->table($this->table);
    }

    /**
     * Mendapatkan daftar komponen gaji yang sudah dimiliki oleh seorang anggota.
     *
     * @return list<array<string, mixed>>
     */
    public function getAssignmentsForAnggota(int $anggotaId): array
    {
        return $this->penggajianBuilder()
            ->select([
                'penggajian.id_anggota',
                'penggajian.id_komponen_gaji',
                'komponen_gaji.nama_komponen',
                'komponen_gaji.kategori',
                'komponen_gaji.jabatan',
                'komponen_gaji.nominal',
                'komponen_gaji.satuan',
            ])
            ->join('komponen_gaji', 'komponen_gaji.id_komponen_gaji = penggajian.id_komponen_gaji', 'left')
            ->where('penggajian.id_anggota', $anggotaId)
            ->orderBy('komponen_gaji.nama_komponen', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil daftar ID komponen yang sudah dimiliki oleh anggota.
     *
     * @return list<int>
     */
    public function getAssignedKomponenIds(int $anggotaId): array
    {
        $rows = $this->penggajianBuilder()
            ->select('id_komponen_gaji')
            ->where('id_anggota', $anggotaId)
            ->get()
            ->getResultArray();

        return array_map(static fn (array $row): int => (int) $row['id_komponen_gaji'], $rows);
    }

    public function exists(int $anggotaId, int $komponenId): bool
    {
        return $this->penggajianBuilder()
            ->where([
                'id_anggota'       => $anggotaId,
                'id_komponen_gaji' => $komponenId,
            ])
            ->countAllResults() > 0;
    }

    public function assign(int $anggotaId, int $komponenId): bool
    {
        return (bool) $this->penggajianBuilder()->insert([
            'id_anggota'       => $anggotaId,
            'id_komponen_gaji' => $komponenId,
        ]);
    }

    public function remove(int $anggotaId, int $komponenId): bool
    {
        return (bool) $this->penggajianBuilder()
            ->where([
                'id_anggota'       => $anggotaId,
                'id_komponen_gaji' => $komponenId,
            ])
            ->delete();
    }

    public function countAllAssignments(): int
    {
        return $this->penggajianBuilder()->countAllResults();
    }

    public function countDistinctAnggota(): int
    {
        $row = $this->penggajianBuilder()
            ->select('COUNT(DISTINCT id_anggota) AS total')
            ->get()
            ->getRow();

        return $row ? (int) $row->total : 0;
    }

    /**
     * Menghasilkan ringkasan take home pay seluruh anggota.
     *
     * @return list<array<string, mixed>>
     */
    public function getTakeHomeSummary(): array
    {
        $builder = $this->db->table('anggota AS a');

        $builder
            ->select([
                'a.id_anggota',
                'a.nama_depan',
                'a.nama_belakang',
                'a.gelar_depan',
                'a.gelar_belakang',
                'a.jabatan',
                'a.status_pernikahan',
                'a.jumlah_anak',
                'COUNT(p.id_komponen_gaji) AS total_komponen',
                'COALESCE(SUM(k.nominal), 0) AS total_nominal',
            ])
            ->join('penggajian AS p', 'p.id_anggota = a.id_anggota', 'left')
            ->join('komponen_gaji AS k', 'k.id_komponen_gaji = p.id_komponen_gaji', 'left')
            ->groupBy('a.id_anggota')
            ->orderBy('a.nama_depan', 'ASC')
            ->orderBy('a.nama_belakang', 'ASC');

        return $builder->get()->getResultArray();
    }
}
