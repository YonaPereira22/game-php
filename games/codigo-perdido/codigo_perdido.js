// Función para mostrar solo la sección activa
function mostrarSeccion(id) {
  document.querySelectorAll('.pantalla').forEach(p => p.classList.remove('activa'));
  document.getElementById(id).classList.add('activa');
}

// Mostrar la introducción narrativa
function mostrarIntroduccion() {
  mostrarSeccion('introduccion');
}

// Empezar las misiones, cargando las misiones dinámicamente
function empezarMisiones() {
  mostrarSeccion('juego');
  cargarMisiones();
}

// Definición de misiones (tal cual)
const misiones = [
  {
    imagen: "inicio.jpg",
    narrativa: "Oscuridad total. Chispas sueltas saltan de una placa rota. Una figura metálica tirada, con cables sueltos y su pantalla principal apagada. De pronto... un destello.",
    desafio: null
  },
  {
    imagen: "nobo-1-despertar.png",
    narrativa: "El sistema comienza a arrancar, fragmentado... sin memoria, sin nombre, sin propósito...",
    desafio: {
      tipo: "opciones",
      pregunta: '¿Qué hace la línea print("Hola mundo") en Python?',
      opciones: [
        { texto: "a) Guardar un archivo", correcta: false },
        { texto: "b) Mostrar un mensaje en pantalla", correcta: true },
        { texto: "c) Crear un usuario", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-2-secuencia.png",
    narrativa: "Un chispazo de memoria. Recuerda cómo construir objetos simples...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Cuál es el símbolo para comentarios en Python?",
      opciones: [
        { texto: "a) //", correcta: false },
        { texto: "b) /-", correcta: false },
        { texto: "c) #", correcta: true }
      ]
    }
  },
  {
    imagen: "nobo-3-python.png",
    narrativa: "Una palabra aparece en su mente, pero está desordenada. Sabe que si logra reorganizarla, recordará algo vital.",
    desafio: {
      tipo: "ordenarLetras",
      letras: ["T", "N", "Y", "H", "P", "O"],
      respuesta: "PYTHON"
    }
  },
  {
    imagen: "nobo-4-union.png",
    narrativa: "Su base lógica comienza a reconstruirse...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Qué es una variable?",
      opciones: [
        { texto: "a) Un bucle infinito", correcta: false },
        { texto: "b) Una palabra reservada", correcta: false },
        { texto: "c) Un espacio donde se guarda un valor", correcta: true }
      ]
    }
  },
  {
    imagen: "nobo-5-error.png",
    narrativa: "Una línea de código defectuosa...",
    desafio: {
      tipo: "opciones",
      pregunta: '¿Cuál es el error en este código?\nnombre = input("Cómo te llamas?)\nprint("Hola", nombre)',
      opciones: [
        { texto: "a) Falta el paréntesis final en print", correcta: false },
        { texto: "b) Faltan las comillas al cerrar 'Cómo te llamas?'", correcta: true },
        { texto: "c) El input debe estar fuera del print", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-6-funciones.png",
    narrativa: "Recuerda algo importante: las funciones...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Qué es una función en programación?",
      opciones: [
        { texto: "a) Un bloque de código reutilizable", correcta: true },
        { texto: "b) Un tipo de variable", correcta: false },
        { texto: "c) Un error en el programa", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-7-bucle.png",
    narrativa: "Una nueva chispa de conocimiento: recordaba los bucles...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Cuál es la estructura básica de un bucle 'for' en Python?",
      opciones: [
        { texto: "a) for i in range()", correcta: true },
        { texto: "b) for i from 0 to 10:", correcta: false },
        { texto: "c) for (i = 0; i < 10; i++) { }", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-8-matrices.png",
    narrativa: "Un fragmento de memoria aparece: algo sobre arrays...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Cómo accedes al primer elemento de una lista en Python?",
      opciones: [
        { texto: "a) list[1]", correcta: false },
        { texto: "b) list[0]", correcta: true },
        { texto: "c) list.first()", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-9-depuracion.png",
    narrativa: "Vuelve a recordar esos momentos de frustración...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Qué comando se usa para depurar el código en Python?",
      opciones: [
        { texto: "a) debug()", correcta: false },
        { texto: "b) print()", correcta: true },
        { texto: "c) trace()", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-10-clase.png",
    narrativa: "Finalmente, recuerda cómo estructurar su código en clases...",
    desafio: {
      tipo: "opciones",
      pregunta: "¿Cómo defines una clase en Python?",
      opciones: [
        { texto: "a) class NombreDeClase:", correcta: true },
        { texto: "b) def NombreDeClase:", correcta: false },
        { texto: "c) function NombreDeClase()", correcta: false }
      ]
    }
  },
  {
    imagen: "nobo-11-final.png",
    narrativa: "Una palabra clave. El código de desbloqueo...",
    desafio: {
      tipo: "input",
      pregunta: "Introduce la palabra clave secreta basada en todo lo que has aprendido:",
      respuesta: "identidad"
    }
  },
  {
    imagen: "nobo-12-salida.png",
    narrativa: `La compuerta se abre. Luces brillan. IDENTIDAD.
¡Has completado todas las misiones!
Cada fragmento, cada desafío, era una pista sobre su verdadero ser.
Al reunir las letras, reconstruyó más que su conocimiento...
Reconstruyó su IDENTIDAD.
Ya no es solo un sistema. Tiene propósito, memoria... y un nombre: Nobo.
Gracias por acompañarlo en este viaje. Sin tu ayuda, Nobo nunca habría recordado quién era.
Fin del sistema de recuperación...`,
    desafio: null
  }
];

// Función para cargar las misiones en el HTML
function cargarMisiones() {
  const contenedor = document.getElementById('juego');
  // Limpiar contenido anterior
  contenedor.innerHTML = `<h2>Misiones activadas</h2>`;

  // Agregar cada misión al contenedor
  misiones.forEach((mision, index) => {
    const seccionMision = document.createElement('section');
    seccionMision.innerHTML = `
      <h3>${mision.titulo}</h3>
      <p>${mision.descripcion}</p>
      <p><strong>Letra secreta:</strong> ${mision.letra}</p>
      <hr/>
    `;
    contenedor.appendChild(seccionMision);
  });
}