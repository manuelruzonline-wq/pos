<?php
/**
 * GATO PINTO POS - Instalador de Base de Datos
 * Ejecuta este archivo UNA SOLA VEZ para crear todas las tablas
 */

// ============================================
// CONFIGURACIÓN - EDITA ESTOS DATOS
// ============================================

// Base de datos principal del POS
define('POS_DB_HOST', 'localhost');
define('POS_DB_USER', 'tu_usuario_mysql');
define('POS_DB_PASS', 'tu_contraseña_mysql');
define('POS_DB_NAME', 'gato_pinto_pos');

// Base de datos de MiEspacio (clientes)
define('MIESPACIO_DB_HOST', 'localhost');
define('MIESPACIO_DB_NAME', 'miespacio_db');
define('MIESPACIO_DB_USER', 'tu_usuario_mysql');
define('MIESPACIO_DB_PASS', 'tu_contraseña_mysql');

// Base de datos de WordPress (opcional)
define('WP_DB_HOST', 'localhost');
define('WP_DB_NAME', 'wordpress_db');
define('WP_DB_USER', 'tu_usuario_mysql');
define('WP_DB_PASS', 'tu_contraseña_mysql');

// Usuario administrador por defecto
$admin_user = 'admin';
$admin_pass = 'GatoPinto2024!';
$admin_email = 'admin@gatopinto.com';

// ============================================
// NO EDITES DEBAJO DE ESTA LÍNEA
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Conectar a MySQL
    $conn = new PDO("mysql:host=" . POS_DB_HOST, POS_DB_USER, POS_DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos del POS
    $conn->exec("CREATE DATABASE IF NOT EXISTS `" . POS_DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE `" . POS_DB_NAME . "`");
    
    echo "<h3>Creando tablas...</h3>";
    
    // ============================================
    // TABLA: users - Usuarios del sistema POS
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            name VARCHAR(100) NOT NULL,
            role ENUM('admin', 'cashier', 'manager') DEFAULT 'cashier',
            active TINYINT(1) DEFAULT 1,
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_active (active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla users creada<br>";
    
    // ============================================
    // TABLA: categories - Categorías de productos
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE,
            description TEXT,
            color VARCHAR(7) DEFAULT '#ff6b35',
            icon VARCHAR(100) DEFAULT 'fa-box',
            parent_id INT NULL,
            sort_order INT DEFAULT 0,
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
            INDEX idx_slug (slug),
            INDEX idx_active (active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla categories creada<br>";
    
    // ============================================
    // TABLA: products - Inventario de productos
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            
            -- Datos básicos
            sku VARCHAR(50),
            barcode VARCHAR(50),
            name VARCHAR(255) NOT NULL,
            description TEXT,
            short_description VARCHAR(500),
            
            -- Categorización
            category_id INT,
            
            -- Precios
            cost_price DECIMAL(10,2) DEFAULT 0.00,
            sale_price DECIMAL(10,2) NOT NULL,
            compare_price DECIMAL(10,2),
            
            -- Inventario
            stock INT DEFAULT 0,
            stock_min INT DEFAULT 5,
            track_stock TINYINT(1) DEFAULT 1,
            
            -- Integración WooCommerce
            wp_product_id BIGINT NULL,
            wp_variation_id BIGINT NULL,
            wp_sku VARCHAR(50),
            wp_price DECIMAL(10,2),
            wp_stock INT,
            wp_description TEXT,
            wp_image_url VARCHAR(500),
            sync_status ENUM('not_synced', 'synced', 'pending', 'error', 'review_needed') DEFAULT 'not_synced',
            last_synced DATETIME,
            
            -- Diferencias para revisión
            price_diff DECIMAL(10,2),
            stock_diff INT,
            needs_review TINYINT(1) DEFAULT 0,
            
            -- Imagen y multimedia
            image VARCHAR(255),
            images TEXT,
            
            -- Variantes y atributos
            has_variants TINYINT(1) DEFAULT 0,
            variant_type VARCHAR(50),
            variant_name VARCHAR(100),
            
            -- SEO y extras
            meta_title VARCHAR(255),
            meta_description TEXT,
            
            -- Estado
            active TINYINT(1) DEFAULT 1,
            featured TINYINT(1) DEFAULT 0,
            
            -- Fechas
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Índices
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            UNIQUE KEY unique_sku (sku),
            UNIQUE KEY unique_barcode (barcode),
            INDEX idx_wp_product (wp_product_id),
            INDEX idx_name (name),
            INDEX idx_active (active),
            INDEX idx_sync_status (sync_status),
            INDEX idx_needs_review (needs_review),
            FULLTEXT idx_search (name, description)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla products creada<br>";
    
    // ============================================
    // TABLA: sales - Registro de ventas
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS sales (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_number VARCHAR(20) UNIQUE NOT NULL,
            
            -- Usuario que realizó la venta
            user_id INT NOT NULL,
            
            -- Cliente (opcional, de MiEspacio)
            customer_id INT NULL,
            customer_name VARCHAR(100),
            customer_email VARCHAR(100),
            customer_phone VARCHAR(20),
            
            -- Montos
            subtotal DECIMAL(10,2) NOT NULL,
            discount DECIMAL(10,2) DEFAULT 0.00,
            coupon_id INT NULL,
            tax DECIMAL(10,2) DEFAULT 0.00,
            wallet_used DECIMAL(10,2) DEFAULT 0.00,
            total DECIMAL(10,2) NOT NULL,
            
            -- Pago
            payment_method ENUM('cash', 'card', 'transfer', 'wallet', 'mixed') DEFAULT 'cash',
            payment_status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'completed',
            
            -- Cashback/Monedero
            wallet_earned DECIMAL(10,2) DEFAULT 0.00,
            
            -- Notas y extras
            notes TEXT,
            internal_notes TEXT,
            
            -- Ticket
            ticket_printed TINYINT(1) DEFAULT 0,
            ticket_sent TINYINT(1) DEFAULT 0,
            
            -- Fechas
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME,
            
            -- Índices
            FOREIGN KEY (user_id) REFERENCES users(id),
            INDEX idx_sale_number (sale_number),
            INDEX idx_customer (customer_id),
            INDEX idx_date (created_at),
            INDEX idx_user (user_id),
            INDEX idx_payment_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla sales creada<br>";
    
    // ============================================
    // TABLA: sale_items - Items de cada venta
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS sale_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sale_id INT NOT NULL,
            product_id INT NOT NULL,
            
            -- Datos del producto al momento de la venta
            product_name VARCHAR(255) NOT NULL,
            product_sku VARCHAR(50),
            
            -- Cantidades y precios
            quantity INT NOT NULL,
            unit_price DECIMAL(10,2) NOT NULL,
            cost_price DECIMAL(10,2) DEFAULT 0.00,
            discount DECIMAL(10,2) DEFAULT 0.00,
            subtotal DECIMAL(10,2) NOT NULL,
            
            -- Variante (si aplica)
            variant_name VARCHAR(100),
            
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id),
            INDEX idx_sale (sale_id),
            INDEX idx_product (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla sale_items creada<br>";
    
    // ============================================
    // TABLA: coupons - Sistema de cupones/descuentos
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS coupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            code VARCHAR(50) UNIQUE NOT NULL,
            description VARCHAR(255),
            
            -- Tipo de descuento
            discount_type ENUM('percentage', 'fixed', 'free_shipping') DEFAULT 'percentage',
            discount_value DECIMAL(10,2) NOT NULL,
            
            -- Aplicación
            applies_to ENUM('all', 'categories', 'products') DEFAULT 'all',
            applies_to_ids TEXT,
            
            -- Restricciones
            min_purchase DECIMAL(10,2) DEFAULT 0.00,
            max_discount DECIMAL(10,2),
            usage_limit INT,
            usage_limit_per_customer INT DEFAULT 1,
            times_used INT DEFAULT 0,
            
            -- Fechas
            valid_from DATE,
            valid_until DATE,
            
            -- Estado
            active TINYINT(1) DEFAULT 1,
            
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_code (code),
            INDEX idx_active (active),
            INDEX idx_dates (valid_from, valid_until)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla coupons creada<br>";
    
    // ============================================
    // TABLA: coupon_usage - Uso de cupones
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS coupon_usage (
            id INT AUTO_INCREMENT PRIMARY KEY,
            coupon_id INT NOT NULL,
            sale_id INT NOT NULL,
            customer_id INT,
            discount_amount DECIMAL(10,2) NOT NULL,
            used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
            INDEX idx_coupon (coupon_id),
            INDEX idx_customer (customer_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla coupon_usage creada<br>";
    
    // ============================================
    // TABLA: wallet_transactions - Transacciones de monedero
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS wallet_transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            
            -- Tipo de transacción
            type ENUM('credit', 'debit', 'adjustment', 'refund') NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            balance_before DECIMAL(10,2) NOT NULL,
            balance_after DECIMAL(10,2) NOT NULL,
            
            -- Referencia
            sale_id INT NULL,
            reference VARCHAR(100),
            description TEXT,
            
            -- Usuario que realizó la transacción
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_customer (customer_id),
            INDEX idx_type (type),
            INDEX idx_date (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla wallet_transactions creada<br>";
    
    // ============================================
    // TABLA: sync_queue - Cola de sincronización WooCommerce
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS sync_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            
            -- Acción a realizar
            action ENUM('update_stock', 'update_price', 'create_product', 'update_all') NOT NULL,
            
            -- Datos
            old_value TEXT,
            new_value TEXT,
            
            -- Estado
            status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
            attempts INT DEFAULT 0,
            max_attempts INT DEFAULT 3,
            
            -- Error
            error_message TEXT,
            last_error TEXT,
            
            -- Fechas
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_attempt DATETIME,
            completed_at DATETIME,
            
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            INDEX idx_status (status),
            INDEX idx_product (product_id),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla sync_queue creada<br>";
    
    // ============================================
    // TABLA: sync_decisions - Decisiones de sincronización
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS sync_decisions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            
            -- Tipo de decisión
            decision_type ENUM('price', 'stock', 'description', 'create_in_wp', 'match_product') NOT NULL,
            
            -- Acción tomada
            action_taken ENUM('pos_to_wp', 'wp_to_pos', 'keep_separate', 'skip', 'manual_review') NOT NULL,
            
            -- Valores
            old_value TEXT,
            new_value TEXT,
            
            -- Metadata
            notes TEXT,
            decided_by INT,
            decided_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (decided_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_product (product_id),
            INDEX idx_type (decision_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla sync_decisions creada<br>";
    
    // ============================================
    // TABLA: settings - Configuración del sistema
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type ENUM('string', 'number', 'boolean', 'json', 'text') DEFAULT 'string',
            category VARCHAR(50) DEFAULT 'general',
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_key (setting_key),
            INDEX idx_category (category)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla settings creada<br>";
    
    // ============================================
    // TABLA: activity_log - Registro de actividades
    // ============================================
    $conn->exec("
        CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            entity_type VARCHAR(50),
            entity_id INT,
            description TEXT,
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_user (user_id),
            INDEX idx_action (action),
            INDEX idx_date (created_at),
            INDEX idx_entity (entity_type, entity_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "✓ Tabla activity_log creada<br>";
    
    // ============================================
    // INSERTAR USUARIO ADMINISTRADOR
    // ============================================
    $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("
        INSERT INTO users (username, password, email, name, role) 
        VALUES (?, ?, ?, 'Administrador', 'admin')
    ");
    $stmt->execute([$admin_user, $hashed_password, $admin_email]);
    echo "✓ Usuario administrador creado<br>";
    
    // ============================================
    // INSERTAR CATEGORÍAS POR DEFECTO
    // ============================================
    $categories = [
        ['Arte', 'arte', 'Productos de arte y manualidades', '#FF6B35', 'fa-palette'],
        ['Papelería', 'papeleria', 'Artículos de papelería', '#F7931E', 'fa-pen'],
        ['Oficina', 'oficina', 'Suministros de oficina', '#FF8C42', 'fa-briefcase'],
        ['Escolar', 'escolar', 'Material escolar', '#FFB84D', 'fa-book'],
        ['Manualidades', 'manualidades', 'Materiales para manualidades', '#FF6B35', 'fa-scissors'],
        ['Otros', 'otros', 'Otros productos', '#95A5A6', 'fa-box']
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO categories (name, slug, description, color, icon) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "✓ Categorías por defecto creadas<br>";
    
    // ============================================
    // INSERTAR CONFIGURACIONES POR DEFECTO
    // ============================================
    $settings = [
        // Negocio
        ['business_name', 'Gato Pinto', 'string', 'business', 'Nombre del negocio'],
        ['business_phone', '', 'string', 'business', 'Teléfono del negocio'],
        ['business_email', 'info@gatopinto.com', 'string', 'business', 'Email del negocio'],
        ['business_address', '', 'text', 'business', 'Dirección del negocio'],
        ['business_logo', 'https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png', 'string', 'business', 'URL del logo'],
        
        // Moneda
        ['currency', 'MXN', 'string', 'general', 'Moneda'],
        ['currency_symbol', '$', 'string', 'general', 'Símbolo de moneda'],
        ['currency_position', 'before', 'string', 'general', 'Posición del símbolo'],
        
        // Impuestos
        ['tax_enabled', '0', 'boolean', 'general', 'Activar impuestos'],
        ['tax_rate', '0', 'number', 'general', 'Tasa de impuesto (%)'],
        ['tax_included', '0', 'boolean', 'general', 'Impuesto incluido en precio'],
        
        // Inventario
        ['low_stock_alert', '1', 'boolean', 'inventory', 'Alertas de stock bajo'],
        ['low_stock_threshold', '5', 'number', 'inventory', 'Umbral de stock bajo'],
        
        // Tickets
        ['ticket_header', 'Gracias por su compra', 'text', 'ticket', 'Encabezado del ticket'],
        ['ticket_footer', 'Vuelva pronto', 'text', 'ticket', 'Pie del ticket'],
        ['ticket_width', '80', 'number', 'ticket', 'Ancho del ticket (mm)'],
        ['ticket_show_logo', '1', 'boolean', 'ticket', 'Mostrar logo en ticket'],
        
        // Monedero
        ['wallet_enabled', '1', 'boolean', 'wallet', 'Activar sistema de monedero'],
        ['wallet_earn_percent', '5', 'number', 'wallet', '% de cashback por compra'],
        ['wallet_max_use_percent', '50', 'number', 'wallet', '% máximo de uso por compra'],
        ['wallet_min_purchase', '100', 'number', 'wallet', 'Compra mínima para acumular'],
        ['wallet_expiry_days', '365', 'number', 'wallet', 'Días de expiración del saldo'],
        
        // WooCommerce
        ['wc_sync_enabled', '1', 'boolean', 'woocommerce', 'Activar sincronización WC'],
        ['wc_sync_mode', 'realtime', 'string', 'woocommerce', 'Modo de sincronización'],
        ['wc_sync_stock', '1', 'boolean', 'woocommerce', 'Sincronizar stock'],
        ['wc_sync_price', '1', 'boolean', 'woocommerce', 'Sincronizar precios'],
        
        // MiEspacio
        ['miespacio_enabled', '1', 'boolean', 'miespacio', 'Conectar con MiEspacio'],
        ['miespacio_table', 'usuarios', 'string', 'miespacio', 'Nombre de tabla de clientes']
    ];
    
    $stmt = $conn->prepare("
        INSERT INTO settings (setting_key, setting_value, setting_type, category, description) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }
    echo "✓ Configuraciones por defecto creadas<br>";
    
    // ============================================
    // HTML DE ÉXITO
    // ============================================
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Instalación Exitosa - Gato Pinto POS</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                border-radius: 20px;
                padding: 40px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 20px 60px rgba(255, 107, 53, 0.3);
            }
            .logo {
                text-align: center;
                margin-bottom: 30px;
            }
            .logo img {
                max-width: 200px;
                height: auto;
            }
            .success-icon {
                text-align: center;
                font-size: 80px;
                margin-bottom: 20px;
                color: #10b981;
            }
            h1 {
                color: #FF6B35;
                margin-bottom: 20px;
                font-size: 28px;
                text-align: center;
            }
            .info {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 20px;
                margin: 20px 0;
            }
            .info-item {
                margin: 15px 0;
                padding: 15px;
                background: white;
                border-radius: 8px;
                border-left: 4px solid #FF6B35;
            }
            .label {
                font-weight: 700;
                color: #FF6B35;
                display: block;
                margin-bottom: 5px;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .value {
                color: #1a1a1a;
                font-family: 'Courier New', monospace;
                font-size: 16px;
                font-weight: 600;
            }
            .warning {
                background: #fff3cd;
                border: 2px solid #FF6B35;
                border-radius: 10px;
                padding: 20px;
                margin: 20px 0;
                color: #856404;
            }
            .warning strong {
                color: #FF6B35;
            }
            .btn {
                display: inline-block;
                background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
                color: white;
                padding: 15px 40px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: bold;
                margin-top: 20px;
                transition: transform 0.3s;
                text-align: center;
                display: block;
            }
            .btn:hover {
                transform: translateY(-2px);
            }
            .checklist {
                background: #f8f9fa;
                border-radius: 10px;
                padding: 20px;
                margin: 20px 0;
            }
            .checklist h3 {
                color: #1a1a1a;
                margin-bottom: 15px;
                font-size: 18px;
            }
            .checklist ul {
                list-style: none;
                padding: 0;
            }
            .checklist li {
                padding: 10px 0;
                color: #555;
                padding-left: 30px;
                position: relative;
            }
            .checklist li:before {
                content: '☐';
                position: absolute;
                left: 0;
                color: #FF6B35;
                font-size: 20px;
            }
            .delete-msg {
                color: #e74c3c;
                font-weight: bold;
                margin-top: 20px;
                font-size: 14px;
                text-align: center;
                padding: 15px;
                background: #fee;
                border-radius: 10px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='logo'>
                <img src='https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png' alt='Gato Pinto'>
            </div>
            
            <div class='success-icon'>✓</div>
            <h1>¡Instalación Exitosa!</h1>
            <p style='text-align: center; color: #666; margin-bottom: 30px;'>
                Tu sistema POS de Gato Pinto ha sido instalado correctamente
            </p>
            
            <div class='info'>
                <div class='info-item'>
                    <span class='label'>Usuario</span>
                    <span class='value'>$admin_user</span>
                </div>
                <div class='info-item'>
                    <span class='label'>Contraseña</span>
                    <span class='value'>$admin_pass</span>
                </div>
                <div class='info-item'>
                    <span class='label'>Email</span>
                    <span class='value'>$admin_email</span>
                </div>
            </div>
            
            <div class='warning'>
                <strong>⚠️ IMPORTANTE:</strong>
                <ul style='margin-top: 10px; padding-left: 20px;'>
                    <li>Guarda estas credenciales en un lugar seguro</li>
                    <li>Cambia la contraseña después del primer login</li>
                    <li>ELIMINA el archivo <code>install.php</code> por seguridad</li>
                </ul>
            </div>
            
            <div class='checklist'>
                <h3>✅ Próximos Pasos:</h3>
                <ul>
                    <li>Edita <code>includes/config.php</code> con tus datos</li>
                    <li>Configura la conexión a MiEspacio</li>
                    <li>Configura la conexión a WordPress (opcional)</li>
                    <li>Importa tu inventario desde Excel</li>
                    <li>Configura los datos de tu negocio</li>
                    <li>¡Empieza a vender!</li>
                </ul>
            </div>
            
            <a href='index.php' class='btn'>Ir al Sistema POS →</a>
            
            <p class='delete-msg'>
                🗑️ Recuerda: ELIMINA el archivo install.php después de este paso
            </p>
        </div>
    </body>
    </html>
    ";
    
} catch(PDOException $e) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Error de Instalación</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: #fee;
                padding: 20px;
                color: #721c24;
            }
            .error-container {
                background: white;
                border: 2px solid #f5c6cb;
                border-radius: 10px;
                padding: 30px;
                max-width: 600px;
                margin: 50px auto;
            }
            h1 { color: #e74c3c; }
            code {
                background: #f8f9fa;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
            }
            pre {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                overflow-x: auto;
            }
        </style>
    </head>
    <body>
        <div class='error-container'>
            <h1>❌ Error de Instalación</h1>
            <p><strong>Error:</strong> " . $e->getMessage() . "</p>
            <pre>" . $e->getTraceAsString() . "</pre>
            <hr>
            <h3>Verifica lo siguiente:</h3>
            <ul>
                <li>Los datos de conexión en <code>install.php</code> son correctos</li>
                <li>El usuario de MySQL tiene permisos para crear bases de datos</li>
                <li>El servidor MySQL está funcionando</li>
                <li>No hay conflictos con bases de datos existentes</li>
            </ul>
        </div>
    </body>
    </html>
    ";
}
?>
