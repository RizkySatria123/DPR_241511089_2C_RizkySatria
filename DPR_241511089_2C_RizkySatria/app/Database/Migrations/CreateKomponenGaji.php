<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKomponenGaji extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_komponen' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['gaji', 'tunjangan'],
                'default'    => 'gaji',
            ],
            'nominal_default' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => '0.00',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_komponen', true);
        $this->forge->createTable('komponen_gaji', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('komponen_gaji', true);
    }
}
