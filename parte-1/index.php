<?php
/**
 * Script para la prueba y ejecución de la clase Sudoku.
 *
 */

require_once 'miSudoku.php';

$sudoku = new miSudoku();

$sudoku->construirBase();

$sudoku->render();
