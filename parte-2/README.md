# sudoku

## Llenar una cuadricula de Sudoku en blanco (parte 2)

![Un tablero de Sudoku completamente lleno](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-nuevo-full.png?raw=true)

¿Por dónde comenzar? Bueno, luego de intentar plasmar mi proceso mental de solución de forma infructuosa, pasé a consultar en Internet cuáles son los procedimientos sugeridos para este tipo de escenarios. De nuevo, Wikipedia vino al rescate con la siguiente sugerencia:

> Algunos jugadores han desarrollado programas de computador que resuelven Sudokus usando algoritmos de backtracking, el cuál es un tipo de búsqueda por fuerza bruta.
> (Segmento traducido de [Wikipedia](https://en.wikipedia.org/wiki/Sudoku_solving_algorithms#Techniques))

Esto básicamente se traduce en un procedimiento como el descrito a continuación:

* Se toma una primera celda y se le asigna un posible valor (entre 1 y 9 para Sudokus de 9x9).
* Se valida que este valor cumpla con las reglas del Sudoku, esto es: “Cada celda puede contener un número del uno al nueve y cada número solo puede aparecer una vez en cada fila, cada columna o cada caja”.
* Si el número no es valido, se toma uno diferente y se repite el proceso hasta encontrar uno que cumpla.
* Si ninguno de los números cumple, se debe regresar una celda, cambiar el valor asignado y repetir el proceso a partir de allí. Si en esa celda ya se probaron todos los posibles valores, se regresa una más atrás y así hasta encontrar los valores validos para todas las celdas.
* Si ningún valor satisface la primer celda (lo que por supuesto no debiera ocurrir en un tablero en blanco), entonces de determina que el Sudoku no tiene solución, es decir, se tiene un tablero mal definido.

La parte realmente interesante es el **cómo** controlar el proceso de llenado cuando se encuentra una celda donde ningún valor satisface las reglas del Sudoku. Para la implementación propuesta, la lógica deberá llevar cuidadosa cuenta de:

* Los valores disponibles para cada celda a fin de reducir los posibles errores de asignación.
* Información histórica de los valores disponibles en las celdas que son modificadas, a fin de restablecerlos cuando debe reversar un cambio.

Vale anotar que una alternativa al uso de históricos, es la de reconstruir cada vez el listado de “disponibles”, pero esto aumentaría considerablemente los ciclos de computo. En este caso, habría que validar qué es más rentable, si consumir ciclos o consumir memoria. Para esta solución, considero (sin ningún soporte estadístico, más como intuición basada en la experiencia) que es más rápida la opción de llevar cuenta de los historiales de cambio.

## Un comentario antes de terminar

Si **siempre** se usa una secuencia de validaciones ordenada, “123…”, el total de ciclos para un Sudoku de 9x9 **será** siempre el mismo (en esta implementación, corresponde a un valor de 527 iteraciones). Esto significaría que todos los tableros serían llenados con la misma secuencia en cada fila, comenzando con la primera siempre en “123…”. Para prevenir esto, se aleatorizan el orden de las opciones para cada celda, no basta solamente con la primera (esto limitaría las combinaciones de Sudokus al orden de los números en esa primera fila). Con este ajuste, se pueden generar tableros diferentes cada vez. Es de anotar también que los ciclos encontrados en este escenario, pueden variar entre 81 y un número indeterminado de posibilidades, ya que es realmente una cosa que queda al azar.

![Un tablero de Sudoku con la secuencia fija en "123..."](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-nuevo-full.png?raw=true)

Información también disponible en [mi Blog de Programador](https://micode-manager.blogspot.com/search/label/Sudoku).