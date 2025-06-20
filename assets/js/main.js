/**
 * Funciones JavaScript para EduLink
 */

document.addEventListener('DOMContentLoaded', () => {
    // Inicializar componentes

    // Validación de formularios
    const formularios = document.querySelectorAll('form[data-validate="true"]');
    if (formularios) {
        formularios.forEach(form => {
            form.addEventListener('submit', validarFormulario);
        });
    }

    // Botones para reportar comentarios
    const botonesReporte = document.querySelectorAll('.btn-reportar');
    if (botonesReporte) {
        botonesReporte.forEach(btn => {
            btn.addEventListener('click', confirmarReporte);
        });
    }

    // Previsualización de archivos
    const inputArchivo = document.getElementById('archivo');
    if (inputArchivo) {
        inputArchivo.addEventListener('change', previsualizarArchivo);
    }

    // Animaciones para mensajes de alerta
    const alertas = document.querySelectorAll('.alert');
    if (alertas) {
        alertas.forEach(alerta => {
            setTimeout(() => {
                alerta.classList.add('fade-out');
                setTimeout(() => {
                    alerta.style.display = 'none';
                }, 500);
            }, 5000);
        });
    }

    // Inicializar contadores de caracteres en textareas
    const textareas = document.querySelectorAll('textarea[data-max-length]');
    if (textareas) {
        textareas.forEach(textarea => {
            textarea.addEventListener('input', actualizarContador);
            // Inicializar contador
            const maxLength = textarea.getAttribute('data-max-length');
            const counterId = textarea.getAttribute('data-counter-id');
            const counter = document.getElementById(counterId);
            if (counter) {
                counter.textContent = `${textarea.value.length}/${maxLength}`;
            }
        });
    }
});

/**
 * Valida formularios HTML5 y muestra mensajes personalizados
 */
function validarFormulario(e) {
    const form = e.target;
    
    // Verificar campos requeridos
    const camposRequeridos = form.querySelectorAll('[required]');
    let formValido = true;
    
    camposRequeridos.forEach(campo => {
        if (!campo.value.trim()) {
            campo.classList.add('is-invalid');
            formValido = false;
            
            // Crear mensaje de error si no existe
            let mensajeError = campo.nextElementSibling;
            if (!mensajeError || !mensajeError.classList.contains('error-message')) {
                mensajeError = document.createElement('div');
                mensajeError.classList.add('error-message', 'text-danger', 'small', 'mt-1');
                mensajeError.textContent = 'Este campo es obligatorio';
                campo.parentNode.insertBefore(mensajeError, campo.nextSibling);
            }
        } else {
            campo.classList.remove('is-invalid');
            
            // Eliminar mensaje de error si existe
            const mensajeError = campo.nextElementSibling;
            if (mensajeError && mensajeError.classList.contains('error-message')) {
                mensajeError.remove();
            }
            
            // Validar email si es campo de correo
            if (campo.type === 'email' && !validarEmail(campo.value)) {
                campo.classList.add('is-invalid');
                formValido = false;
                
                // Crear mensaje de error
                const mensajeError = document.createElement('div');
                mensajeError.classList.add('error-message', 'text-danger', 'small', 'mt-1');
                mensajeError.textContent = 'Por favor ingrese un correo electrónico válido';
                campo.parentNode.insertBefore(mensajeError, campo.nextSibling);
            }
            
            // Validar contraseñas si es registro
            if (campo.id === 'password' && form.id === 'registro-form') {
                const password = campo.value;
                if (password.length < 6) {
                    campo.classList.add('is-invalid');
                    formValido = false;
                    
                    // Crear mensaje de error
                    const mensajeError = document.createElement('div');
                    mensajeError.classList.add('error-message', 'text-danger', 'small', 'mt-1');
                    mensajeError.textContent = 'La contraseña debe tener al menos 6 caracteres';
                    campo.parentNode.insertBefore(mensajeError, campo.nextSibling);
                }
            }
            
            // Validar confirmación de contraseña
            if (campo.id === 'confirmar_password') {
                const password = document.getElementById('password').value;
                if (campo.value !== password) {
                    campo.classList.add('is-invalid');
                    formValido = false;
                    
                    // Crear mensaje de error
                    const mensajeError = document.createElement('div');
                    mensajeError.classList.add('error-message', 'text-danger', 'small', 'mt-1');
                    mensajeError.textContent = 'Las contraseñas no coinciden';
                    campo.parentNode.insertBefore(mensajeError, campo.nextSibling);
                }
            }
        }
    });
    
    // Detener envío si no es válido
    if (!formValido) {
        e.preventDefault();
        return false;
    }
    
    return true;
}

/**
 * Valida formato de email
 */
function validarEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Confirma el reporte de un comentario
 */
function confirmarReporte(e) {
    if (!confirm('¿Estás seguro de que deseas reportar este comentario?')) {
        e.preventDefault();
    }
}

/**
 * Previsualiza archivos antes de subirlos
 */
function previsualizarArchivo(e) {
    const archivo = e.target.files[0];
    const previewContainer = document.getElementById('preview-container');
    
    if (!previewContainer) return;
    
    // Limpiar previsualización anterior
    previewContainer.innerHTML = '';
    
    if (!archivo) return;
    
    // Verificar tipo de archivo
    if (archivo.type.startsWith('image/')) {
        // Es una imagen
        const img = document.createElement('img');
        img.classList.add('img-fluid', 'mt-2', 'preview-image');
        img.file = archivo;
        
        previewContainer.appendChild(img);
        
        const reader = new FileReader();
        reader.onload = (function(aImg) { 
            return function(e) { 
                aImg.src = e.target.result; 
            }; 
        })(img);
        
        reader.readAsDataURL(archivo);
    } else if (archivo.type === 'application/pdf') {
        // Es un PDF
        const iconoPDF = document.createElement('div');
        iconoPDF.innerHTML = `
            <div class="pdf-preview mt-2">
                <i class="far fa-file-pdf pdf-icon"></i>
                <p>${archivo.name}</p>
            </div>
        `;
        previewContainer.appendChild(iconoPDF);
    } else {
        // Otro tipo de archivo
        const iconoArchivo = document.createElement('div');
        iconoArchivo.innerHTML = `
            <div class="file-preview mt-2">
                <i class="far fa-file file-icon"></i>
                <p>${archivo.name}</p>
            </div>
        `;
        previewContainer.appendChild(iconoArchivo);
    }
}

/**
 * Actualiza contador de caracteres en textareas
 */
function actualizarContador(e) {
    const textarea = e.target;
    const maxLength = textarea.getAttribute('data-max-length');
    const counterId = textarea.getAttribute('data-counter-id');
    const counter = document.getElementById(counterId);
    
    if (counter) {
        counter.textContent = `${textarea.value.length}/${maxLength}`;
        
        // Cambiar color si se acerca al límite
        if (textarea.value.length > maxLength * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
        
        // Evitar exceder el límite
        if (textarea.value.length > maxLength) {
            textarea.value = textarea.value.substring(0, maxLength);
            counter.textContent = `${maxLength}/${maxLength}`;
            counter.classList.add('text-danger');
        } else {
            counter.classList.remove('text-danger');
        }
    }
}