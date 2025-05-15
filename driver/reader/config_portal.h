#ifndef CONFIG_PORTAL_H
#define CONFIG_PORTAL_H

const char part1[] PROGMEM = R"raw(
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Red Wi-Fi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 100vw;
            min-height: 100vh;
            overflow: hidden;
        }

        .config-container {
            overflow: hidden;
            display: grid;
            grid-template-rows: auto 1fr;
            height: 100vh;
            padding: 1.5rem;
            width: 100%;
            max-width: 450px;
            transition: all 0.3s ease;
        }

        .config-header {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            overflow: visible;
            text-align: center;
            margin-bottom: 2rem;
        }

        .config-header h1 {
            font-size: 1.5rem;
            color: #333;
        }

        .config-header span {
            font-style: italic;
            color: #3D3D3D;
            font-size: 14px;
        }

        .wifi-list {
            padding-inline: 0.5rem;
            overflow-y: auto;
        }

        .wifi-network {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f7f7f7;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .wifi-network:hover {
            background: #f0f0f0;
            border-color: #333;
        }

        .wifi-name {
            font-weight: 500;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .svg-icon {
            width: 24px;
            height: 24px;
            fill: #333;
            transition: transform 0.3s ease;
        }

        .wifi-password {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: none;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10;
        }

        /* Animación para deslizar la wifi-list hacia la izquierda */
        @keyframes slideLeft {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(-100%);
            }
        }

        .slide-left {
            animation: slideLeft 0.5s forwards;
        }

        .wifi-network {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f7f7f7;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .wifi-network:hover {
            background: #f0f0f0;
            border-color: #333;
        }

        .wifi-name {
            font-weight: 500;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .svg-icon {
            width: 24px;
            height: 24px;
            fill: #333;
            transition: transform 0.3s ease;
        }

        /* Modal Background con Fade */
        .modal-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-background.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            position: relative;
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(+50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes descendOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(100%);
            }
        }

        .modal-background.hide .modal-content {
            animation: descendOut 0.5s forwards;
        }

        .wifi-password label {
            margin-bottom: 1rem;
        }

        .wifi-password input {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            width: 100%;
            box-sizing: border-box;
        }

        .wifi-password input:focus {
            border-color: #333;
            outline: none;
        }

        .input-with-icon {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-with-icon input {
            padding-right: 40px;
        }

        .input-with-icon .icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            pointer-events: none;
            transition: color 0.3s;
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid #ccc;
            border-top: 2px solid #3D3D3D;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: translateY(-50%) rotate(0deg);
            }

            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }

        .connect-button {
            margin-top: 20px;
            padding: 12px;
            background: #3D3D3D;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .connect-button:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .valid input {
            border-color: #16C47F !important;
        }

        .invalid input {
            border-color: #f00 !important;
        }

        .valid .icon {
            color: #16C47F !important;
        }

        .invalid .icon {
            color: #f00 !important;
        }

        .result-modal {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #333;
            opacity: 0;
            visibility: hidden;
        }

        .result-modal h3 {
            margin-top: 1rem;
        }

        .result-modal.show {
            transition: all 0.5s ease;
            opacity: 1;
            visibility: visible;
        }

        #ip {
            color: gray;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="config-container" id="app">
        <div class="config-header">
            <div>
                <h1>Conecta tu dispositivo</h1>
                <p>Configura el acceso Wi-Fi para el lector NFC</p>
            </div>
)raw";

const char part2[] PROGMEM = R"raw(
</div>

        <div class="wifi-list" id="wifiList">
            <!-- Las redes se generarán dinámicamente con JavaScript -->
        </div>

        <!-- Modal de contraseña (ahora usa modal-background) -->
        <div class="modal-background wifi-password" id="passwordForm">
            <div class="modal-content" id="passwordModalContent">
                <label for="password">Contraseña de la red:</label>
                <div class="input-with-icon" id="inputContainer">
                    <input type="password" id="password" placeholder="Ingresa la contraseña">
                    <span class="icon" id="statusIcon"></span>
                </div>
                <button class="connect-button" onclick="connect()">Conectar</button>
            </div>
        </div>
    </div>

    <div class="result-modal " id="resultModal">
        <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="#16C47F"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
            <path d="M9 12l2 2l4 -4" />
        </svg>
        <h3>Configuración Exitosa</h3>
        <p>La red se configuró correctamente.</p>
        <br>
        <span id="ip"></span>
    </div>

    <script>
        const wifiNetworks = [
)raw";

const char part3[] PROGMEM = R"raw(
];

        // Función para determinar el nivel de señal (0-3) según el RSSI
        function getSignalLevel(rssi) {
            if (rssi == 1) return 4;        // No se encontraron redes
            else if (rssi >= -50) return 3; // Excelente (-30 a -50 dBm)
            else if (rssi >= -65) return 2; // Buena (-50 a -65 dBm)
            else if (rssi >= -75) return 1; // Aceptable (-65 a -75 dBm)   
            else return 0;                  // Mala (< -75 dBm)
        }

        const svgIcons = {
            0: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /></svg>`,
            1: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /></svg>`,
            2: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a8 8 0 0 1 11.314 0" /></svg>`,
            3: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a8 8 0 0 1 11.314 0" /><path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" /></svg>`,
            4: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a7.963 7.963 0 0 1 3.864 -2.14m4.163 .155a7.965 7.965 0 0 1 3.287 2" /><path d="M3.515 9.515a12 12 0 0 1 3.544 -2.455m3.101 -.92a12 12 0 0 1 10.325 3.374" /><path d="M3 3l18 18" /></svg>`,
        };

        const app = document.getElementById('app');
        const wifiList = document.getElementById('wifiList');
        const passwordForm = document.getElementById('passwordForm');
        const passwordInput = document.getElementById('password');

        let selectedSSID = '';

        wifiNetworks.forEach(network => {
            const signalLevel = getSignalLevel(network.rssi);
            const div = document.createElement('div');
            div.classList.add('wifi-network');
            div.innerHTML = `
                <span class="wifi-name">${svgIcons[signalLevel]} ${network.ssid}</span>
            `;

            // Solo agregar el event listener si signalLevel no es 4
            if (signalLevel !== 4) {
                div.addEventListener('click', () => {
                    selectedSSID = network.ssid;
                    passwordForm.classList.remove('hide');
                    passwordForm.style.display = 'flex';
                    passwordForm.classList.add('show');
                    passwordInput.focus();

                    // Remover clase active de todos
                    document.querySelectorAll('.wifi-network').forEach(el => el.classList.remove('active'));
                    // Agregar clase active al actual
                    div.classList.add('active');
                });
            }

            wifiList.appendChild(div);
        });

        passwordForm.addEventListener('click', function (e) {
            if (e.target === passwordForm) {
                passwordForm.classList.add('hide');
                passwordForm.classList.remove('show');
                document.getElementById('password').value = '';
                statusIcon.className = 'icon';
                statusIcon.textContent = '';
                inputContainer.classList.remove('valid', 'invalid');
                // Remueve la clase active de cualquier red
                document.querySelectorAll('.wifi-network').forEach(el => el.classList.remove('active'));
                selectedSSID = '';
            }
        });

        const passwordModalContent = document.getElementById('passwordModalContent');
        const statusIcon = document.getElementById('statusIcon');
        const inputContainer = document.getElementById('inputContainer');
        const resultModal = document.getElementById('resultModal');
        const resultIcon = document.getElementById('resultIcon');
        const resultTitle = document.getElementById('resultTitle');
        const resultMessage = document.getElementById('resultMessage');
        const ip = document.getElementById('ip');

        // Al escribir de nuevo en el input, volver al estado neutro
        passwordInput.addEventListener('input', () => {
            inputContainer.classList.remove('valid', 'invalid');
            passwordInput.style.borderColor = '#ddd';
            statusIcon.className = 'icon';
            statusIcon.textContent = '';
        });

        function connect() {
            const password = passwordInput.value;
            // Mostrar spinner
            statusIcon.className = 'icon spinner';
            statusIcon.textContent = '';
            inputContainer.classList.remove('valid', 'invalid');

            fetch('/connect', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ ssid: selectedSSID, password: password }).toString()
            }).then(response => {
                if (!response.ok) throw new Error(`Error HTTP! Estado: ${response.status}`);
                return response.json();
            }).then(data => {
                if (data.status === "success") {
                    // Validación exitosa
                    ip.textContent = `IP asignada: ${data.ip}`;
                    statusIcon.className = 'icon';
                    statusIcon.textContent = '✔';
                    inputContainer.classList.add('valid');
                    setTimeout(() => {
                        passwordForm.classList.add('hide');
                    }, 1000);
                    // Ocultar el modal de contraseña con animación descendente
                    setTimeout(() => {
                        // Quitamos la clase 'show' para ocultar el modal con fade
                        passwordForm.classList.remove('show');
                        setTimeout(() => {
                            app.classList.add('slide-left');
                            setTimeout(() => {
                                app.style.display = 'none';
                                // Mostrar modal de resultado final
                                resultModal.classList.add('show');
                            }, 500);
                        }, 500);
                    }, 1500);
                } else {
                    // Validación fallida
                    statusIcon.className = 'icon';
                    statusIcon.textContent = '✖';
                    inputContainer.classList.add('invalid');
                }
            });
        }
    </script>
</body>

</html>
)raw";

#endif