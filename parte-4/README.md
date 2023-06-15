# sudoku

## Crear un Sudoku para ser impreso (parte 4)

Hay reglas a considerar para la creación correcta de un Sudoku y en este último artículo, relacionado con la creación de Sudokus usando PHP. Para la creación de un tablero de Sudoku no basta con rellenar una cuadrícula en blanco y que fue cubierta en un artículo anterior. De acuerdo con la información en [Wikipedia](https://en.wikipedia.org/wiki/Sudoku), existen al menos un par de reglas que deben tenerse en cuenta:

* El menor número posible de celdas fijas para un Sudoku de 9x9 es 17 (aprox. 21% del total de 81 celdas en un Sudoku de 9x9).
* Cada Sudoku tiene una única posible solución. Ojo con esta regla porque se debe garantizar esa única posible solución al establecer qué celdas fijas mantener.

Respecto al nivel de dificultad también hay consideraciones a tener presente, al menos de acuerdo con las definiciones encontradas en [www.sudokulovers.com](https://www.sudokulovers.com/what-makes-Sudoku-easy-medium-or-hard) y de las que podemos concluir que:

* Sudokus fáciles y medios pueden ser resueltos usando deducciones lógicas directas, es decir, no tienes que esforzarte demasiado, solamente estar muy atento.
* Sudokus fáciles tienen al menos tres celdas fijas por fila, columna, caja y al menos una por cada número, lo que en promedio (para Sudokus de 9x9) deja al menos 27 celdas por tablero (usualmente un poco más).
* Sudokus difíciles tienen menos de 27 celdas fijas pero no menos de 17, muy seguramente con cajas, filas, columnas o números completos sin valores.
* Para Sudokus difíciles probablemente tendrás que suponer valores y si te equivocas, pues a empezar de nuevo.

Antes de continuar, tener presente que esta solución es mi propuesta respecto a cómo obtener tableros con diferentes niveles de dificultad y que no es ni mucho menos absoluta o definitiva. Pueden existir (y seguro existen) otras formas para determinar la complejidad de los tableros a proponer, pero para efectos de los alcances y limitaciones de este desarrollo, esta será la guía a seguir.

Así las cosas, el script propuesto permite generar tableros con diferentes niveles de solución para una misma cuadricula, tal como los mostrados a continuación:

![Un tablero de Sudoku de nivel difícil](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-dificil.png?raw=true)
![Un tablero de Sudoku de nivel medio](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-medio.png?raw=true)
![Un tablero de Sudoku de nivel fácil](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-facil.png?raw=true)

Información también disponible en [mi Blog de Programador](https://micode-manager.blogspot.com/search/label/Sudoku).