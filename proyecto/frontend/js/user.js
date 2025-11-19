const response = await fetch(`${API_URL}?accion=contar_usuarios`);
import { API_URL } from "./config.js";
// --- ELEMENTOS DEL DOM ---
const tablaUsuarios = document.getElementById("tablaUsuarios");
const formAgregar = document.getElementById("formAgregar");
const formEditar = document.getElementById("formEditar");
const formEliminar = document.getElementById("formEliminar");

/**
 * Carga y muestra los usuarios en la tabla.
 */
async function cargarUsuarios() {
    try {
        const res = await fetch(`${API_URL}?action=mostrar_usuarios`);
        const data = await res.json();

        const tbody = tablaUsuarios.querySelector("tbody");
        tbody.innerHTML = ""; // Limpiar tabla

        if (data.success && data.data?.length > 0) {
            const rows = data.data.map(u => `
                <tr>
                    <td>${u.id_usuario}</td>
                    <td>${u.nombre}</td>
                    <td>${u.correo}</td>
                    <td>${u.telefono || 'N/A'}</td>
                    <td class="text-center">
                        <a href="editar_usuario.html?id=${u.id_usuario}" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="eliminar_usuario.html?id=${u.id_usuario}" class="btn btn-danger btn-sm">🗑️ Eliminar</a>
                    </td>
                </tr>
            `).join('');
            tbody.innerHTML = rows;
        } else {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center">${data.message || "No hay usuarios registrados."}</td></tr>`;
        }
    } catch (error) {
        console.error("Error al cargar los usuarios:", error);
        const tbody = tablaUsuarios.querySelector("tbody");
        tbody.innerHTML = `<tr><td colspan="5" class="text-center">Error al cargar los datos.</td></tr>`;
    }
}

/**
 * Carga los datos de un usuario específico en el formulario de edición.
 * @param {string} id - El ID del usuario a cargar.
 */
async function cargarUsuarioParaEditar(id) {
    try {
        const res = await fetch(`${API_URL}?action=obtener_usuario&id_usuario=${id}`);
        const data = await res.json();

        if (data.success && data.data) {
            const u = data.data;
            document.getElementById("id_usuario").value = u.id_usuario;
            document.getElementById("nombre").value = u.nombre;
            document.getElementById("correo").value = u.correo;
            document.getElementById("telefono").value = u.telefono;
        } else {
            alert(data.message || "No se pudieron cargar los datos del usuario.");
            window.location.href = "usuarios.html";
        }
    } catch (error) {
        console.error("Error al cargar el usuario:", error);
        alert("Error de conexión al cargar los datos del usuario.");
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
            window.location.href = "usuarios.html";
        }
    } catch (error) {
        console.error("Error al enviar el formulario:", error);
        alert("Error de conexión con el servidor.");
    }
}

// --- LÓGICA DE EJECUCIÓN ---

document.addEventListener("DOMContentLoaded", () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");

    if (tablaUsuarios) {
        cargarUsuarios();
    }

    if (formAgregar) {
        formAgregar.addEventListener("submit", enviarFormulario);
    }

    if (formEditar && id) {
        cargarUsuarioParaEditar(id);
        formEditar.addEventListener("submit", enviarFormulario);
    }

    if (formEliminar && id) {
        document.getElementById("id_usuario").value = id;
        formEliminar.addEventListener("submit", enviarFormulario);
    }
});