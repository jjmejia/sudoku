<?php
/**
 * Script para la prueba y ejecución de la clase Sudoku.
 *
 */

require_once 'miSudoku.php';

$sudoku = new miSudoku();
// $sudoku->debug = true;
// $sudoku->base = 2;

$data = $sudoku->nuevo();
$fijas = $data['dificil'];
// $fijas = $data['medio'];
// $fijas = $data['facil'];
// $sudoku->debug = false;

$sudoku->setFijas($fijas);
$sudoku->render();

// Soluciona Sudoku recién creado
$sudoku->llenarFilas();
$sudoku->render();
// Confirma la solución y que el valor encontrado sea el esperado
$sudoku->validarSolucion(false, $data['checksum-solucion']);
