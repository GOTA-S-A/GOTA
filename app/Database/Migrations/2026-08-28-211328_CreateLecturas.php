<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturas extends Migration
{
    public function up()
    {
        // Definimos cada columna de la tabla "lecturas"
        $this->forge->addField([
            // Llave primaria autoincremental
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // Referencia al contador que se leyó.
            //  por ahora es un INT normal, SIN llave foránea,
            // porque la tabla "contadores" todavía no existe en el repo
            // Cuando esa tabla exista, se agrega la FK en una migración aparte.
            'contador_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // Lectura anterior del contador (la última que se había registrado)
            'lectura_anterior' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],

            // Lectura actual que ingresa el Lector desde el celular
            'lectura_actual' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],

            // Consumo calculado = lectura_actual - lectura_anterior
            // Lo guardamos ya calculado (no como fórmula en la BD) para
            // que quede "congelado" el valor histórico, aunque después
            // cambien las lecturas de otros períodos.
            'consumo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],

            // Referencia a la tarifa que estaba vigente en la fecha de esta lectura.
            // Mismo caso que contador_id: sin FK todavía, porque "tarifas"
            // tampoco existe aún en el repo.
            'tarifa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // permitimos null temporalmente mientras no exista tarifas
            ],

            // Monto a cobrar = consumo * tarifa aplicada
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],

            // Fecha en que se tomó la lectura
            'fecha' => [
                'type' => 'DATE',
            ],

            // Quién (qué usuario con rol Lector) registró esta lectura.
            'usuario_lector_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // Timestamps estándar de creación/actualización del registro
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Marcamos "id" como llave primaria
        $this->forge->addPrimaryKey('id');

        // Esta FK sí la podemos crear ya, porque "usuarios" existe en el repo
        $this->forge->addForeignKey('usuario_lector_id', 'usuarios', 'id', 'CASCADE', 'RESTRICT');

        // Creamos la tabla física en la base de datos
        $this->forge->createTable('lecturas');
    }

    public function down()
    {
        // Comando para deshacer esta migración si hace falta (borra la tabla)
        $this->forge->dropTable('lecturas');
    }
}