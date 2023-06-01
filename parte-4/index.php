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

$sudoku->setFijas($fijas);
$sudoku->render();

// Soluciona Sudoku
$sudoku->llenarFilas();
$sudoku->render();
$sudoku->validarSolucion();
