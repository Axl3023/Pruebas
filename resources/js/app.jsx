import React, { useState } from "react";
import ReactDOM from "react-dom/client";
import { useForm } from "react-hook-form";

function FormularioFile() {
    const {
        register,
        handleSubmit,
        formState: { errors }
    } = useForm();

    const [uploading, setUploading] = useState(false); // ✅ Estado para indicar carga
    const [serverError, setServerError] = useState(null); // ✅ Estado para errores del servidor

    const onSubmit = async (data) => {
        setUploading(true); // Bloquear botón al subir archivo
        setServerError(null); // Limpiar errores previos

        const formData = new FormData();
        formData.append("archivo", data.archivo[0]);

        // Adjuntar token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
        formData.append("_token", csrfToken);

        try {
            const response = await fetch("/documentos", {
                method: "POST",
                body: formData,
            });

            const result = await response.json(); // Convertir respuesta a JSON

            if (!response.ok) {
                throw new Error(result.message || "Error en la subida");
            }

            alert("Archivo subido con éxito. ID: " + result.document_id);
        } catch (error) {
            console.error("Error en la subida:", error);
            setServerError(error.message); // Mostrar error en la UI
        } finally {
            setUploading(false); // ✅ Permitir otro intento de subida
        }
    };

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <label htmlFor="archivo">Selecciona un archivo:</label>
            <input
                type="file"
                id="archivo"
                accept=".jpg,.png,.pdf,.doc,.docx" // ✅ Solo tipos de archivo permitidos
                {...register("archivo", {
                    required: "Debes seleccionar un archivo",
                })}
            />
            {errors.archivo && <div style={{ color: "red" }}>{errors.archivo.message}</div>}

            {serverError && <div style={{ color: "red" }}>⚠ {serverError}</div>} {/* ✅ Error del backend */}

            <br />
            <button type="submit" disabled={uploading}>
                {uploading ? "Subiendo..." : "Subir"} {/* ✅ Indicador de carga */}
            </button>
        </form>
    );
}

ReactDOM.createRoot(document.getElementById("app")).render(<FormularioFile />);
