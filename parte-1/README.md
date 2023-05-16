# sudoku

## Definir la clase y visualizar el tablero en pantalla (parte 1)

La clase a usar para gestionar el Sudoku permitirá:

* Definir el tamaño base del Sudoku: 2 cajas de ancho, 4 celdas o 3 cajas de ancho, 9 celdas. Este último será la opción por defecto.
* Registrar el tablero de Sudoku en un arreglo bidimensional. Probablemente no la más adecuada forma pero si la representación más aproximada a la cuadricula de Sudoku a la que estamos acostumbrados.

Para cada celda, permite definir y asignarle atributos como:

* Valor de la celda (usaremos el carácter “.” para identificar una celda si valor asignado).
* Si es una celda fija (a usar cuando queramos solucionar un Sudoku existente)
* Posibles valores permitidos en la celda (números del 1 al 9 que cambiarán conforme vayamos llenando el Sudoku).

Finalmente y aunque no es lo usual debido a que va en contra de algunos principios aplicables a las clases (¿has escuchado hablar de principios *SOLID*?), voy a incluir un método para visualizar el tablero de Sudoku en pantalla. Esto ayudará a visualizar el progreso de la solución. Posteriormente puede retirarse para tener una clase no dependiente de la salida a pantalla pero de momento nos servirá así.

Como resultado, tendremos una presentación en pantalla como la siguiente:

![miSUdoku parte 1](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-parte-1.png?raw=true)

(Salida generada desde el script *index.php*)
