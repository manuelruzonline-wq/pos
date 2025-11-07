# 🚀 INSTALACIÓN RÁPIDA - 10 MINUTOS

## PASO 1: Subir Archivos (2 min)
1. Descarga el ZIP
2. Descomprime
3. Sube TODA la carpeta a tu hosting (vía FTP o cPanel)

## PASO 2: Configurar Datos (3 min)

### A. Edita `install.php` (líneas 12-25)
```php
define('POS_DB_HOST', 'localhost');
define('POS_DB_USER', 'tu_usuario');      // ← TUS DATOS
define('POS_DB_PASS', 'tu_contraseña');   // ← TUS DATOS
define('POS_DB_NAME', 'gato_pinto_pos');

// MiEspacio
define('MIESPACIO_DB_NAME', 'miespacio_db');  // ← TUS DATOS

// WordPress (opcional)
define('WP_DB_NAME', 'wordpress_db');         // ← TUS DATOS
```

### B. Edita `includes/config.php` (líneas 8-20)
Mismos datos que arriba

## PASO 3: Instalar (1 min)
1. Abre en navegador: `tudominio.com/install.php`
2. Si ves "✓ Instalación Exitosa" → ¡Listo!
3. **ANOTA** usuario y contraseña que aparecen

## PASO 4: Eliminar Instalador (10 seg)
```bash
rm install.php
```
O elimínalo desde cPanel/FTP

## PASO 5: Usar (3 min)
1. Ve a: `tudominio.com/`
2. Login:
   - Usuario: `admin`
   - Contraseña: `GatoPinto2024!`
3. ¡Listo!

---

## ✅ CHECKLIST

- [ ] Archivos subidos
- [ ] install.php editado
- [ ] config.php editado
- [ ] Navegador: tudominio.com/install.php
- [ ] ✓ Instalación exitosa
- [ ] install.php eliminado
- [ ] Login funcionando
- [ ] Contraseña cambiada

---

## 🆘 PROBLEMAS COMUNES

### "Error de conexión"
→ Verifica usuario/contraseña/nombre DB en config.php

### "Página en blanco"
→ Revisa PHP 7.4+ y error_log en cPanel

### "No puedo hacer login"
→ Usuario: `admin` / Pass: `GatoPinto2024!`

---

## 📞 SIGUIENTE PASO

Lee `DESARROLLO.md` para ver qué está completo y qué falta desarrollar.

