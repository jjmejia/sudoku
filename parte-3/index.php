<?php
/**
 * Script para la prueba y ejecución de la clase Sudoku.
 *
 */

require_once 'miSudoku.php';

// Aviso: Este Sudoku tiene mas de una solución!
$data = '
	..9|7..|3..
	..8|...|659
	.1.|...|7..
	-----------
	.96|837|1.4
	1..|...|...
	.2.|...|..3
	-----------
	...|..4|..1
	4..|..3|8..
	2..|9.6|..5
	';

$sudoku = new miSudoku();
// $sudoku->debug = true;
$sudoku->construirBase();
$sudoku->setFijas($data);
$sudoku->llenarFilas();
$sudoku->render();
$sudoku->validarSolucion();
