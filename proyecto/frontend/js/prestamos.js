const response = await fetch(`${API_URL}?accion=contar_prestamos`);
import { API_URL } from "./config.js";

// --- ELEMENTOS DEL DOM ---
const tablaPrestamos = document.getElementById("tablaPrestamos");
const formCrear = document.getElementById("formPrestamo");
const formEditar = document.getElementById("formEditarPrestamo");

/**
 * Carga y muestra los préstamos en la tabla.
 */
async function cargarPrestamos() {
    try {
        const res = await fetch(`${API_URL}?action=mostrar_prestamos`);
        const data = await res.json();

        const tbody = tablaPrestamos.querySelector("tbody");
        tbody.innerHTML = ""; // Limpiar tabla

        if (data.success && data.data?.length > 0) {
            const rows = data.data.map(p => {
                // Asignar clases de Bootstrap según el estado del préstamo
                const estadoClass = p.estado === 'devuelto' ? 'text-success' : (p.estado === 'pendiente' ? 'text-warning' : 'text-danger');
                const estadoText = p.estado.charAt(0).toUpperCase() + p.estado.slice(1);

                return `
                    <tr>
                        <td>${p.id_prestamo}</td>
                        <td>${p.nombre_usuario}</td>
                        <td>${p.titulo_libro}</td>
                        <td>${p.fecha_prestamo}</td>
                        <td>${p.fecha_devolucion || 'N/A'}</td>
                        <td><strong class="${estadoClass}">${estadoText}</strong></td>
                        <td class="text-center">
                            <a href="editar_prestamos.html?id=${p.id_prestamo}" class="btn btn-warning btn-sm">✏️ Editar</a>
                        </td>
                    </tr>
                `;
            }).join('');
            tbody.innerHTML = rows;
        } else {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center">${data.message || "No hay préstamos registrados."}</td></tr>`;
        }
    } catch (error) {
        console.error("Error al cargar los préstamos:", error);
        const tbody = tablaPrestamos.querySelector("tbody");
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">Error al cargar los datos.</td></tr>`;
    }
}

/**
 * Carga los datos de un préstamo específico en el formulario de edición.
 * @param {string} id - El ID del préstamo a cargar.
 */
async function cargarPrestamo(id) {
    try {
        const res = await fetch(`${API_URL}?action=obtener_prestamo&id_prestamo=${id}`);
        const data = await res.json();

        if (data.success && data.data) {
            const p = data.data;
            document.getElementById("id_prestamo").value = p.id_prestamo;
            document.getElementById("id_usuario").value = p.id_usuario;
            document.getElementById("id_libro").value = p.id_libro;
            document.getElementById("fecha_prestamo").value = p.fecha_prestamo;
            document.getElementById("fecha_devolucion").value = p.fecha_devolucion;
            document.getElementById("estado").value = p.estado;

            // Mostrar nombres en campos deshabilitados para referencia
            document.getElementById("usuario_nombre").value = `${p.nombre_usuario} (ID: ${p.id_usuario})`;
            document.getElementById("libro_titulo").value = `${p.titulo_libro} (ID: ${p.id_libro})`;
        } else {
            alert(data.message || "No se pudo cargar el préstamo.");
            window.location.href = "prestamos.html";
        }
    } catch (error) {
        console.error("Error al cargar el préstamo:", error);
        alert("Error de conexión al cargar los datos del préstamo.");
    }
}

/**
 * Carga opciones (usuarios o libros) en un elemento <select>.
 * @param {string} action - La acción de la API ('mostrar_usuarios' o 'mostrar_libros').
 * @param {string} selectId - El ID del elemento <select>.
 * @param {string} valueField - El nombre del campo para el 'value' de la opción.
 * @param {string} textField - El nombre del campo para el texto de la opción.
 */
async function cargarOpcionesSelect(action, selectId, valueField, textField) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;

    try {
        const res = await fetch(`${API_URL}?action=${action}`);
        const data = await res.json();

        if (data.success && data.data?.length > 0) {
            data.data.forEach(item => {
                const option = document.createElement("option");
                option.value = item[valueField];
                option.textContent = item[textField];
                selectElement.appendChild(option);
            });
        }
    } catch (error) {
        console.error(`Error al cargar ${action}:`, error);
    }
}

/**
 * Envía los datos de un formulario a la API.
 * @param {Event} e - El evento de submit del formulario.
 */
async function enviarFormulario(e) {
    e.preventDefault();
    const formData = new FormData(e.target);

    try {
        const res = await fetch(API_URL, { method: "POST", body: formData });
        const data = await res.json();

        alert(data.message || "Operación procesada.");

        if (data.success) {
            window.location.href = "prestamos.html";
        }
    } catch (error) {
        console.error("Error al enviar el formulario:", error);
        alert("Error de conexión con el servidor.");
    }
}

// --- LÓGICA DE EJECUCIÓN ---

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);

    // Si estamos en la página de listar préstamos, los cargamos.
    if (tablaPrestamos) {
        cargarPrestamos();
    }

    // Si estamos en la página de agregar préstamo, cargamos los selects y configuramos el form.
    if (formCrear) {
        cargarOpcionesSelect("mostrar_usuarios", "id_usuario", "id_usuario", "nombre");
        cargarOpcionesSelect("mostrar_libros", "id_libro", "id_libro", "titulo");
        formCrear.addEventListener("submit", enviarFormulario);
    }

    // Si estamos en la página de editar préstamo, cargamos los datos y configuramos el form.
    if (formEditar) {
        const id = params.get("id");
        if (id) {
            cargarPrestamo(id);
            formEditar.addEventListener("submit", enviarFormulario);
        } else {
            alert("No se especificó un ID de préstamo para editar.");
            window.location.href = "prestamos.html";
        }
    }
});
