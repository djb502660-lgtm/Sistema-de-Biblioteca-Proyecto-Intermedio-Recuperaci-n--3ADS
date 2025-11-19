import { API_URL } from "./config.js";

/**
 * Función para cargar estadísticas desde el backend.
 * @param {string} accion - Acción a solicitar al endpoint.
 * @param {string} elementoId - ID del elemento HTML donde se mostrará el resultado.
 */
async function cargarEstadistica(accion, elementoId) {
    try {
        const response = await fetch(`${API_URL}?accion=${accion}`);
        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor");
        }

        const data = await response.json();

        if (data.status === "success") {
            document.getElementById(elementoId).textContent = data.total;
        } else {
            console.error(`Error al cargar ${accion}:`, data.message);
        }

    } catch (error) {
        console.error(`Error al cargar la estadística (${accion}):`, error);
    }
}

// Ejecutar cuando cargue la página
document.addEventListener("DOMContentLoaded", () => {
    cargarEstadistica("contar_usuarios", "total-usuarios");
    cargarEstadistica("contar_libros", "total-libros");
    cargarEstadistica("contar_prestamos", "total-prestamos");
});
