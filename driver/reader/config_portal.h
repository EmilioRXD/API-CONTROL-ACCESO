#ifndef CONFIG_PORTAL_H
#define CONFIG_PORTAL_H

const char part1[] PROGMEM = R"raw(
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configuración de Red Wi-Fi</title>
    <link rel="stylesheet" href="style.css" />
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

      /* Estilos para el splash screen con logo */
      .splash-screen {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          background-color: #f9f9f9;
          z-index: 1000;
          transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out;
      }

      .splash-logo {
          width: 150px;
          height: 150px;
          margin-bottom: 20px;
          position: relative;
      }

      .splash-logo svg {
          width: 100%;
          height: 100%;
          filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
          animation: logoFadeIn 1.2s ease-out forwards;
      }

      @keyframes logoFadeIn {
          0% {
              opacity: 0;
              transform: scale(0.8);
          }
          50% {
              opacity: 1;
              transform: scale(1.05);
          }
          100% {
              opacity: 1;
              transform: scale(1);
          }
      }

      .loader {
          width: 80px;
          height: 4px;
          margin-top: 15px;
          background-color: #eaeaea;
          border-radius: 2px;
          overflow: hidden;
          position: relative;
          opacity: 0;
          animation: loaderFadeIn 0.5s 0.6s ease forwards;
      }

      @keyframes loaderFadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
      }

      .loader::after {
          content: '';
          position: absolute;
          width: 40%;
          height: 100%;
          background-color: #000060;
          border-radius: 2px;
          animation: loading 1.5s infinite ease-in-out;
      }

      @keyframes loading {
          0% {
              left: -40%;
          }
          100% {
              left: 100%;
          }
      }

      .splash-text {
          margin-top: 15px;
          font-size: 16px;
          color: #3D3D3D;
          opacity: 0;
          animation: textFadeIn 0.5s 0.8s ease forwards;
      }

      @keyframes textFadeIn {
          from { opacity: 0; transform: translateY(10px); }
          to { opacity: 1; transform: translateY(0); }
      }

      .splash-hide {
          opacity: 0;
          visibility: hidden;
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
          color: #292d6b;
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
          border-color: #292d6b;
      }

      .wifi-name {
          font-weight: 500;
          font-size: 1em;
          display: flex;
          align-items: center;
          gap: 10px;
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
          border-color: #292d6b;
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

      /* Modal de contraseña y de resultado usan la misma clase base */
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

      /* Animación para ocultar el modal descendiendo */
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
          border-color: #292d6b;
          outline: none;
      }

      /* Contenedor del input + ícono */
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

      /* Spinner: se mostrará durante la validación */
      .spinner {
          width: 18px;
          height: 18px;
          border: 2px solid #ccc;
          border-top: 2px solid #292d6b;
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
          background: #292d6b;
          border: none;
          border-radius: 8px;
          color: white;
          font-weight: bold;
          cursor: pointer;
          transition: all 0.3s ease;
      }

      .connect-button:hover {
          background: #292d6b;
          transform: translateY(-2px);
      }

      /* Clases para input válido/erróneo */
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

      /* Estilos para el contenedor de entrada */
      .input-container {
          position: relative;
          margin-bottom: 1rem;
          width: 100%;
      }

      .input-container input {
          padding: 12px;
          border: 2px solid #ddd;
          border-radius: 8px;
          font-size: 16px;
          transition: border-color 0.3s ease;
          width: 100%;
          box-sizing: border-box;
      }

      .input-container input:focus {
          border-color: #292d6b;
          outline: none;
      }

      .form-group {
          margin-bottom: 1.2rem;
          text-align: left;
      }

      .form-group label {
          display: block;
          margin-bottom: 0.5rem;
          font-weight: 500;
          color: #333;
      }

      .select-container {
          position: relative;
      }

      .select-container select {
          width: 100%;
          padding: 12px;
          border: 2px solid #ddd;
          border-radius: 8px;
          font-size: 16px;
          background-color: white;
          appearance: none;
          -webkit-appearance: none;
          -moz-appearance: none;
          cursor: pointer;
      }

      .select-container::after {
          content: '\25BC';
          font-size: 12px;
          position: absolute;
          right: 12px;
          top: 50%;
          transform: translateY(-50%);
          pointer-events: none;
          color: #3D3D3D;
      }

      .select-container select:focus {
          border-color: #292d6b;
          outline: none;
      }

      .error-message {
          color: #f00;
          font-size: 14px;
          margin-bottom: 1rem;
          padding: 0.5rem;
          background-color: rgba(255, 0, 0, 0.05);
          border-radius: 4px;
          text-align: center;
      }
    </style>
  </head>

  <body>
    <!-- Splash Screen con Logo -->
    <div class="splash-screen" id="splashScreen">
      <div class="splash-logo">
        <svg
          id="Capa_1"
          data-name="Capa 1"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 530.27 252.3"
        >
          <title>LOGO</title>
          <path
            d="M28.44,356.19l46-25.53-51.23-8.87L22.8,321l45.1-25,0-.77L17.8,286.56l-0.09-.87c9.2-5,18.41-10.05,27.58-15.14,7.18-4,14.26-8.13,21.44-12.09,10.06-5.56,20.19-11,30.26-16.53,6.25-3.45,12.42-7,18.66-10.51,7.85-4.37,15.76-8.65,23.61-13,11.19-6.26,22.33-12.63,33.53-18.89,13-7.28,26.14-14.49,39.21-21.73s26.17-14.5,39.26-21.74c1.72-1,3.58-1.68,5.2-2.77,4.21-2.85,8.5-.82,12.65-0.18,12.14,1.89,24.24,4.08,36.35,6.19,6.83,1.19,13.63,2.5,20.46,3.68,11.11,1.91,22.25,3.71,33.36,5.64,17.63,3.06,35.24,6.21,52.87,9.29q23.48,4.1,47,8.13,25.35,4.38,50.7,8.74c7.85,1.34,15.72,2.54,23.57,3.83a23.43,23.43,0,0,1,2.9,1L491,224.72l0.16,1,50.94,8.78-46.25,25.8L548,269.3c-3,1.82-5.2,3.25-7.49,4.52q-12.83,7.11-25.71,14.14c-10.1,5.55-20.23,11.05-30.31,16.64-6.91,3.83-13.73,7.8-20.64,11.62-10.17,5.62-20.4,11.14-30.58,16.74-7,3.86-14,7.81-21,11.7q-16.63,9.25-33.27,18.47c-6.55,3.65-13.05,7.4-19.62,11-10.54,5.82-21.13,11.56-31.67,17.39-7.18,4-14.29,8-21.47,12A3.75,3.75,0,0,1,304,404c-12.1-2.12-24.18-4.38-36.29-6.45-15.29-2.62-30.6-5-45.89-7.67-11.12-1.91-22.2-4.06-33.32-6-11.74-2-23.5-3.87-35.25-5.84q-14.69-2.47-29.37-5-15.72-2.73-31.42-5.51c-11.62-2-23.22-4.14-34.85-6.12-9.29-1.58-18.61-3-27.91-4.53A6.68,6.68,0,0,1,28.44,356.19ZM40,354l0.19,0.71c0.88,0.21,1.76.46,2.65,0.61q16.22,2.78,32.44,5.55L230,387.45c23.88,4.11,47.76,8.28,71.65,12.33a8.3,8.3,0,0,0,4.76-.62c4.3-2.12,8.44-4.57,12.63-6.89l39.14-21.7,39.14-21.7L436,327.49q19.7-10.92,39.4-21.86c11.66-6.51,23.28-13.1,34.95-19.6,5.37-3,10.85-5.79,16.23-8.76,3.2-1.76,6.32-3.68,9.47-5.52a10.11,10.11,0,0,0-4.21-1.54l-25.95-4.46L340,237.24c-21.62-3.72-43.23-7.48-64.86-11.1a9,9,0,0,0-5.32.7c-10.77,5.72-21.45,11.62-32.13,17.52q-19.59,10.81-39.14,21.7L159.12,287.9l-38.86,21.54Q100.83,320.21,81.41,331c-11.76,6.56-23.47,13.19-35.22,19.76C44.16,351.89,42.06,352.9,40,354Zm484.35-152.2,0-.77c-0.9-.21-1.79-0.47-2.7-0.63q-41.84-7.23-83.68-14.45L325.5,166.59q-31.35-5.39-62.72-10.7a7,7,0,0,0-4.11.58c-7.11,3.74-14.13,7.63-21.16,11.52q-19.45,10.75-38.87,21.55-19.84,11-39.68,22c-12.87,7.12-25.77,14.17-38.63,21.31-16.06,8.91-32.07,17.9-48.13,26.81-5.29,2.93-10.67,5.7-16,8.58-2.47,1.34-4.88,2.8-7.31,4.2a2.24,2.24,0,0,0,2,.07q16.88-6.71,33.74-13.43,33.27-13.32,66.53-26.68,18.74-7.51,37.49-15c20.24-8.13,40.52-16.18,60.7-24.46,9.12-3.75,18.07-7.79,28.17-3.78a8,8,0,0,0,1.55.2L411,212.13c23.16,4,46.31,8,69.49,11.93a8.25,8.25,0,0,0,4.73-.73c4.94-2.46,9.77-5.15,14.6-7.82C508,211,516.16,206.35,524.34,201.78Zm-470.22,106,0.34,0.48c0.48-.15,1-0.26,1.44-0.45q34.76-13.85,69.51-27.73c11.53-4.63,23-9.49,34.5-14.09,18.42-7.35,36.91-14.52,55.34-21.84,16-6.36,31.93-12.93,48-19.16,3.48-1.35,6.78-3.44,11-2.69C304.5,227.64,334.77,232.82,365,238q29,5,58.07,10c21.4,3.68,42.8,7.38,64.21,11a7,7,0,0,0,4.13-.76c5.89-3.15,11.67-6.49,17.5-9.75q7.51-4.21,15-8.39c2-1.12,4.1-2.16,6.16-3.23a25.53,25.53,0,0,0-6.91-1.91q-44.89-7.7-89.78-15.41-45.86-7.89-91.71-15.83c-24.39-4.22-48.78-8.49-73.19-12.58a10.08,10.08,0,0,0-5.62,1c-4,1.93-7.9,4.24-11.82,6.42q-20.38,11.32-40.76,22.64-18.88,10.47-37.78,20.91-26.43,14.62-52.86,29.23c-11.68,6.48-23.31,13.06-35,19.54-5.38,3-10.86,5.77-16.25,8.73C63.67,302.29,58.91,305,54.12,307.75Z"
            transform="translate(-17.71 -151.76)"
            style="fill: #000060"
          />
          <path
            d="M291.27,329.69c2-.67,3.24-1.22,4.57-1.53,6.73-1.57,5.23.05,6.57-6.61q3.14-15.6,6.22-31.21c0.2-1,.27-2,0.45-3.35-5.24.43-9.72,1.9-13.27,5.33-2.89,2.8-5.36,6-8.12,9a22.85,22.85,0,0,1-3.3,2.47l-0.79-.79c1.58-4.49,3.09-9,4.8-13.46a3.18,3.18,0,0,1,2.14-1.46q27.54-5.24,55.11-10.34a12.68,12.68,0,0,1,1.74,0c-0.81,4.63-1.45,9.13-2.5,13.54-0.17.73-2.13,1-3.16,1.49a3.92,3.92,0,0,1-.49-0.68c-1.39-8.64-7.83-12.11-15.81-8.4a4.06,4.06,0,0,0-1.57,2.7c-1.91,10.15-3.71,20.33-5.57,30.49-0.41,2.21-1,4.39-1.55,7.12l9.21-1.36,0.4,0.68c-0.57.49-1.09,1.3-1.73,1.42-10.66,2-21.33,4-32,5.89A9.44,9.44,0,0,1,291.27,329.69Z"
            transform="translate(-17.71 -151.76)"
            style="fill: #000060"
          />
          <path
            d="M219.55,303.69L211.12,305l-0.4-.77c0.49-.47.92-1.26,1.49-1.37,10.12-2,20.25-3.82,30.38-5.71l0.49,1.06a12.69,12.69,0,0,1-2.88,1.41c-6.48,1.32-5.45-.19-6.66,6.82-1.28,7.41-2.65,14.81-3.71,22.25-0.82,5.74,1.82,9,7.63,9.51A32.13,32.13,0,0,0,255,334.44a14,14,0,0,0,7.67-9.46c2.29-10,4.49-20,6.86-30.65l-10.13,1.44c0.82-1.1,1.12-1.95,1.58-2,6.41-1.29,12.83-2.48,19.26-3.69l0.47,1.22a14.18,14.18,0,0,1-3,1.26c-5.48.94-5.46,0.9-6.42,6.36-1.23,7-2.51,14-3.74,21-1.65,9.43-7.22,15.34-16.1,18.55a64.77,64.77,0,0,1-23.95,4,22.52,22.52,0,0,1-5.54-.86c-5.68-1.63-7.84-4.59-7-10.43,1.11-7.86,2.63-15.66,4-23.48C219.09,306.5,219.27,305.38,219.55,303.69Z"
            transform="translate(-17.71 -151.76)"
            style="fill: #000060"
          />
          <path
            d="M388.32,268.55l1.49,41.66,8.76-1.31,0.44,0.69c-0.53.51-1,1.38-1.61,1.49-9.74,1.85-19.49,3.61-29.24,5.38a4.39,4.39,0,0,1-1.19-.11l-0.34-.82a14.07,14.07,0,0,1,2.81-1.22c5.85-1.17,5.86-1.14,5.6-7.24-0.05-1.22-.17-2.44-0.29-4.14-5.33,1-10.28,2.13-15.3,2.76a11.82,11.82,0,0,0-8.59,4.89c-2,2.72-4.45,5.16-6.41,8.33l7.71-1.32,0.45,0.61c-0.43.5-.8,1.35-1.31,1.46-6.19,1.28-12.4,2.45-18.6,3.65l-0.45-.79c0.56-.52,1.13-1.51,1.69-1.5,5.55,0.08,8.07-4.44,11.15-7.65,7.72-8,15-16.53,22.5-24.74,5.48-6,11.09-11.81,16.72-17.63C385.13,270.11,386.44,269.66,388.32,268.55ZM374.73,286.3l-1.32-.21-15.47,17.08A97.54,97.54,0,0,0,373.06,301a2.35,2.35,0,0,0,1.59-1.47C374.78,295.13,374.73,290.71,374.73,286.3Z"
            transform="translate(-17.71 -151.76)"
            style="fill: #000060"
          />
          <path
            d="M182.79,310.66l-6,.75-0.33-.8a7.5,7.5,0,0,1,2.24-1.44c8.79-1.72,17.6-3.34,26.41-5l0.53,0.95c-0.9.52-1.8,1.5-2.7,1.49-4.09,0-5,2.33-5.56,5.86-1.72,10.73-3.81,21.4-5.74,32.1-0.16.89-.2,1.8-0.34,3.12l6.58-1.06,0.45,0.67c-0.61.53-1.16,1.41-1.85,1.54-8.61,1.67-17.24,3.24-25.87,4.82A7.23,7.23,0,0,1,169,353a27.45,27.45,0,0,1,4.35-1.75c1.53-.3,2.74-0.52,3.07-2.43,2.08-12.15,4.24-24.29,6.37-36.43A12.62,12.62,0,0,0,182.79,310.66Z"
            transform="translate(-17.71 -151.76)"
            style="fill: #000060"
          />
        </svg>
      </div>
      <div class="loader"></div>
      <div class="splash-text">Configuración WiFi</div>
    </div>

    <div class="config-container" id="app" style="display: none">
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
            <input
              type="password"
              id="password"
              placeholder="Ingresa la contraseña"
            />
            <span class="icon" id="statusIcon"></span>
          </div>
          <button class="connect-button" onclick="connect()">Conectar</button>
        </div>
      </div>
    </div>
    <!-- Formulario de configuración de servidor -->
    <div
      class="modal-background wifi-password"
      id="serverConfigForm"
      style="display: none"
    >
      <div class="modal-content">
        <h3>Configuración del Servidor</h3>
        <p>Por favor, completa la información de tu servidor</p>

        <div class="form-group">
          <label for="serverUrl">URL del Servidor:</label>
          <div class="input-container">
            <input
              type="text"
              id="serverUrl"
              placeholder="Ejemplo: 192.168.0.100:8000"
            />
          </div>
        </div>

        <div class="form-group">
          <label for="deviceName">Ubicación del Dispositivo:</label>
          <div class="input-container">
            <input
              type="text"
              id="deviceName"
              placeholder="Ejemplo: Lector NFC-01"
            />
          </div>
        </div>

        <div class="form-group">
          <label for="deviceType">Tipo de Dispositivo:</label>
          <div class="select-container">
            <select id="deviceType">
              <option value="">Selecciona una opción</option>
              <option value="ENTRADA">Entrada</option>
              <option value="SALIDA">Salida</option>
            </select>
          </div>
        </div>

        <div class="error-message" id="formErrorMessage" style="display: none">
          Error al conectar con el servidor. Por favor, verifica los datos
          ingresados.
        </div>

        <button class="connect-button" onclick="submitServerConfig()">
          Enviar Configuración
        </button>
      </div>
    </div>

    <!-- Modal de resultado exitoso -->
    <div class="result-modal" id="resultModal">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="96"
        height="96"
        viewBox="0 0 24 24"
        fill="none"
        stroke="#16C47F"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"
      >
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
        <path d="M9 12l2 2l4 -4" />
      </svg>
      <h3>Configuración Exitosa</h3>
      <p>La red se configuró correctamente.</p>
      <br />
      <span id="ip"></span>
    </div>
    <script>
      const wifiNetworks = [
)raw";

const char part3[] PROGMEM = R"raw(
];
      
      // Mostrar el contenido principal después de la animación
      window.addEventListener("DOMContentLoaded", function () {
        const app = document.getElementById("app");
        const wifiList = document.getElementById("wifiList");
        const passwordForm = document.getElementById("passwordForm");
        const passwordInput = document.getElementById("password");
        const statusIcon = document.getElementById("statusIcon");
        const inputContainer = document.getElementById("inputContainer");
        const resultModal = document.getElementById("resultModal");
        const ip = document.getElementById("ip");

        // Cargar las redes WiFi
        wifiNetworks.forEach((network) => {
          const signalLevel = getSignalLevel(network.rssi);
          const div = document.createElement("div");
          div.classList.add("wifi-network");
          div.innerHTML = `
              <span class="wifi-name">${svgIcons[signalLevel]} ${network.ssid}</span>
          `;

          // Solo agregar el event listener si signalLevel no es 4
          if (signalLevel !== 4) {
            div.addEventListener("click", () => {
              selectedSSID = network.ssid;
              passwordForm.classList.remove("hide");
              passwordForm.style.display = "flex";
              passwordForm.classList.add("show");
              passwordInput.focus();

              // Remover clase active de todos
              document
                .querySelectorAll(".wifi-network")
                .forEach((el) => el.classList.remove("active"));
              // Agregar clase active al actual
              div.classList.add("active");
            });
          }

          wifiList.appendChild(div);
        });
        
        // Mostrar la aplicación luego de la animación del splash screen
        setTimeout(function () {
          const splashScreen = document.getElementById("splashScreen");

          splashScreen.classList.add("splash-hide");
          app.style.display = "grid";

          // Asegurar que el splash screen no interfiera después de la animación
          setTimeout(function () {
            splashScreen.style.display = "none";
          }, 800);
        }, 2500); // Tiempo total de la animación de carga
      });

      // Función para determinar el nivel de señal (0-3) según el RSSI
      function getSignalLevel(rssi) {
        if (rssi == 1) return 4; // No se encontraron redes
        else if (rssi >= -50) return 3; // Excelente (-30 a -50 dBm)
        else if (rssi >= -65) return 2; // Buena (-50 a -65 dBm)
        else if (rssi >= -75) return 1; // Aceptable (-65 a -75 dBm)
        else return 0; // Mala (< -75 dBm)
      }

      const svgIcons = {
        0: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /></svg>`,
        1: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /></svg>`,
        2: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a8 8 0 0 1 11.314 0" /></svg>`,
        3: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a8 8 0 0 1 11.314 0" /><path d="M3.515 9.515c4.686 -4.687 12.284 -4.687 17 0" /></svg>`,
        4: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-wifi-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 18l.01 0" /><path d="M9.172 15.172a4 4 0 0 1 5.656 0" /><path d="M6.343 12.343a7.963 7.963 0 0 1 3.864 -2.14m4.163 .155a7.965 7.965 0 0 1 3.287 2" /><path d="M3.515 9.515a12 12 0 0 1 3.544 -2.455m3.101 -.92a12 12 0 0 1 10.325 3.374" /><path d="M3 3l18 18" /></svg>`,
      };

      // Variables para acceso global
      const app = document.getElementById("app");
      const wifiList = document.getElementById("wifiList");
      const passwordForm = document.getElementById("passwordForm");
      const passwordInput = document.getElementById("password");
      const statusIcon = document.getElementById("statusIcon");
      const inputContainer = document.getElementById("inputContainer");
      const resultModal = document.getElementById("resultModal");
      const ip = document.getElementById("ip");

      let selectedSSID = "";

      function appendNetworkToList(network) {
        const signalLevel = getSignalLevel(network.rssi);
        const div = document.createElement("div");
        div.classList.add("wifi-network");
        div.innerHTML = `
          <span class="wifi-name">${svgIcons[signalLevel]} ${network.ssid}</span>
        `;

        div.addEventListener("click", () => {
          selectedSSID = network.ssid;
          passwordForm.classList.remove("hide");
          passwordForm.style.display = "flex";
          passwordForm.classList.add("show");
          passwordInput.focus();

          // Remover clase active de todos
          document
            .querySelectorAll(".wifi-network")
            .forEach((el) => el.classList.remove("active"));
          // Agregar clase active al actual
          div.classList.add("active");
        });

        wifiList.appendChild(div);
      }

      passwordForm.addEventListener("click", function (e) {
        if (e.target === passwordForm) {
          passwordForm.classList.add("hide");
          passwordForm.classList.remove("show");
          document.getElementById("password").value = "";
          statusIcon.className = "icon";
          statusIcon.textContent = "";
          inputContainer.classList.remove("valid", "invalid");
          // Remueve la clase active de cualquier red
          document
            .querySelectorAll(".wifi-network")
            .forEach((el) => el.classList.remove("active"));
          selectedSSID = "";
        }
      });

      // Al escribir de nuevo en el input, volver al estado neutro
      passwordInput.addEventListener("input", () => {
        inputContainer.classList.remove("valid", "invalid");
        passwordInput.style.borderColor = "#ddd";
        statusIcon.className = "icon";
        statusIcon.textContent = "";
      });

      // Detectar cuando se presiona la tecla Enter en el campo de contraseña
      passwordInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
          event.preventDefault(); // Prevenir el comportamiento predeterminado
          connect(); // Ejecutar la conexión
        }
      });

      // Función de conexión WiFi con Arduino
      function connect() {
        const password = passwordInput.value;

        // Mostrar spinner
        statusIcon.className = "icon spinner";
        statusIcon.textContent = "";
        inputContainer.classList.remove("valid", "invalid");

        // Enviar datos de conexión al ESP
        fetch("/connect", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            ssid: selectedSSID,
            password: password,
          }).toString(),
        })
          .then((response) => {
            if (!response.ok)
              throw new Error(`Error HTTP! Estado: ${response.status}`);
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              // Conexión exitosa
              ip.textContent = `IP asignada: ${data.ip}`;
              statusIcon.className = "icon";
              statusIcon.textContent = "✔";
              inputContainer.classList.add("valid");

              setTimeout(() => {
                passwordForm.classList.add("hide");
              }, 1000);

              // Ocultar el modal de contraseña con animación descendente
              setTimeout(() => {
                passwordForm.classList.remove("show");
                setTimeout(() => {
                  app.classList.add("slide-left");
                  setTimeout(() => {
                    app.style.display = "none";

                    // Mostrar formulario de configuración del servidor
                    const serverConfigForm =
                      document.getElementById("serverConfigForm");
                    serverConfigForm.style.display = "flex";

                    // Añadir la clase show para la animación de fade-in
                    setTimeout(() => {
                      serverConfigForm.classList.add("show");
                    }, 100);
                  }, 500);
                }, 500);
              }, 1500);
            } else {
              // Conexión fallida
              statusIcon.className = "icon";
              statusIcon.textContent = "✖";
              inputContainer.classList.add("invalid");
            }
          })
          .catch((error) => {
            console.error("Error de conexión:", error);
            statusIcon.className = "icon";
            statusIcon.textContent = "✖";
            inputContainer.classList.add("invalid");
          });
      }

      // Referencias a elementos del formulario de configuración de servidor
      const serverUrlInput = document.getElementById("serverUrl");
      const deviceNameInput = document.getElementById("deviceName");
      const deviceTypeSelect = document.getElementById("deviceType");
      const formErrorMessage = document.getElementById("formErrorMessage");

      // Event listeners para detectar Enter en los campos del formulario
      serverUrlInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
          event.preventDefault();
          deviceNameInput.focus();
        }
      });

      deviceNameInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
          event.preventDefault();
          deviceTypeSelect.focus();
        }
      });

      // Ocultar mensaje de error al cambiar el select
      deviceTypeSelect.addEventListener("change", function () {
        formErrorMessage.style.display = "none";
      });

      // Función para enviar configuración del servidor al Arduino
      function submitServerConfig() {
        // Obtener los valores del formulario
        const serverUrl = serverUrlInput.value.trim();
        const deviceName = deviceNameInput.value.trim();
        const deviceType = deviceTypeSelect.value;

        // Validación básica (comprobar que hay datos)
        if (!serverUrl || !deviceName || !deviceType) {
          formErrorMessage.style.display = "block";
          formErrorMessage.textContent = "Por favor, completa todos los campos";

          // Resaltar campos con problemas con un borde rojo simple
          if (!serverUrl) {
            serverUrlInput.style.borderColor = "#f00";
          }

          if (!deviceName) {
            deviceNameInput.style.borderColor = "#f00";
          }

          if (!deviceType) {
            deviceTypeSelect.style.borderColor = "#f00";
          }

          return;
        }

        // Resetear estilos de los campos
        serverUrlInput.style.borderColor = "#ddd";
        deviceNameInput.style.borderColor = "#ddd";
        deviceTypeSelect.style.borderColor = "#ddd";

        // Ocultar mensaje de error si todo está bien
        formErrorMessage.style.display = "none";

        // Mostrar un spinner de carga en el botón
        const submitButton = document.querySelector(".connect-button");
        const originalText = submitButton.textContent;
        submitButton.textContent = "Enviando...";
        submitButton.disabled = true;

        // Enviar configuración al Arduino
        fetch("/server", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            url: serverUrl,
            location: deviceName,
            type: deviceType,
          }),
        })
          .then((response) => {
            if (!response.ok)
              throw new Error(`Error HTTP! Estado: ${response.status}`);
            return response.json();
          })
          .then((data) => {
            // Si todo está bien y el servidor respondió correctamente
            if (data.status === "success") {
              // Ocultar formulario y mostrar resultado exitoso con animación
              const serverForm = document.getElementById("serverConfigForm");

              // Primero, añadir clase hide para animación de descenso
              setTimeout(() => {
                serverForm.classList.add("hide");

                // Luego, ocultar completamente y mostrar el resultado
                setTimeout(() => {
                  serverForm.classList.remove("show");
                  setTimeout(() => {
                    serverForm.style.display = "none";
                    // Mostrar modal de resultado final
                    resultModal.classList.add("show");
                  }, 300);
                }, 500);
              }, 1000);
            } else {
              throw new Error(data.message || "Error de conexión");
            }
          })
          .catch((error) => {
            console.error("Error:", error);

            // Resaltar campo de URL si hay un error
            serverUrlInput.style.borderColor = "#f00";
            let errorMessage =
              "Error de conexión: No se puede establecer conexión con el servidor. Verifica que la URL sea correcta.";

            // Mostrar mensaje de error específico
            formErrorMessage.style.display = "block";
            formErrorMessage.textContent = errorMessage;

            // Restaurar el botón
            submitButton.textContent = originalText;
            submitButton.disabled = false;
          });
      }
    </script>
  </body>
</html>
)raw";

#endif