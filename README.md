# 📦 Sistema de Gestión de Inventarios - Cooperativa de Cafetaleros

Este sistema ha sido desarrollado como proyecto principal durante el periodo de pasantías en la **Sociedad Cooperativa de Cafetaleros Ciudad Barrios de R.L.** El objetivo es digitalizar y optimizar el control de insumos y productos dentro de la cooperativa.

---

## 🎓 Información del Proyecto
* **Desarrolladora:** Dalia Melissa Araujo Rivas
* **Institución:** Universidad de Oriente (UNIVO)
* **Carrera:** 4to año de Ingeniería en Desarrollo de Software
* **Programa:** Becaria de la Fundación Gloria de Kriete
* **Periodo:** Ciclo I - 2026 (280 horas de pasantía)

---

## 🚀 Tecnologías y Herramientas

El proyecto está construido bajo un stack moderno enfocado en el rendimiento y la mantenibilidad:

* **Backend:** [Laravel 11](https://laravel.com/) (Framework PHP con arquitectura MVC)
* **Frontend:** [Vue.js 3](https://vuejs.org/) & [Alpine.js](https://alpinejs.dev/)
* **Estilos:** [Tailwind CSS](https://tailwindcss.com/)
* **Plantilla Base:** [TailAdmin](https://tailadmin.com/)
* **Base de Datos:** MySQL



---

## 🛠️ Funcionalidades Implementadas

### 🔐 Autenticación y Seguridad
* **Registro de Usuarios:** Validación de datos en servidor y cifrado de contraseñas con Bcrypt.
* **Inicio de Sesión Seguro:** Manejo de sesiones y protección contra ataques CSRF.
* **Rutas Protegidas:** Implementación de Middleware para restringir el acceso al inventario solo a personal autorizado.
* **Cierre de Sesión:** Gestión segura de destrucción de tokens y sesiones.

### 📊 Dashboard e Interfaz
* **Interfaz Adaptativa:** Diseño totalmente responsivo para uso en tablets o PC.
* **Modo Oscuro:** Soporte nativo para reducir la fatiga visual.
* **Componentes Blade/Vue:** Modularización de la interfaz para facilitar futuras actualizaciones.

---

## 📂 Estructura del Módulo de Acceso

La lógica de autenticación se ha separado para mantener un código limpio (`Clean Code`):

-   **Controlador:** `app/Http/Controllers/Auth/AuthController.php`
-   **Rutas:** `routes/auth.php` (Incluida en `web.php` mediante `require`).
-   **Vistas:** `resources/views/pages/auth/` (Personalizadas con componentes de TailAdmin).



