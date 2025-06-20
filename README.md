# EduLink - Plataforma Educativa

EduLink es una aplicación web educativa diseñada para facilitar la interacción entre profesores y estudiantes. Esta plataforma permite la gestión de contenido educativo, foros de discusión y evaluaciones en un entorno digital seguro y fácil de usar.

## Requisitos

- [XAMPP](https://www.apachefriends.org/es/download.html) (versión 7.4 o superior)
- Navegador web moderno (Chrome, Firefox, Edge)

## Instalación

### 1. Instalar XAMPP

1. Descarga XAMPP desde [apachefriends.org](https://www.apachefriends.org/es/download.html)
2. Sigue las instrucciones de instalación según tu sistema operativo
3. Inicia los servicios de Apache y MySQL desde el panel de control de XAMPP

### 2. Configurar la base de datos

1. Abre tu navegador e ingresa a `http://localhost/phpmyadmin`
2. Crea una nueva base de datos llamada `edulink`
3. Selecciona la base de datos creada e importa el archivo `database/edulink.sql` incluido en este proyecto

### 3. Instalar la aplicación

1. Descarga o clona este repositorio
2. Coloca todos los archivos en la carpeta `htdocs/edulink` de tu instalación de XAMPP
   - En Windows: `C:\xampp\htdocs\edulink`
   - En macOS: `/Applications/XAMPP/htdocs/edulink`
   - En Linux: `/opt/lampp/htdocs/edulink`
3. Abre tu navegador y accede a `http://localhost/edulink`

## Uso del sistema

### Acceso al sistema

- **Administrador**: admin@edulink.com / admin123
- **Profesor**: profesor@edulink.com / profe123
- **Estudiante**: estudiante@edulink.com / estudiante123

### Funcionalidades principales

1. **Sistema de usuarios**
   - Registro e inicio de sesión
   - Perfiles de usuario con roles específicos
   
2. **Gestión de contenido educativo**
   - Subida y descarga de materiales educativos
   - Organización por categorías
   
3. **Foros de discusión**
   - Creación de temas
   - Publicación de comentarios
   - Sistema de reportes
   
4. **Sistema de evaluaciones**
   - Creación de cuestionarios
   - Realización de evaluaciones
   - Visualización de resultados

## Estructura del proyecto

```
edulink/
│
├── assets/            # Recursos estáticos (CSS, JS, imágenes)
├── controllers/       # Controladores PHP
├── database/          # Scripts SQL
├── includes/          # Archivos de configuración y utilidades
├── models/            # Modelos de datos
├── uploads/           # Archivos subidos por los usuarios
└── views/             # Vistas/Plantillas HTML
```

## Notas adicionales

- Esta aplicación está diseñada para funcionar localmente con XAMPP
- No se requieren configuraciones adicionales ni herramientas externas
- Todos los datos se almacenan localmente en MySQL