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

		// Llena tablero y disponibles
		for ($x = 0; $x < $this->base; $x++) {
			for ($y = 0; $y < $this->base; $y++) {
				for ($cell_x = 0; $cell_x < $this->base; $cell_x ++) {
					for ($cell_y = 0; $cell_y < $this->base; $cell_y ++) {
						$idbloque = 'B' . (($x * $this->base) + $y);
						$tx = ($cell_x + $x * $this->base);
						$ty = ($cell_y + $y * $this->base);

						// Aleatoriza la cadena base.
						// NOTA: Una prueba de solución mostró que al no aleatorizar, siempre reportaba 531 ciclos al solucionarlo,
						// en tanto que al aleatorizar podian ser 99, 101, 633 u otro, dependiendo de la cadena generada. Esto
						// significa que aunque aleatorizar no garantiza una solución más rápida, si puede resultar en una.
						// Para tableros limpios, aleatorizar para cada celda permite generar tableros mucho mas variados.
						// (Si solamente se aleatoriza una vez, todos los tableros con la misma secuencia serán iguales)
						if ($this->randomBase || $randomBase) {
							$disponibles = str_shuffle($disponibles);
						}

						$this->tablero[$tx][$ty] = array(
							'id' => '(' . $tx . ',' . $ty . ')', 	// Identificador legible.
							'valor' => '.',							// Valor de la celda (1,2,..). Punto "." para celda en blanco.
							'fija' => false,						// TRUE si el valor es dado por un  Sudoku a solucionar.
							'caja' => $idbloque,					// Identificación legible para la caja a que pertenece.
							'disponibles' => $disponibles,			// Opciones de valor disponibles para esta celda.
							'pre' => ''								// Valores permitidos que no cumplen con las reglas
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
	 * Recorre las celdas libres de una fila y les asigna un valor valido, acorde a las reglas de Sudoku.
	 * Cuando no puede encontrar el valor para una celda ($x,$y), se devuelve una celda (usualmente, decrementa $y
	 * pero puede implicar también devolverse a una fila previa y cambiar así el valor de $x). Si ya probó con todos
	 * los valores de esa celda, se regresa una más y tantas como necesite hasta encontrar una con valores que
	 * todavía tenga pendiente de evaluar. En ese punto, restablece los valores que el resto de celdas tenían (por
	 * ello se preserva el historial de las celdas. Otra opción sería reconstruirlo pero de esta forma es más rápido,
	 * aunque puede usar un poco más de memoria). Teniendo el tablero restablecido en un punto conocido, procede
	 * de nuevo a evaluar cada celda de la fila hasta encontrar los valores que correspondan.
	 *
	 * @return bool TRUE si todas las celdas libres pudieron ser llenadas, FALSE en otro caso.
	 */
	public function llenarFilas() {

		// Antes de proceder, inicializa pendientes si existen celdas fijas.
		// En el proceso también fija aquellos valores unicos posibles.
		$this->inicializarDisponibles();

		$ancho_tablero = $this->anchoTablero();

		$x = 0;

		// Recorre cada fila
		while ($x < $ancho_tablero) {
			$y = 0;
			// Recorre cada celda de la fila
			while ($y < $ancho_tablero) {

				if (!$this->llenarCelda($x, $y)) {
					// Si no es la primera columna, intenta nuevas combinaciones
					// en el paso anterior. Devuelve el valor disponible.
					if (!$this->recuperarHistorial($x, $y)) {
						// Nada por hacer
						if ($this->debug) {
							echo "<hr>NO HISTORIAL: $x , $y : {$this->infoerror} <pre>"; print_r($this->historial); echo "<hr>"; print_r($this->tablero);
						}
						$this->infoerror = trim("No pudo solucionar Sudoku, falla encontrada en fila {$x}\n" . $this->infoerror);
						return false;
					}
				}
				else {
					// Siguiente celda
					$y ++;
				}
			}

			$x ++;
		}

		return true;
	}

	/**
	 * Asigna valor a una celda, tomado del primero en su listado de valores disponibles.
	 * Si el valor selecto no cumple, toma el siguiente en el listado de disponibles.
	 * Si el valor es valido, lo retira de las celdas relacionadas según las reglas del Sudoku.
	 *
	 * @param int $x Fila (base 0)
	 * @param int $y Columna (base 0)
	 * @return bool TRUE si encuentra un valor valido, FALSE en otro caso.
	 */
	private function llenarCelda(int $x, int $y) {

		$encontrado = false;

		if (isset($this->tablero[$x][$y])) {
			// Si es FIJA la da como ENCONTRADA para no modificarla.
			$encontrado = ($this->tablero[$x][$y]['fija'] || $this->tablero[$x][$y]['valor'] != '.');
			if (!$encontrado) {
				$disponibles = $this->tablero[$x][$y]['disponibles'];
				$len = strlen($disponibles);
				if ($len > 0) {
					$pos = 0;
					while ($pos < $len) {
						$this->infoerror = '';
						$this->ciclos ++;

						// Valida que no haya superado el máximo de ciclos permitidos (previene bucles infinitos)
						if ($this->ciclos >= $this->maxCiclos) {
							$this->infoerror = 'Sudoku sin solución, número de ciclos máximos alcanzado (' . $this->ciclos . ')';
							return false;
						}

						$valor = substr($this->tablero[$x][$y]['disponibles'], $pos, 1);

						// Valida las reglas del Sudoku al valor asignado
						$encontrado = $this->evalCelda($x, $y, $valor);
						if ($encontrado) {
							$this->infoerror = ''; // Limpia errores previos
							$this->tablero[$x][$y]['valor'] = $valor;
							if ($this->debug) {
								echo "ENCONTRADO EN ($x,$y) $pos : $valor de [{$this->tablero[$x][$y]['disponibles']}]<hr>";
							}
							$this->tablero[$x][$y]['disponibles'] = str_replace($this->tablero[$x][$y]['valor'], '', $this->tablero[$x][$y]['disponibles']);

							// Descarta de los disponibles de las celdas relacionadas.
							$removidos = $this->actualizarDisponibles($x, $y);
							$this->historial[] = array(
								'x' => $x,
								'y' => $y,
								'v' => $this->tablero[$x][$y]['valor'],
								'disponibles' => $this->tablero[$x][$y]['disponibles'],
								'pre' => $this->tablero[$x][$y]['pre'],
								'rem' => $removidos
							);

							// Revisa los posibles valores con solamente un elemento disponible. Si alguno falla
							// en ser un valor valido, cancela esta actualización.
							$encontrado = $this->validarUnicos();

							break;
						}

						// Si llega aquí, el valor actual no cumplió
						if ($this->debug) {
							echo "* BUSCANDO EN ($x,$y) $pos : $valor de [{$this->tablero[$x][$y]['disponibles']}] - {$this->infoerror}<hr>";
						}

						$pos ++;
					}
				}
				else {
					// No tiene más opciones por evaluar
					if ($this->debug) {
						echo "BUSCANDO TERMINA OPCIONES EN ($x,$y)<hr>"; $this->render(); echo "<hr>";
					}
				}
			}
		}

		return $encontrado;
	}

	/**
	 * Evalua si el valor sugerido para una celda es valido.
	 * Esto es, si las celdas de la fila, columna y caja actuales no contienen el mismo valor.
	 *
	 * @param int $x Fila (base 0)
	 * @param int $y Columna (base 0)
	 * @param string $valor Valor sugerido (numérico de base 1, se maneja como string por compatibilidad con otros datos).
	 * @return bool TRUE si encuentra un valor valido, FALSE en otro caso.
	 */
	private function evalCelda(int $x, int $y, string $valor) {

		$ancho_tablero = $this->anchoTablero();
		$encontrado = ($this->infoerror == '');

		// Valida posicion en la fila
		if ($encontrado) {
			for ($cell_y = 0; $cell_y < $ancho_tablero; $cell_y ++) {
				if ($y != $cell_y && $this->tablero[$x][$cell_y]['valor'] == $valor) {
					$encontrado = false;
					$this->infoerror = "FILA: Colisión de valor {$valor} en ({$x},{$y}) con ({$x},{$cell_y})";
					break;
				}
			}
		}
		// Valida posicion en la columna
		if ($encontrado) {
			for ($cell_x = 0; $cell_x < $ancho_tablero; $cell_x ++) {
				if ($x != $cell_x && $this->tablero[$cell_x][$y]['valor'] == $valor) {
					$encontrado = false;
					$this->infoerror = "COLUMNA: Colisión de valor {$valor} en ({$x},{$y}) con ({$cell_x},{$y})";
					break;
				}
			}
		}
		// Valida posicion en el bloque
		if ($encontrado) {
			foreach ($this->cajas[$this->tablero[$x][$y]['caja']] as $info) {
				if ($x != $info['x'] && $y != $info['y'] && $this->tablero[$info['x']][$info['y']]['valor'] == $valor) {
					$encontrado = false;
					$this->infoerror = "BLOQUE: Colisión de valor {$valor} en ({$x},{$y}) con ({$info['x']},{$info['y']})";
					break;
				}
			}
		}

		return $encontrado;
	}

	/**
	 * Recupera información del historial de cambios.
	 * Actualiza las celdas modificadas durante el cambio registrado en cada historial.
	 *
	 * @param int $x Fila (base 0)
	 * @param int $y Columna (base 0)
	 * @return bool TRUE si encuentra un historial valido, FALSE en otro caso.
	 */
	private function recuperarHistorial(int &$x, int &$y) {

		$historial = array_pop($this->historial);
		$hay_historial = is_array($historial);

		while ($hay_historial) {
			if ($this->debug) {
				echo "HISTORIAL: " . count($this->historial); echo "<br><pre style='font-size:10pt'>"; print_r($historial); echo "</pre><hr>";
			}
			// Puede intentar de nuevo
			$y = $historial['y'];
			$x = $historial['x'];
			$this->tablero[$x][$y]['disponibles'] = $historial['disponibles'];
			// Valor actual va a la cola de "pre"
			$this->tablero[$x][$y]['pre'] = $historial['pre'] . $historial['v'];
			$this->tablero[$x][$y]['valor'] = '.';
			// Restablece todos los demas "disponibles" asociados a este historico
			foreach ($historial['rem'] as $rem) {
				$this->tablero[$rem['x']][$rem['y']]['disponibles'] = $rem['disponibles'];
				$this->tablero[$rem['x']][$rem['y']]['pre'] = $rem['pre'];
			}
			// Si el primer elemento de "disponible" es un "!" significa que ya repasó todos los disponibles de esta opción
			if (strlen($this->tablero[$x][$y]['disponibles']) == 0) {
				// Limpia disponibles y recupera valores previamente usados,
				// esto porque no siempre estará esta celda en los históricos siguientes.
				$this->tablero[$x][$y]['disponibles'] = $this->tablero[$x][$y]['pre'];
				$this->tablero[$x][$y]['pre'] = '';
				$historial = array_pop($this->historial);
				$hay_historial = is_array($historial);
			}
			else {
				break;
			}
		}

		// TRUE si encontró un historial valido
		return $hay_historial;
	}

	/**
	 * Remueve del listado de disponibles el valor asignado a la celda actual.
	 *
	 * @param int $x Fila (base 0)
	 * @param int $y Columna (base 0)
	 */
	public function actualizarDisponibles(int $x, int $y) {

		$removidos = array();

		// No procesa si encuentra errores
		if ($this->infoerror == '') {

			$valor = $this->tablero[$x][$y]['valor'];
			$ancho_tablero = $this->anchoTablero();
			// Elimina los disponibles en los demás elementos de la fila
			// (el valor en Y se evalua desde cero porque al llenar fila no lo hace en orden)
			for ($cell_y = 0; $cell_y < $ancho_tablero && $this->infoerror == ''; $cell_y ++) {
				$this->removerDisponible($x, $cell_y, $valor, $removidos);
			}

			// Elimina los disponibles en los demás elementos de la columna
			// BUG: Estaba iniciando en $cell_x = $x + 1 lo que hacia que no liberara hacia atrás.
			// En tableros en blanco funcionan, pero no en tableros con celdas fijas ya que requiere
			// limpiar al asignar el valor de dichas celdas.
			for ($cell_x = 0; $cell_x < $ancho_tablero && $this->infoerror == ''; $cell_x ++) {
				$this->removerDisponible($cell_x, $y, $valor, $removidos);
			}
			// Elimina los disponibles en los demás elementos del bloque
			foreach ($this->cajas[$this->tablero[$x][$y]['caja']] as $info) {
				$this->removerDisponible($info['x'], $info['y'], $valor, $removidos);
			}
		}

		return $removidos;
	}

	/**
	 * Elimina valor selecto del listado de disponibles en las celdas afectadas.
	 * Guarda copia del elemento original (histórico) en caso que requiera deshacer esta selección.
	 * Soporte para $this->actualizarDisponibles().
	 *
	 * @param int $x Fila (base 0)
	 * @param int $y Columna (base 0)
	 * @param string $valor Valor a remover.
	 * @param array $removidos Arreglo donde registra los valores de la celda actual antes de remover el valor indicado.
	 */
	private function removerDisponible(int $x, int $y, string $valor, array &$removidos) {

		$retornar = false;

		if ($this->infoerror == ''
			// Si solamente queda un valor, el disponible debe ser diferente a $valor
			&& $this->tablero[$x][$y]['valor'] == '.'
			&& strpos($this->tablero[$x][$y]['disponibles'], $valor) !== false
			) {
			if ($this->tablero[$x][$y]['disponibles'] !== $valor) {
				$removidos[] = array(
					'x' => $x,
					'y' => $y,
					'disponibles' => $this->tablero[$x][$y]['disponibles'],
					'pre' => $this->tablero[$x][$y]['pre']
					);
				$this->tablero[$x][$y]['disponibles'] = str_replace($valor, '', $this->tablero[$x][$y]['disponibles']);
				$retornar = true;
			}
			else {
				$this->infoerror = "Posible colisión de valores {$valor} en ({$x},{$y}).";
			}
		}

		return $retornar;
	}

	/**
	 * Asigna valores fijos.
	 * Requiere ejecutar primero $this->construirBase().
	 * Recibe texto con la estructura del Sudoku deseado, usando "." para indicar las celdas vacias,
	 * números para las celdas ocupadas (1..9 en un Sudoku de 9x9). Una linea por fila. Las líneas en blanco
	 * y tabuladores o espacios al inicio/final de cada línea son ignoradas. Opcionalmente puede separar las
	 * cajas usando el carácter "|" en las fijas e incluyendo una fila con "-" para separar las cajas en horizontal.
	 * Por ejemplo:
	 * > ..9|7..|3..
	 * > ..8|...|659
	 * > ..1|...|7..
	 * > -----------
	 * > .96|837|1.4
	 * > 1..|...|...
	 * > .2.|...|..3
	 * > -----------
	 * > ...|..4|..1
	 * > 4..|..3|8..
	 * > 2..|9.6|..5
	 */
	public function setFijas(string $texto) {

		// Limpia todo el tablero (por precaución)
		$ancho_tablero = $this->anchoTablero();
		for ($x = 0; $x < $ancho_tablero; $x ++) {
			for ($y = 0; $y < $ancho_tablero; $y ++) {
				$this->tablero[$x][$y]['valor'] = '.';
				$this->tablero[$x][$y]['fija'] = false;
			}
		}

		// Puede usar "-", "+" y "|" para facilitar interpretacion. "." para no fijos.
		$lineas = explode("\n", str_replace(array('-', '|', '+'), '', $texto));
		$x = 0;
		$maxvalor = $this->anchoTablero();
		foreach ($lineas as $linea) {
			$linea = trim($linea);
			if ($linea != '') {
				for ($y=0; $y < strlen($linea); $y++) {
					if (isset($this->tablero[$x][$y])) {
						$valor = substr($linea, $y, 1);
						if (is_numeric($valor) && $valor > 0 && $valor <= $maxvalor) {
							$this->tablero[$x][$y]['valor'] = $valor;
							$this->tablero[$x][$y]['fija'] = true;
							$this->tablero[$x][$y]['disponibles'] = str_replace($valor, '', $this->tablero[$x][$y]['disponibles']);
						}
					}
					else {
						$this->infoerror = "Sudoku valor fijo manual mal formateado en ({$x},{$y}).";
						return false;
					}
				}

				$x ++;
			}
		}

		if ($this->debug) {
			echo "<pre>"; print_r($this->tablero); echo "</pre><hr>";
		}

		return true;
	}

	/**
	 * Recorre tablero y si encuentra que existen celdas fijas, revisa valores disponibles asociados.
	 */
	private function inicializarDisponibles() {

		$hay_fijas = false;
		$ancho_tablero = $this->anchoTablero();

		for ($x = 0; $x < $ancho_tablero; $x ++) {
			for ($y = 0; $y < $ancho_tablero; $y ++) {
				if ($this->tablero[$x][$y]['fija']) {
					$this->actualizarDisponibles($x, $y);
					$hay_fijas = true;
				}
			}
		}
		// Valida unicos solamente si encontró fijas
		if ($hay_fijas) {
			$this->validarUnicos();
		}
	}

	/**
	 * Recorre el tablero buscando celdas en blanco que pueden tomar uno y solamente un valor disponible.
	 *
	 * @return bool TRUE si el valor es aceptable por las reglas del Sudoku, FALSE en otro caso.
	 */
	private function validarUnicos() {

		$ancho_tablero = $this->anchoTablero();

		for ($x = 0; $x < $ancho_tablero; $x ++) {
			for ($y = 0; $y < $ancho_tablero; $y ++) {
				if ($this->tablero[$x][$y]['valor'] == '.'
					&& strlen($this->tablero[$x][$y]['disponibles']) == 1) {
					if (!$this->llenarCelda($x, $y)) {
						// No encaja
						if ($this->debug) {
							echo "* VALORUNICO NOK en ($x,$y): {$this->infoerror}<hr>";
						}
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Revisa el tablero solucionado y garantiza que todos los valores cumplen con las reglas del Sudoku.
	 * Imprime mensaje de texto opcionalmente.
	 *
	 * @param bool $solo_validar TRUE no imprime mensajes de texto.
	 * @return bool TRUE si el tablero quedó bien solucionado, FALSE en otro caso.
	 */
	public function validarSolucion(bool $solo_validar = false) {

		if ($this->infoerror != '') {
			if (!$solo_validar) {
				echo "<p class=\"error\"><b>Aviso:</b><br />" . nl2br($this->infoerror) . "</p>";
			}
			return false;
		}

		$ancho_tablero = $this->anchoTablero();
		for ($x = 0; $x < $ancho_tablero; $x ++) {
			for ($y = 0; $y < $ancho_tablero; $y ++) {
				if ($this->tablero[$x][$y]['valor'] == '.') {
					$this->infoerror = 'Tablero no solucionado completamente';
					if (!$solo_validar) {
						echo "<hr><b>ERROR VALIDACION SUDOKU</b> en ($x,$y) : {$this->infoerror}<hr>";
					}
					return false;
				}
				elseif (!$this->evalCelda($x, $y, $this->tablero[$x][$y]['valor'])) {
					// Realiza repeticiones hacia las filas de arriba
					// ya que la combinación final no genera resultados validos.
					if (!$solo_validar) {
						echo "<hr><b>ERROR VALIDACION SUDOKU</b> en ($x,$y) : {$this->infoerror}<hr>";
					}
					return false;
				}
			}
		}

		if (!$solo_validar) {
			echo "<hr><b>VALIDACION SUDOKU OK</b><hr>";
		}

		return true;
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
	.error { padding:10px; border:1px solid darkred; color:darkred; margin:10px 0; }
	.error b { color:darkred; }
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