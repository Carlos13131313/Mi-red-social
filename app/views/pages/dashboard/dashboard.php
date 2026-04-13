<?php

include_once URL_APP . '/views/custom/header.php';

include_once URL_APP . '/views/custom/navbar.php';

//echo "<pre>";
//var_dump($datos['misNotificaciones']);
//echo "</pre>";

?>

<div class="containerBoard">

    <!--boton para resetear la posicion del reloj-->
    <button class="relojreset">Reset</button>


    <!--Reloj-->
    <div class="reloj" id="reloj">
        <div class="resizer"></div>
    </div>

    <div class="contenedor_de_publicaciones">

        <div class="navigation">
            <!--- menu para hacer publicaciones -->
            <ul class="mb-0">

                <!--icono de "Texto"-->
                <li class="list active" id="texto" onclick="selectOption(this)">
                    <a>
                        <span class="icon">
                            <ion-icon name="book-outline"></ion-icon>
                        </span>
                    </a>

                    <!-- -------------------------------------------------------------------------- -->
                    <!-- contenedor publicacion de texto y botones -->

                    <div class="publicacion" id="texto-pub">

                        <form action="<?php echo URL_PROJECT ?>/publicaciones/publicartextoenboard/<?php echo $datos['usuario']->idusuario ?>" method="POST" enctype="multipart/form-data" class="form-publicar-texto">

                            <textarea name="contenido" id="contenido" class="area-de-texto" name="post" placeholder="¿Qué estás pensando?"></textarea>

                            <!-- Botones para subir y publicar -->
                            <div class="para-cargar-imagen">
                                <img src="<?php echo URL_PROJECT ?>/img/image.png" alt="" class="imagen-public">
                                <span class="btn-Foto">Subir foto</span>

                                <div class="input-file">
                                    <input type="file" name="imagen" id="imagen">
                                </div>

                                <!-- script que habilita el boton Publicar -->
                                <button class="btn-publicar" id="btn-publicar" disabled>Publicar</button>
                            </div>
                        </form>
                    </div>
                </li>

                <!--icono de "Audio"-->
                <li class="list" id="audio" onclick="selectOption(this)">
                    <a>
                        <span class="icon">
                            <ion-icon name="mic-outline"></ion-icon>
                        </span>
                    </a>

                    <!-- -------------------------------------------------------------------------- -->
                    <!-- contenedor publicacion de audio y botones -->
                    <div class="publicacion" id="audio-pub" style="display: none;">

                        <!-- muestra este mensaje cuando se publica el audio -->
                        <div id="mensaje-audio"></div>

                        <form class="form-publicar-audio" action="<?php echo URL_PROJECT ?>/publicaciones/publicarAudioenBoard/<?php echo $datos['usuario']->idusuario ?>" method="POST" enctype="multipart/form-data">
                            <div class="contenido-audio-botones">
                                <div class="botones-controles">
                                    <p>
                                        <button type="button" id="record">Grabar</button>
                                        <button type="button" id="pause" disabled>Pausa</button>
                                        <button type="button" id="stopRecord" disabled>Detener</button>
                                        <button type="button" id="preview" disabled>Reproducir</button>
                                        <button type="button" id="delete" disabled>Eliminar</button>
                                    </p>

                                    <p id="recording-timer" style="font-weight: bold; display: none;">00:00</p>
                                </div>

                                <canvas id="visualizer" width="600" height="100"></canvas>

                                <!-- Mensaje cuando se quiere eliminar la grabacion de Audio -->
                                <div id="alerta" class="alerta" style="display: none;">
                                    <h2>¿Quieres eliminar el audio?</h2>
                                    <div class="botones-eliminar-audio">
                                        <button type="button" id="botonSi">Sí</button>
                                        <button type="button" id="botonNo">No</button>
                                    </div>
                                </div>
                            </div>

                            <div class="audio-upload">
                                <div class="upload-audio-file">
                                    <button type="submit" class="publicar-audio">Publicar</button>
                                </div>
                            </div>

                            <!-- script para los controles de grabacion, visualizacion de audio y publicacion de Audio -->
                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    let mediaRecorder;
                                    let audioChunks = [];
                                    let timerInterval;
                                    let countdown = 30;
                                    let audioContext;
                                    let analyser;
                                    let dataArray;
                                    let animationId;
                                    let canvas, canvasCtx;

                                    const recordButton = document.getElementById('record');
                                    const stopRecordButton = document.getElementById('stopRecord');
                                    const pauseButton = document.getElementById('pause');
                                    const previewButton = document.getElementById('preview');
                                    const deleteButton = document.getElementById('delete');
                                    const alertaDiv = document.getElementById('alerta');
                                    const botonSi = document.getElementById('botonSi');
                                    const botonNo = document.getElementById('botonNo');
                                    const formPublicarAudio = document.querySelector('.form-publicar-audio');
                                    const submitButton = formPublicarAudio.querySelector('.publicar-audio');
                                    const timerDisplay = document.getElementById('recording-timer');
                                    const mensajeDiv = document.getElementById('mensaje-audio');

                                    submitButton.disabled = true;

                                    // Eventos
                                    recordButton.addEventListener('click', startRecording);
                                    stopRecordButton.addEventListener('click', stopRecording);
                                    pauseButton.addEventListener('click', pauseRecording);
                                    previewButton.addEventListener('click', playRecording);
                                    deleteButton.addEventListener('click', showDeleteConfirmation);

                                    formPublicarAudio.addEventListener('submit', function(event) {
                                        event.preventDefault();

                                        if (audioChunks.length === 0) {
                                            console.warn("No hay datos de audio para enviar.");
                                            return;
                                        }

                                        if ((30 - countdown) < 10) {
                                            mensajeDiv.innerHTML = `
                                            <div class="mensaje-error-audio">
                                            ⏱️ El audio debe durar al menos 10 segundos para poder publicarse.</div>
                                            `;
                                            const mensaje = mensajeDiv.querySelector('.mensaje-error-audio');
                                            setTimeout(() => {
                                                mensaje.classList.add('fade-out-audio');
                                                setTimeout(() => {
                                                    mensajeDiv.innerHTML = '';
                                                }, 600);
                                            }, 3500);
                                            return;
                                        }

                                        const audioBlob = new Blob(audioChunks, {
                                            type: 'audio/webm;codecs=opus'
                                        });
                                        const formData = new FormData();
                                        formData.append('audioBlob', audioBlob, 'grabacion.webm');

                                        fetch(formPublicarAudio.action, {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then(response => response.text())
                                            .then(html => {
                                                mensajeDiv.innerHTML = `
                                                <div class="mensaje-exito">
                                                ¡Audio publicado!
                                                </div>
                                                `;
                                                const mensaje = mensajeDiv.querySelector('.mensaje-exito');
                                                setTimeout(() => {
                                                    mensaje.classList.add('fade-out-audio');
                                                    setTimeout(() => {
                                                        mensajeDiv.innerHTML = '';
                                                    }, 400);
                                                }, 4000);
                                                deleteRecording();
                                            })
                                            .catch(error => {
                                                console.error("❌ Error al enviar el audio:", error);
                                                mensajeDiv.innerHTML = `
                                                <div class="mensaje-error-audio">
                                                ❌ Hubo un error al publicar el audio. Intenta nuevamente.</div>
                                                `;
                                                const mensaje = mensajeDiv.querySelector('.mensaje-error-audio');
                                                setTimeout(() => {
                                                    mensaje.classList.add('fade-out-audio');
                                                    setTimeout(() => {
                                                        mensajeDiv.innerHTML = '';
                                                    }, 450);
                                                }, 4500);
                                            });
                                    });

                                    //Comienza la grabacion de audio.
                                    function startRecording(event) {
                                        event.preventDefault();
                                        navigator.mediaDevices.getUserMedia({
                                                audio: true
                                            })
                                            .then(stream => {
                                                mediaRecorder = new MediaRecorder(stream, {
                                                    mimeType: 'audio/webm;codecs=opus'
                                                });
                                                audioChunks = [];
                                                countdown = 30;
                                                updateTimerDisplay();
                                                timerDisplay.style.display = 'block';
                                                startTimer();

                                                mediaRecorder.ondataavailable = (event) => {
                                                    if (event.data.size > 0) {
                                                        audioChunks.push(event.data);
                                                    }
                                                };

                                                mediaRecorder.onstop = () => {
                                                    stopTimer();
                                                    updateButtonStatesAfterRecording();
                                                    updateSubmitButtonState();
                                                };

                                                mediaRecorder.start();

                                                audioContext = new AudioContext();
                                                const source = audioContext.createMediaStreamSource(stream);
                                                analyser = audioContext.createAnalyser();
                                                source.connect(analyser);
                                                analyser.fftSize = 64;

                                                const bufferLength = analyser.frequencyBinCount;
                                                dataArray = new Uint8Array(bufferLength);

                                                // Obtener canvas y contexto de dibujo
                                                canvas = document.getElementById('visualizer');
                                                canvasCtx = canvas.getContext('2d');

                                                // Comienza a dibujar el visualizador
                                                drawVisualizer();


                                                updateButtonStatesDuringRecording();
                                            })
                                            .catch(error => {
                                                console.error('Error al acceder al micrófono:', error);
                                                alert("No se pudo acceder al micrófono");
                                            });
                                    }

                                    // Pausa la grabacion de audio.
                                    function pauseRecording(event) {
                                        event.preventDefault();
                                        if (!mediaRecorder) return;

                                        if (mediaRecorder.state === 'recording') {
                                            mediaRecorder.pause();
                                            pauseButton.textContent = 'Reanudar';
                                            stopTimer();

                                            // En lugar de cancelar la animación y limpiar el canvas, solo desvincula el analyser
                                            analyser = null; // Esto hará que las barras bajen suavemente en drawVisualizer()

                                        } else if (mediaRecorder.state === 'paused') {
                                            mediaRecorder.resume();
                                            pauseButton.textContent = 'Pausa';
                                            startTimer();

                                            // Restaurar el analyser y continuar visualización
                                            if (audioContext && audioContext.state === 'running') {
                                                analyser = audioContext.createAnalyser();
                                                const source = audioContext.createMediaStreamSource(mediaRecorder.stream);
                                                source.connect(analyser);
                                                analyser.fftSize = 256;
                                                dataArray = new Uint8Array(analyser.frequencyBinCount);
                                                drawVisualizer(); // vuelve a iniciar la animación
                                            }
                                        }
                                    }

                                    // Reproduce la grabacion grabada.
                                    function playRecording(event) {
                                        event.preventDefault();

                                        // Si ya se está reproduciendo, detener y limpiar
                                        if (audioPlayer && !audioPlayer.paused) {
                                            audioPlayer.pause();
                                            audioPlayer.currentTime = 0;
                                            audioPlayer = null;
                                            previewButton.textContent = 'Reproducir';

                                            // En lugar de cancelar animación o limpiar canvas, desactivar analyser
                                            analyser = null;

                                            // Cerrar el audioContext con retardo para dejar que finalice la animación
                                            if (audioContext) {
                                                setTimeout(() => {
                                                    if (audioContext) {
                                                        audioContext.close();
                                                        audioContext = null;
                                                    }
                                                }, 500);
                                            }

                                            return;
                                        }

                                        if (audioChunks.length === 0) {
                                            alert("No hay grabación para reproducir.");
                                            return;
                                        }

                                        const audioBlob = new Blob(audioChunks, {
                                            type: 'audio/webm;codecs=opus'
                                        });
                                        const audioURL = URL.createObjectURL(audioBlob);
                                        audioPlayer = new Audio(audioURL);
                                        previewButton.textContent = 'Stop';

                                        // Crear nuevo contexto y visualizador
                                        audioContext = new(window.AudioContext || window.webkitAudioContext)();
                                        analyser = audioContext.createAnalyser();
                                        analyser.fftSize = 256;
                                        const source = audioContext.createMediaElementSource(audioPlayer);
                                        source.connect(analyser);
                                        analyser.connect(audioContext.destination);
                                        dataArray = new Uint8Array(analyser.frequencyBinCount);

                                        drawVisualizer();

                                        const finalizarReproduccion = () => {
                                            previewButton.textContent = 'Reproducir';
                                            audioPlayer = null;

                                            // Suavizar visualización
                                            analyser = null;

                                            if (audioContext) {
                                                setTimeout(() => {
                                                    if (audioContext) {
                                                        audioContext.close();
                                                        audioContext = null;
                                                    }
                                                }, 500);
                                            }
                                        };

                                        audioPlayer.onended = finalizarReproduccion;
                                        audioPlayer.onerror = finalizarReproduccion;

                                        audioPlayer.play().catch((error) => {
                                            console.error("Error al iniciar la reproducción:", error);
                                            finalizarReproduccion();
                                        });
                                    }

                                    // Detiene la grabacion de Audio
                                    function stopRecording(event) {
                                        if (event && event.preventDefault) {
                                            event.preventDefault();
                                        }
                                        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                                            mediaRecorder.stop();
                                        }

                                        // En lugar de cancelar la animación y limpiar el canvas directamente,
                                        // se desvincula el analyser para permitir el descenso suave
                                        analyser = null;

                                        // Cerramos el contexto de audio después de un pequeño retraso
                                        // para permitir que las barras terminen su animación
                                        if (audioContext) {
                                            setTimeout(() => {
                                                if (audioContext) {
                                                    audioContext.close();
                                                    audioContext = null;
                                                }
                                            }, 500); // Puedes ajustar el tiempo si lo deseas
                                        }
                                    }

                                    let audioPlayer = null;

                                    // Elimina la grabacion de audio.
                                    function deleteRecording() {
                                        // Detener reproducción si está activa
                                        if (audioPlayer && !audioPlayer.paused) {
                                            audioPlayer.pause();
                                            audioPlayer.currentTime = 0;
                                            audioPlayer = null;
                                            previewButton.textContent = 'Reproducir';
                                        }

                                        audioChunks = [];
                                        updateButtonStatesAfterDeletion();
                                        updateSubmitButtonState();
                                        timerDisplay.textContent = '30';
                                        timerDisplay.style.display = 'none';
                                        countdown = 30;

                                        // No canceles la animación ni limpies el canvas aquí
                                        // Solo cierra el audioContext y elimina el analyser, así drawVisualizer sabrá que ya no hay audio
                                        if (audioContext) {
                                            audioContext.close();
                                            audioContext = null;
                                            analyser = null; // Esto provoca que drawVisualizer empiece a suavizar barras hacia abajo
                                        }

                                    }

                                    function showDeleteConfirmation(event) {
                                        event.preventDefault();
                                        alertaDiv.style.display = 'block';
                                    }

                                    botonSi.addEventListener('click', (event) => {
                                        event.preventDefault();
                                        alertaDiv.style.display = 'none';
                                        deleteRecording();
                                    });

                                    botonNo.addEventListener('click', (event) => {
                                        event.preventDefault();
                                        alertaDiv.style.display = 'none';
                                    });

                                    function updateButtonStatesDuringRecording() {
                                        recordButton.disabled = true;
                                        stopRecordButton.disabled = true;
                                        pauseButton.disabled = false;
                                        previewButton.disabled = true;
                                        deleteButton.disabled = true;
                                    }

                                    function updateButtonStatesAfterRecording() {
                                        recordButton.disabled = true;
                                        stopRecordButton.disabled = true;
                                        pauseButton.disabled = true;
                                        previewButton.disabled = false;
                                        deleteButton.disabled = false;
                                    }

                                    function updateButtonStatesAfterDeletion() {
                                        recordButton.disabled = false;
                                        stopRecordButton.disabled = true;
                                        pauseButton.disabled = true;
                                        previewButton.disabled = true;
                                        deleteButton.disabled = true;
                                    }

                                    // Con este boton se publica el audio grabado.
                                    function updateSubmitButtonState() {
                                        const elapsed = 30 - countdown;
                                        const restante = Math.max(10 - elapsed, 0);

                                        if (elapsed < 10) {
                                            submitButton.disabled = true;
                                            submitButton.textContent = `${restante} s`;
                                        } else {
                                            submitButton.textContent = 'Publicar';
                                            submitButton.disabled = audioChunks.length === 0;
                                        }
                                    }

                                    // Comienza el conteo regresivo.
                                    function startTimer() {
                                        timerInterval = setInterval(() => {
                                            countdown--;
                                            if (countdown < 0) countdown = 0;

                                            updateTimerDisplay();
                                            updateSubmitButtonState();

                                            if ((30 - countdown) >= 10) {
                                                stopRecordButton.disabled = false;
                                            }

                                            if (countdown <= 0) {
                                                stopRecording(new Event('autoStop'));
                                            }

                                        }, 1000);
                                    }

                                    // Detiene el conteo regresivo.
                                    function stopTimer() {
                                        clearInterval(timerInterval);
                                    }

                                    // Resete el conteo regresivo.
                                    function updateTimerDisplay() {
                                        timerDisplay.textContent = String(countdown).padStart(2, '0');
                                    }

                                    // Crear un array global para almacenar las alturas anteriores
                                    let previousHeights = [];
                                    let isIdle = true

                                    canvas = document.getElementById('visualizer');
                                    canvasCtx = canvas.getContext('2d');

                                    // Visualizador de audio (Animacion en barras verticales).
                                    function drawVisualizer() {
                                        animationId = requestAnimationFrame(drawVisualizer);

                                        if (analyser) {
                                            analyser.getByteFrequencyData(dataArray);
                                        } else {
                                            // Modo reposo: oscilación suave de barras blancas
                                            if (!dataArray) {
                                                dataArray = new Uint8Array(64);
                                            }
                                            for (let i = 0; i < dataArray.length; i++) {
                                                dataArray[i] = 15 + Math.sin(Date.now() / 150 + i) * 10;
                                            }
                                        }

                                        canvasCtx.clearRect(0, 0, canvas.width, canvas.height);

                                        const centerX = canvas.width / 2;
                                        const centerY = canvas.height / 2;
                                        const numBars = 14; // Total de barras (par o impar)
                                        const maxBarHeight = 30; // Altura máxima
                                        const minBarHeight = 8; // Altura mínima
                                        const barSpacing = 23; // Espacio entre barras
                                        const barWidth = 13; // Grosor de cada barra
                                        const lowFreqSkip = 10; // Omitir las frecuencias más bajas
                                        const baseNoise = 5; // Filtrado de ruido de base
                                        const riseSmoothing = 0.30; // más bajo (ej. 0.1) si quieres que suban aún más lento.
                                        const fallSmoothing = 0.15; // más alto (ej. 0.15) si quieres que bajen más rápido.
                                        const radius = 4; // Radio para redondear solo en extremos verticales

                                        if (previousHeights.length !== numBars) {
                                            previousHeights = new Array(numBars).fill(minBarHeight);
                                        }

                                        let allBarsAtMinHeight = true;

                                        for (let i = 0; i < numBars; i++) {
                                            const index = lowFreqSkip + Math.floor((i / numBars) * (dataArray.length - lowFreqSkip));
                                            let value = Math.max(0, dataArray[index] - baseNoise);
                                            let targetHeight = Math.max(minBarHeight, Math.min(value / 2, maxBarHeight));

                                            if (targetHeight > previousHeights[i]) {
                                                previousHeights[i] += (targetHeight - previousHeights[i]) * riseSmoothing;
                                            } else {
                                                previousHeights[i] -= (previousHeights[i] - targetHeight) * fallSmoothing;
                                            }

                                            const barHeight = previousHeights[i];
                                            if (barHeight > minBarHeight + 0.5) {
                                                allBarsAtMinHeight = false;
                                            }

                                            const offsetIndex = Math.floor(i / 2);
                                            const direction = (i % 2 === 0) ? -1 : 1;
                                            const x = centerX + direction * offsetIndex * (barWidth + barSpacing);

                                            const isCenterBar = Math.abs(x + barWidth / 2 - centerX) < barWidth / 2;
                                            if (isCenterBar) continue;

                                            canvasCtx.fillStyle = isIdle ? '#ffffff' : '#ffffff';

                                            drawVerticalRoundedBar(x, centerY - barHeight, barWidth, barHeight, radius, 'top');
                                            drawVerticalRoundedBar(x, centerY, barWidth, barHeight, radius, 'bottom');
                                        }

                                        // Si todas las barras están al mínimo, detener animación
                                        if (allBarsAtMinHeight && !analyser) {
                                            cancelAnimationFrame(animationId);
                                            animationId = null;
                                        }
                                    }                                   

                                    // Inicializa dataArray y comienza animación de reposo
                                    dataArray = new Uint8Array(64);
                                    drawVisualizer();


                                    // Redondea las puntas superiores e inferiores.
                                    function drawVerticalRoundedBar(x, y, width, height, radius, position) {
                                        canvasCtx.beginPath();
                                        if (position === 'top') {
                                            canvasCtx.moveTo(x, y + radius);
                                            canvasCtx.arcTo(x, y, x + width, y, radius);
                                            canvasCtx.arcTo(x + width, y, x + width, y + radius, radius);
                                            canvasCtx.lineTo(x + width, y + height);
                                            canvasCtx.lineTo(x, y + height);
                                        } else if (position === 'bottom') {
                                            canvasCtx.moveTo(x, y);
                                            canvasCtx.lineTo(x + width, y);
                                            canvasCtx.lineTo(x + width, y + height - radius);
                                            canvasCtx.arcTo(x + width, y + height, x, y + height, radius);
                                            canvasCtx.arcTo(x, y + height, x, y + height - radius, radius);
                                            canvasCtx.lineTo(x, y);
                                        }
                                        canvasCtx.closePath();
                                        canvasCtx.fill();
                                    }
                                });
                            </script>

                        </form>

                    </div>
                </li>

                <!--icono de "Video"-->
                <li class="list" id="video" onclick="selectOption(this)">
                    <a>
                        <span class="icon">
                            <ion-icon name="videocam-outline"></ion-icon>
                        </span>
                    </a>

                    <!-- -------------------------------------------------------------------------- -->
                    <!-- contenedor publicacion de video y botones -->
                    <div class="publicacion" id="video-pub" style="display: none;">

                        <input type="text" placeholder="Video">
                        <button onclick="submitForm('video')">Enviar</button>

                    </div>
                </li>
                
                <div class="indicator"></div>

                <div class="barra-division"></div>

                <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
                <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

            </ul>
        </div>
    </div>
</div>











<!-- Reloj------------------------------------------------------------------------------------------------- -->
<script>
    function actualizarReloj() {
        const ahora = new Date();

        let horas = ahora.getHours();
        const minutos = ahora.getMinutes().toString().padStart(2, '0');
        const periodo = horas >= 12 ? 'PM' : 'AM';

        // Convertir a formato de 12 horas
        horas = (horas % 12) || 12;

        const horaActual = `${horas}:${minutos} ${periodo}`;

        document.getElementById('reloj').innerText = `${horaActual}`;

        // Cambiar el color del texto según la posición del reloj
        cambiarColorTexto();
    }

    function cambiarColorTexto() {
        const reloj = document.getElementById('reloj');
        const ventanaAncho = window.innerWidth;
        const ventanaAlto = window.innerHeight;
        const posicionX = parseFloat(reloj.style.left) || 0;
        const posicionY = parseFloat(reloj.style.top) || 0;

        // Calcular porcentajes de posición
        const porcentajeX = (posicionX / ventanaAncho) * 100;
        const porcentajeY = (posicionY / ventanaAlto) * 100;

        // Establecer el color del texto según la posición
        reloj.style.color = `rgb(${porcentajeX}%, ${porcentajeY}%, ${(porcentajeX + porcentajeY) / 2}%)`;
    }

    const reloj = document.getElementById('reloj');

    // Recuperar las coordenadas del localStorage
    let storedLeft = localStorage.getItem('relojLeft');
    let storedTop = localStorage.getItem('relojTop');

    // Establecer las coordenadas almacenadas o las predeterminadas
    if (storedLeft !== null && storedTop !== null) {
        reloj.style.left = storedLeft;
        reloj.style.top = storedTop;
    }

    let isDragging = false;
    let offset = {
        x: 0,
        y: 0
    };

    // Agregar un evento al mouse para seguir la posición
    reloj.addEventListener('mousedown', (e) => {
        isDragging = true;
        offset = {
            x: e.clientX - reloj.getBoundingClientRect().left,
            y: e.clientY - reloj.getBoundingClientRect().top
        };
    });

    // Agregar eventos para dejar de arrastrar
    document.addEventListener('mouseup', () => {
        isDragging = false;

        // Almacenar las nuevas coordenadas en el localStorage
        localStorage.setItem('relojLeft', reloj.style.left);
        localStorage.setItem('relojTop', reloj.style.top);

        // Cambiar el color del texto después de soltar
        cambiarColorTexto();
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            reloj.style.left = e.clientX - offset.x + 'px';
            reloj.style.top = e.clientY - offset.y + 'px';
        }
    });

    function resetearPosicionReloj() {
        // Cambia estas coordenadas según la posición deseada
        const posicionPredeterminada = {
            left: '20px',
            top: '100px'
        };

        // Establecer las coordenadas del reloj
        reloj.style.left = posicionPredeterminada.left;
        reloj.style.top = posicionPredeterminada.top;

        // Almacenar las nuevas coordenadas en el localStorage
        localStorage.setItem('relojLeft', reloj.style.left);
        localStorage.setItem('relojTop', reloj.style.top);

        // Cambiar el color del texto después de restablecer
        cambiarColorTexto();
    }

    // Agregar un evento al botón de reset
    const botonReset = document.querySelector('.relojreset');
    botonReset.addEventListener('click', resetearPosicionReloj);


    // Actualizar el reloj cada segundo
    setInterval(actualizarReloj, 1000);
</script>

<!-- Script que habilita el boton Publicar (lo habilita si hay texto escrito o una imagen cargada) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('contenido');
        const fileInput = document.getElementById('imagen');
        const submitButton = document.getElementById('btn-publicar');

        function checkForm() {
            if (textarea.value.trim() !== '' || fileInput.files.length > 0) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        textarea.addEventListener('input', checkForm);
        fileInput.addEventListener('change', checkForm);
    });
</script>

<!-- Script que permite accionar el input del boton de subir foto ----------------------------------------- -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btnSubirFoto = document.querySelector('.btn-Foto');
        var inputImagen = document.getElementById('imagen');

        btnSubirFoto.addEventListener('click', function() {
            inputImagen.click();
        });
    });
</script>

<!-- Se encarga de el "indicator"-------------------------------------------------------------------------- -->
<script>
    const list = document.querySelectorAll('.list');

    function activeLink() {
        list.forEach((item) =>
            item.classList.remove('active'));
        this.classList.add('active');
    }
    list.forEach((item) =>
        item.addEventListener('click', activeLink));
</script>

<!-- se encarga del contenido interior de cada opcion texto/audio/video------------------------------------ -->
<script>
    function selectOption(selectedLi) {
        // Oculta todos los textboxes y muestra el correspondiente al elemento seleccionado
        document.querySelectorAll('.publicacion').forEach(publicacion => {
            publicacion.style.display = 'none';
        });
        const selectedpublicacion = selectedLi.querySelector('.publicacion');
        if (selectedpublicacion) {
            selectedpublicacion.style.display = 'block';
        }
    }

    function submitForm(option) {
        // Aquí puedes agregar la lógica para enviar el formulario según la opción
        console.log('Enviando formulario para: ' + option);
    }
</script>

<!--se encarga del menu (iconos de publicaciones)------------------------------------------------------------- -->
<script>
    function toggleMenu() {
        const dropdownMenu = document.getElementById('dropdownMenu');
        dropdownMenu.style.display = dropdownMenu.style.display === 'none' ? 'block' : 'none';
    }
</script>

<!-- Visualizador de audio------------------------------------------------------------------------------------ -->


<!-- script para los controles de grabacion de audio---------------------------------------------------- -->

<?php

include_once URL_APP . '/views/custom/footer.php';

?>