<?php
/**
 * Algoritmo para creación y solución de Sudokus.
 *
 * https://en.wikipedia.org/wiki/Sudoku_solving_algorithms
 *
 * > Un Sudoku estándar contiene 81 celdas, dispuestas en una trama de 9×9, que está subdividida en nueve cajas.
 * > Cada caja está determinada por la intersección de tres filas con tres columnas. Cada celda puede contener un
 * > número del uno al nueve y cada número solo puede aparecer una vez en cada fila, cada columna o cada caja. Un
 * > sudoku comienza con algunas celdas ya completas, conteniendo números (pistas), y el objetivo es rellenar las
 * > celdas restantes. Los sudokus bien planteados tienen una única solución. Los jugadores e investigadores pueden
 * > utilizar una amplia gama de algoritmos de ordenador para resolver sudokus, estudiar sus propiedades y hacer
 * > rompecabezas nuevos, incluyendo sudokus con simetrías interesantes y otras propiedades.
 *
 */

class miSudoku {

	// Numero de celdas a contener en cada grupo de celdas. Esto es, el tablero tendrá
	// $this->base cajas a lo ancho, cada una con $this->base celdas. Así, si la base es 2
	// el número de celdas a lo ancho (así como el número de celdas en cada caja) es de 4,
	// entanto que para base 3 el número de celdas es 9.
	public $base = 3;

	// Arreglo bidimensional con la información del tablero de Sudoku
	private $tablero = array();

	// Usado para recuperar rápidamente a qué caja pertenece cada celda y para administrar
	// las celdas pertenecientes a una misma caja.
	private $cajas = array();

	// TRUE para usar un listado de números disponibles (para llenado de celdas) en orden aleatorio.
	// FALSE para manejar los números siempre en orden (1,2,3...).
	public $randomBase = true;

	// Cantidad de iteraciones realizadas para llenar el tablero.
	public $ciclos = 0;

	// Cantidad máxima de iteraciones permitidas (previene ciclos infinitos para Sudokus sin solución).
	public $maxCiclos = 1000000;

	// Nivel del Sudoku (para creación de Sudokus, no para la solución).
	public $level = 2; // <=1 easy, 2=medium, 3=hard

	// TRUE para visualizar información adicional y/o mensajes de error en línea.
	public $debug = false;

	// TRUE cuando $this->render() ha sido ya invocado (no repite listado de estilos)
	private $publicado = false;

	// Mensajes de error encontrados al llenar el tablero.
	private $infoerror = '';

	/**
	 * Retorna el ancho/alto del tablero (4 o 9, usualmente), según la base usada (2 o 3 respectivamente).
	 * Tener presente que el tablero de Sudoku debe tener el mismo número de celdas de ancho que de alto.
	 *
	 * @return int Número de celdas de ancho/alto.
	 */
	public function anchoTablero() {

		return $this->base * $this->base;
	}

	/**
	 * Construye base para el tablero de Sudoku.
	 * Las opciones validas para llenar cada celda son los números de 1 en adelante. El valor máximo
	 * depende de la base usada (4 o 9, usualmente para una base de 2 o 3 respectivamente).
	 *
	 * @param bool $randomBase TRUE para aleatorizar la lista de valores disponibles para una celda.
	 */
	public function construirBase(bool $randomBase = false) {

		// Limpia varibles
		$this->tablero = array();
		// Genera listado con los numeros 1,2,...
		$disponibles = implode('', range(1, $this->anchoTablero()));

		// Aleatoriza la cadena base.
		// NOTA: Una prueba de solución mostró que al no aleatorizar, siempre reportaba 531 ciclos al solucionarlo,
		// en tanto que al aleatorizar podian ser 99, 101, 633 u otro, dependiendo de la cadena generada. Esto
		// significa que aunque aleatorizar no garantiza una solución más rápida, si puede resultar en una.
		if ($this->randomBase || $randomBase) {
			$disponibles = str_shuffle($disponibles);
		}

		// Llena tablero y disponibles
		for ($x = 0; $x < $this->base; $x++) {
			for ($y = 0; $y < $this->base; $y++) {
				for ($cell_x = 0; $cell_x < $this->base; $cell_x ++) {
					for ($cell_y = 0; $cell_y < $this->base; $cell_y ++) {
						$idbloque = 'B' . (($x * $this->base) + $y);
						$tx = ($cell_x + $x * $this->base);
						$ty = ($cell_y + $y * $this->base);
						$this->tablero[$tx][$ty] = array(
							'id' => '(' . $tx . ',' . $ty . ')', 	// Identificador legible.
							'valor' => '.',							// Valor de la celda (1,2,..). Punto "." para celda en blanco.
							'fija' => false,						// TRUE si el valor es dado por un  Sudoku a solucionar.
							'caja' => $idbloque,					// Identificación legible para la caja a que pertenece.
							'disponibles' => $disponibles,			// Opciones de valor disponibles para esta celda.
						);
						// Cajas de celdas, usado para identificar rapidamente a que caja corresponde una celda.
						$this->cajas[$idbloque][] = array('x' => $tx, 'y' => $ty);
					}
				}
			}
		}
	}

	/**
	 * Retorna cadena con el listado completo de valores contenido en un Sudoku.
	 * Asigna cada fila de celdas a una línea de la cadena texto, de forma que retorna tantas
	 * líneas como filas tenga el Sudoku.
	 *
	 * @return string Cadena texto.
	 */
	private function valores() {

		$data = '';
		$ancho_tablero = $this->anchoTablero();
		for ($x = 0; $x < $ancho_tablero; $x ++) {
			for ($y = 0; $y < $ancho_tablero; $y ++) {
				$data .= $this->tablero[$x][$y]['valor'];
			}
			$data .= "\n";
		}

		return $data;
	}

	/**
	 * Genera un identificador único para cada tablero solucionado.
	 *
	 * @return string Identificador único por tablero.
	 */
	public function checksum() {

		return sha1($this->valores());
	}

	/**
	 * Genera texto HTML a pantalla para visualizar el tablero de Sudoku.
	 */
	public function render() {

		$salida = '';
		if (!$this->publicado) {
			// Primera ves. Incluye listado de estilos a usar para cada celda y título de página.
			$salida = '
<style>
	body { font-family:Consolas; color:#777; }
	h1, b { color:#000; }
	table { border-bottom:2px solid #000;border-right:2px solid #000; }
	td { padding:5px; border-left:2px solid #999; border-top:2px solid #999; width:35px;height:35px;text-align:center;vertical-align:middle; }
	td.x-borde { border-top-color:#000; }
	td.y-borde { border-left-color:#000; }
	td.fija { font-weight:bold; background:#ccc; color:#000; }
</style>
<h1>Sudoku</h1>
';
		}
		else {
			// Separador a la presentación anterior
			echo "<hr>";
		}

		echo '<table border="0" cellspacing="0">';

		// Visualización de cada celda
		for ($x = 0; $x < $this->base; $x++) {
			for ($cell_x = 0; $cell_x < $this->base; $cell_x ++) {
				$salida .= '<tr>';
				for ($y = 0; $y < $this->base; $y++) {
					for ($cell_y = 0; $cell_y < $this->base; $cell_y ++) {
						$cell = $this->tablero[($cell_x + $x * $this->base)][($cell_y + $y * $this->base)];
						$estilo = '';
						if ($cell_x == 0) { $estilo .= 'x-borde '; }
						if ($cell_y == 0) { $estilo .= 'y-borde '; }
						if ($cell['fija']) { $estilo .= 'fija '; }
						$salida .= '<td class="' . trim($estilo) . '">';
						$salida .= $cell['valor'];
						if ($this->debug) {
							// Incluye información adicional para cada celda
							$salida .= $cell['id'] . $cell['caja'];
						}
						$salida .= '</td>';
					}
				}
				$salida .= '</tr>';
			}
		}

		$salida .= '</table>';

		// Visualización de información adicional
		if ($this->ciclos > 0) {
			$salida .= "<hr><p><b>Ciclos:</b> {$this->ciclos} (max.Ciclos: {$this->maxCiclos})</p>";
		}
		if (!$this->publicado) {
			$salida .= "<hr><p><b>CheckSum:</b> " . $this->checksum() . "</p>";
			$salida .= "<hr><p><b>Nivel:</b> {$this->level}</p>";
		}

		$this->publicado = true;

		echo $salida;
	}
}