/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from "laravel-echo";

import Pusher from "pusher-js";

import { io } from 'socket.io-client';


// Variables globales
let pusherAttempts = 0; // Contador de intentos de conexión a Pusher
const maxPusherAttempts = 5; // Límite de intentos
let echoInstance = null; // Instancia de Echo
let isSocketConnected = false; // Estado del socket

// Configuración de Pusher
function createPusherInstance() {
  const echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_PUSHER_HOST
      ? import.meta.env.VITE_PUSHER_HOST
      : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
    debug: true,
  });

  // Manejador de errores de conexión en Pusher
  echo.connector.pusher.connection.bind("error", (err) => {
    console.error("Error de conexión en Pusher:", err);

    // Intentar reconectar si no alcanzó el límite
    if (pusherAttempts < maxPusherAttempts) {
      pusherAttempts++;
      console.log(`Intento ${pusherAttempts} de conexión a Pusher...`);
    } else {
      console.warn("Máximos intentos alcanzados. Cambiando a Socket.IO...");
      switchToSocketIO();
    }
  });

  return echo;
}

// Inicializar Pusher
echoInstance = createPusherInstance();

/**
 * Configuración de Socket.IO
 */
const socket = io("http://localhost:3001", {
  transports: ["websocket"],
  reconnection: false, // Desactivar reconexión automática
});

// Cambiar a Socket.IO
function switchToSocketIO() {
  if (echoInstance) {
    echoInstance.disconnect(); // Desconecta Pusher
    echoInstance = null;
  }

  console.log("Conectándose a Socket.IO...");
  socket.connect();

  // Eventos de Socket.IO
  socket.on("connect", () => {
    isSocketConnected = true;
    console.log("Conectado a Socket.IO: ", socket.id);
  });

  socket.on("localNotification", (data) => {
    console.log("Notificación recibida desde Socket.IO:", data);
  });

  socket.on("error", (err) => {
    console.error("Error en Socket.IO:", err);
  });
}

// Listeners iniciales
socket.on("disconnect", () => {
  isSocketConnected = false;
  console.warn("Desconectado de Socket.IO");
});
// Exportar el objeto de Socket.IO y la instancia de Pusher (si es necesario)
export { socket, echoInstance };
