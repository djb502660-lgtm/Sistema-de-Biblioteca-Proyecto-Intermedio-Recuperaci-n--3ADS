const response = await fetch(`${API_URL}?accion=resgistrar_usuario`);
import { API_URL } from "./config.js";

// --- ELEMENTOS DEL DOM ---
const registerForm = document.getElementById("register-form");

/**
 * Maneja el envío del formulario de registro.
 * @param {Event} e - El evento de submit del formulario.
 */
async function handleRegister(e) {
    e.preventDefault(); // Evita que la página se recargue

    const formData = new FormData(registerForm);
    
    // Añadimos la acción que el backend debe realizar
    formData.append('action', 'registrar_usuario');

    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        // Mostramos la notificación que viene del backend
        alert(data.message || "Procesando registro...");

        if (data.success) {
            // Si el registro fue exitoso, redirigimos al login
            window.location.href = 'login.html';
        }
        // Si no es exitoso, el usuario permanece en la página para corregir los datos.

    } catch (error) {
        console.error("Error al registrar el usuario:", error);
        alert("Hubo un problema de conexión. Inténtalo de nuevo más tarde.");
    }
}

// --- LÓGICA DE EJECUCIÓN ---
document.addEventListener("DOMContentLoaded", () => {
    if (registerForm) {
        registerForm.addEventListener("submit", handleRegister);
    }
});