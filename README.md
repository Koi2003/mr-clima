# Mr. Clima — Plataforma Web y Gestión de Servicios Técnicos

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-005C84?style=flat-square&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![Vite](https://img.shields.io/badge/Vite-Bundler-646CFF?style=flat-square&logo=vite&logoColor=white)

Plataforma web integral diseñada a medida para la digitalización operativa y comercial de **Mr. Clima**, empresa especializada en climatización, instalación y mantenimiento técnico.

El sistema unifica en una sola solución la venta de equipos, la contratación de servicios y la gestión interna de citas y partes de trabajo para los operarios.

## Funcionalidades Principales

- **Catálogo comercial y contratación:** Exposición estructurada de equipos de climatización, especificaciones técnicas y solicitud de presupuestos directos.
- **Sistema de reservas y citas online:** Módulo para que los clientes soliciten visitas de mantenimiento, revisión e instalación con selección de fechas y franjas horarias.
- **Panel de administración técnica:** Gestión centralizada de avisos, asignación de partes de trabajo a técnicos en ruta y control de estados de intervención.
- **Autenticación y control de accesos:** Separación de roles (administración, operarios técnicos y clientes) mediante middleware y Laravel Sanctum.
- **Base de datos relacional:** Modelado en MySQL normalizado con migraciones e indexación para optimizar consultas de inventario y partes.

## Stack Tecnológico

- **Backend:** PHP 8.2+ con framework Laravel 12 (Arquitectura MVC, Eloquent ORM, Migrations, Seeders).
- **Frontend:** Blade Templates, JavaScript (ES6+), CSS modular compilado con Vite.
- **Base de Datos:** MySQL / MariaDB.
- **Control de versiones:** Git y GitHub.

## Requisitos de Entorno

- PHP >= 8.2 (extensiones requeridas: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`).
- Composer >= 2.x
- Node.js >= 18.x y npm
- Servidor de base de datos MySQL / MariaDB

## Instalación en Local

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/Koi2003/mr-clima.git
   cd mr-clima
   ```

2. Instalar dependencias de PHP:
   ```bash
   composer install
   ```

3. Instalar y compilar dependencias de frontend:
   ```bash
   npm install
   npm run build
   ```

4. Configurar variables de entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Configurar los parámetros de conexión a MySQL en `.env` y ejecutar migraciones:
   ```bash
   php artisan migrate
   ```

6. Iniciar el servidor de desarrollo:
   ```bash
   php artisan serve
   ```

## Autor

- **Sergio Bailén Sampere** — *Desarrollador Full-Stack*
- **LinkedIn:** [linkedin.com/in/sergio-bailen-sampere/](https://www.linkedin.com/in/sergio-bailen-sampere/)
- **GitHub:** [@Koi2003](https://github.com/Koi2003)
