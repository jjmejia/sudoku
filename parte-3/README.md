# sudoku

## Solucionar un Sudoku existente (parte 3)

El proceso de llenar una cuadrícula de Sudoku en blanco fue cubierto en la entrada anterior. A partir de este punto, nos queda definir el cómo registrar los valores existentes en un Sudoku que deseemos resolver, esto es, indicarle a nuestro código los valores de aquellas celdas iniciales o fijas. Para esto, se propone que dichos valores se ingresen mediante un bloque de texto donde cada línea corresponda a una fila, cada posición en la línea a una columna, cada celda vacía se identifique con un punto (“.”) y los valores fijos por números entre 1 y 9 (para un Sudoku de 9x9). Por ejemplo, usando como base el siguiente Sudoku:

![Un tablero de Sudoku por resolver](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-limpio.png?raw=true)

Su representación en texto sería la siguiente:

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

Para efectos de facilitar su visualización para los ojos humanos como los míos y los tuyos, me he tomado la libertad de adicionar caracteres para separar las filas y cajas, pero estos serán ignorados por el script al interpretarlo. Tanto es así, que sería igualmente válido ingresar un texto como:

    ..97..3..
    ..8...659
    .1....7..
    .968371.4
    1........
    .2......3
    .....4..1
    4....38..
    2..9.6..5

Sin embargo, esta última versión no se ve muy amigable, ¿verdad?

De cualquiera de las dos maneras mostradas o cualquier combinación de las mismas, el código deberá interpretar correctamente la carga y asignar los valores fijos deseados. Una vez inicializado el Sudoku con estos valores fijos y antes de comenzar al proceso de llenado de filas, es prudente (de acuerdo a la lógica planteada en la implementación realizada y descrita en la entrega anterior) revisar los valores disponibles para las celdas a las que esos valores fijos pueden afectar, esto es, a aquellas que están en la misma fila, columna y caja.

Adicionalmente incluí un método de chequeo que revise la cuadrícula final y garantice que la solución presentada satisface todas las reglas del Sudoku, para de esta forma evitarme la tarea de hacerlo manualmente (esas son las cosas que hacemos los programadores, escribir código para ejecutar tareas que no queremos hacer, *jeje*).

Información también disponible en [mi Blog de Programador](https://micode-manager.blogspot.com/search/label/Sudoku).