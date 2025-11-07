# 🐱 GATO PINTO POS - Sistema Punto de Venta

Sistema POS completo, mobile-first, con diseño negro y naranja degradado.

## 🎨 CARACTERÍSTICAS

✅ Diseño moderno (Header negro + Menús naranja degradado)
✅ Mobile-first responsive
✅ Integración con WooCommerce (sincronización inteligente)
✅ Sistema de monedero (MiEspacio)
✅ Cupones y descuentos
✅ Reportes detallados por categoría
✅ Tickets personalizables con logo
✅ Escáner de códigos con cámara
✅ Multi-sesión
✅ Importación masiva desde Excel

## 📦 INSTALACIÓN

### 1. Subir archivos
Sube toda la carpeta a tu hosting

### 2. Crear bases de datos
- Base de datos del POS
- Conexión a MiEspacio (existente)
- Conexión a WordPress (existente)

### 3. Configurar
Edita `install.php` y `includes/config.php` con tus datos de MySQL

### 4. Instalar
Abre `tudominio.com/install.php` en tu navegador

### 5. Eliminar instalador
Borra `install.php` después de instalar

## 🔧 CONFIGURACIÓN

Todos los datos se editan en `includes/config.php`:

- Conexión POS
- Conexión MiEspacio
- Conexión WordPress
- URLs y rutas

## 📊 MÓDULOS

- **Vender**: Punto de venta principal
- **Productos**: Gestión de inventario
- **Categorías**: Organización de productos
- **Ventas**: Historial y detalles
- **Clientes**: Integrado con MiEspacio
- **Cupones**: Sistema de descuentos
- **Reportes**: Análisis detallado
- **Sincronización**: WooCommerce
- **Configuración**: Tickets, monedero, etc.

## 💰 SISTEMA DE MONEDERO

- Acumulación configurable por compra
- Uso en ventas
- Integrado con MiEspacio
- Historial de transacciones

## 🔄 SINCRONIZACIÓN WOOCOMMERCE

- Emparejamiento automático por SKU/Barcode
- Sincronización de stock POS → WP
- Mantiene descripciones e imágenes de WP
- Cola de sincronización con reintentos
- Dashboard de monitoreo

## 📱 PWA

El sistema es instalable como app en el celular

## 🆘 SOPORTE

Ver documentación completa en los archivos:
- INSTALACION.md
- MANUAL-USUARIO.md
- FAQ.md

