<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;
class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            'Rojo', 'Azul', 'Verde', 'Amarillo', 'Naranja', 'Negro', 'Blanco', 'Gris', 'Violeta', 'Rosa',
            'Verde oliva', 'Cian', 'Turquesa', 'Marrón', 'Beige', 'Lavanda', 'Púrpura', 'Fucsia', 'Lima',
            'Papel', 'Aguamarina', 'Dorado', 'Plata', 'Bronce', 'Lila', 'Magenta', 'Salmon', 'Terracota',
            'Verde menta', 'Indigo', 'Café', 'Siena', 'Lima oscuro', 'Azul claro', 'Pistacho', 'Nuez', 'Palo rosa',
            'Mostaza', 'Caramelo', 'Coral', 'Lavanda claro', 'Cobalto', 'Azul turquesa', 'Verde esmeralda', 
            'Marfil', 'Hielo', 'Rojo oscuro', 'Beige oscuro', 'Fucsia oscuro', 'Gris claro', 'Gris oscuro',
            'Menta', 'Rojo brillante', 'Azul marino', 'Gris pálido', 'Rojo anaranjado', 'Verde bosque', 
            'Carmesí', 'Naranja brillante', 'Amarillo pastel', 'Salmón claro', 'Vino', 'Ámbar', 'Azul océano',
            'Verde pasto', 'Ocre', 'Violeta oscuro', 'Rosa pálido'
        ];

        foreach ($colors as $color) {
            Color::create(['color' => $color]);
        }
    }
}
