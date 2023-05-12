# sudoku

Como programador, he tenido que realizar proyectos profesionalmente, algunos con mayores retos que otros. Pero aparte de los retos profesionales, existen retos personales, programas que me nace escribir ya sea porque necesito solucionar una necesidad puntual o solamente por el placer de hacerlo. Uno de esos últimos retos fue el de solucionar un Sudoku. Si ya se, existen muchas aplicaciones allí afuera que lo hacen, pero el reto es hacerlo, no copiarlo. Con esto en mente, lo primero a tener claro es cómo se define un Sudoku. Para esto, voy a apoyarme en la siempre disponible (aunque no siempre fiable) *Wikipedia*:

> Un Sudoku estándar contiene 81 celdas, dispuestas en una trama de 9×9, que está subdividida en nueve cajas. Cada caja está determinada por la intersección de tres filas con tres columnas. Cada celda puede contener un número del uno al nueve y cada número solo puede aparecer una vez en cada fila, cada columna o cada caja. Un sudoku comienza con algunas celdas ya completas, conteniendo números (pistas), y el objetivo es rellenar las celdas restantes. Los sudokus bien planteados tienen una única solución.

[Wikipedia](https://es.wikipedia.org/wiki/Algoritmos_para_la_resoluci%C3%B3n_de_sudokus)

![Un tablero de Sudoku en limpio](https://github.com/jjmejia/sudoku/blob/main/imagenes/sudoku-limpio.png?raw=true)

Solucionar un Sudoku no es algo trivial, aunque así lo parece. Lo primero que intenté fue replicar mi forma de abordar estos pasatiempos, pero entonces me di cuenta que plasmar mi forma de pensar en un “algoritmo coherente” no iba a resultar porque, bueno, los humanos tenemos formas de pensar muy particulares y no siempre se trasladan bien al lenguaje de una máquina (de allí la trascendencia, relevancia e importancia de lo que se ha logrado con ChatGPT). Esto me llevo a que muchas veces abandonara este proyecto. Y a que muchas veces lo intentara de nuevo, hasta que finalmente decidí que lo mejor era comenzar por el principio, con algo “sencillo”, como llenar una cuadricula de Sudoku en limpio. Decidido eso, lo siguiente fue trazar la ruta a seguir, que fue la siguiente:

* [Definir la clase y visualizar el tablero en pantalla (parte 1)](https://github.com/jjmejia/sudoku/tree/main/parte-1).
* Llenar una cuadricula de Sudoku en blanco (parte 2).
* Solucionar un Sudoku existente (parte 3).
* Finalmente, crear un Sudoku para ser impreso (parte 4).

Bueno, con esta ruta comencé con lo básico en estos casos, porque la urgencia en estos proyectos personales suele ser sentarse a programar (usando PHP en mi caso) y comenzar a ver resultados, así no sea la presentación lo más elegante del mundo.

El resultado está contenido en este repositorio.

Información también disponible en [mi Blog de Programador](https://micode-manager.blogspot.com/search/label/Sudoku).