<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="Sistema POS Gato Pinto - Punto de Venta Profesional">
    
    <title>Gato Pinto POS</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/views.css">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png">
</head>
<body>
    
    <!-- ============================================
         PANTALLA DE LOGIN
         ============================================ -->
    <div id="loginScreen" class="screen active">
        <div class="login-container fade-in">
            <div class="login-logo">
                <img src="https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png" alt="Gato Pinto">
            </div>
            
            <div class="login-header">
                <h1>Gato Pinto POS</h1>
                <p>Inicia sesión para continuar</p>
            </div>
            
            <form id="loginForm" class="login-form">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Usuario
                    </label>
                    <div class="input-group">
                        <i class="fas fa-user input-group-icon"></i>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-control" 
                            placeholder="Ingresa tu usuario"
                            required 
                            autofocus 
                            autocomplete="username">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Contraseña
                    </label>
                    <div class="input-group">
                        <i class="fas fa-lock input-group-icon"></i>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Ingresa tu contraseña"
                            required 
                            autocomplete="current-password">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <span class="btn-text">Iniciar Sesión</span>
                    <span class="btn-loader hidden">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
                
                <div id="loginError" class="alert alert-danger hidden"></div>
            </form>
        </div>
    </div>
    
    <!-- ============================================
         APLICACIÓN PRINCIPAL
         ============================================ -->
    <div id="appScreen" class="screen">
        
        <!-- Header -->
        <header class="app-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="header-logo">
                    <img src="https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png" alt="Gato Pinto">
                </div>
                
                <span class="header-title" id="headerTitle">Vender</span>
            </div>
            
            <div class="header-right">
                <div class="user-info">
                    <div class="user-name" id="userName">Usuario</div>
                    <div class="user-role" id="userRole">Admin</div>
                </div>
                
                <button class="logout-btn" id="logoutBtn" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </header>
        
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="https://gatopinto.com/wp-content/uploads/2024/09/LOGO-GATO-PINTO-OFICIAL-TRANSPARENTE-e1725148988113.png" alt="Gato Pinto">
                    <span class="sidebar-brand">Gato Pinto</span>
                </div>
                <button class="close-sidebar" id="closeSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <ul class="sidebar-menu">
                <li class="menu-section">Principal</li>
                <li>
                    <a href="#" data-view="sell" class="menu-item active">
                        <i class="fas fa-cash-register"></i>
                        <span>Vender</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-view="sales" class="menu-item">
                        <i class="fas fa-receipt"></i>
                        <span>Historial de Ventas</span>
                    </a>
                </li>
                
                <li class="menu-section">Inventario</li>
                <li>
                    <a href="#" data-view="products" class="menu-item">
                        <i class="fas fa-box"></i>
                        <span>Productos</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-view="categories" class="menu-item">
                        <i class="fas fa-tags"></i>
                        <span>Categorías</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-view="stock" class="menu-item">
                        <i class="fas fa-warehouse"></i>
                        <span>Control de Stock</span>
                    </a>
                </li>
                
                <li class="menu-section">Clientes</li>
                <li>
                    <a href="#" data-view="customers" class="menu-item">
                        <i class="fas fa-users"></i>
                        <span>Clientes</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-view="coupons" class="menu-item">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Cupones</span>
                    </a>
                </li>
                
                <li class="menu-section">Reportes</li>
                <li>
                    <a href="#" data-view="reports" class="menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Reportes</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-view="analytics" class="menu-item">
                        <i class="fas fa-chart-pie"></i>
                        <span>Análisis</span>
                    </a>
                </li>
                
                <li class="menu-section admin-only">Administración</li>
                <li class="admin-only">
                    <a href="#" data-view="sync" class="menu-item">
                        <i class="fas fa-sync-alt"></i>
                        <span>Sincronización</span>
                    </a>
                </li>
                <li class="admin-only">
                    <a href="#" data-view="settings" class="menu-item">
                        <i class="fas fa-cog"></i>
                        <span>Configuración</span>
                    </a>
                </li>
                <li class="admin-only">
                    <a href="#" data-view="users" class="menu-item">
                        <i class="fas fa-user-shield"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Overlay del Sidebar -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Contenido Principal -->
        <main class="app-content" id="appContent">
            <!-- Las vistas se cargarán aquí dinámicamente -->
        </main>
        
    </div>
    
    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>
    
    <!-- Modal Container -->
    <div id="modalContainer"></div>
    
    <!-- Scripts -->
    <script src="js/app.js"></script>
    <script src="js/api.js"></script>
    <script src="js/views.js"></script>
    <script src="js/components.js"></script>
    <script src="js/utils.js"></script>
    
    <!-- Service Worker para PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Service Worker registrado'))
                .catch(err => console.log('Error al registrar Service Worker', err));
        }
    </script>
</body>
</html>
