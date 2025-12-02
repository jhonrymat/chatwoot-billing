# 🚀 Chatwoot Billing System

Sistema de facturación y gestión de suscripciones para Chatwoot autohospedado con arquitectura multi-gateway de pagos.

**Iniciado por:** [Jhon Matoma](https://github.com/jhonrymata) - Ingeniero de Software, Colombia 🇨🇴

## 🌟 Características

- ✅ Gestión completa de planes de suscripción
- ✅ Arquitectura multi-gateway de pagos (preparada para múltiples proveedores)
- ✅ Integración con MercadoPago Colombia
- ✅ Base de datos preparada para Stripe y otros gateways
- ✅ Creación automática de cuentas en Chatwoot
- ✅ Dashboard de métricas para suscriptores
- ✅ Panel administrativo completo con Filament 4
- ✅ Gestión de métodos de pago
- ✅ Sistema de roles (Admin y Suscriptor)
- ✅ Credenciales unificadas Laravel + Chatwoot

## 🎯 Gateways de Pago

El sistema está diseñado con una arquitectura flexible que permite agregar múltiples proveedores de pago:

### ✅ Actualmente Soportado
- **MercadoPago** (Colombia) - Completamente integrado

### 🔜 Preparado para Integración
- **Stripe** - Base de datos configurada
- Otros gateways pueden ser agregados fácilmente

La arquitectura modular permite extender el sistema con nuevos proveedores de pago sin modificar el código core.

## 📋 Requisitos

- PHP 8.2+
- MySQL 8.0+
- Composer 2.x
- Laravel 12
- Chatwoot autohospedado con acceso API
- Cuenta de MercadoPago Colombia (u otro gateway soportado)

## 🔧 Instalación

1. Clonar el repositorio
```bash
git clone https://github.com/jhonmatoma/chatwoot-billing-system.git
cd chatwoot-billing-system
```

2. Copiar el archivo de configuración
```bash
cp .env.example .env
```

3. Configurar las variables de entorno:
   - Base de datos
   - Chatwoot URL y API Key
   - Credenciales del gateway de pago seleccionado

4. Instalar dependencias
```bash
composer install
```

5. Generar application key
```bash
php artisan key:generate
```

6. Migrar base de datos con seeders
```bash
php artisan migrate --seed
```

7. Crear usuario administrador
```bash
php artisan make:filament-user
```

## ⚙️ Configuración

### Chatwoot

Obtén un API Key de super administrador desde tu instalación de Chatwoot:

1. Ingresa a Chatwoot como super admin
2. Ve a Configuración > Integraciones > API
3. Genera un nuevo token
4. Copia el token en la variable `CHATWOOT_API_KEY` del archivo `.env`

### MercadoPago

1. Crea una aplicación en https://www.mercadopago.com.co/developers
2. Obtén tus credenciales de prueba/producción
3. Configura las credenciales en tu archivo `.env`
4. Configura el webhook en MercadoPago apuntando a:
```
https://tudominio.com/webhook/mercadopago
```

### Stripe (Próximamente)

La base de datos ya está preparada para soportar Stripe. La integración completa estará disponible en futuras versiones.

## 🏗️ Arquitectura

El sistema utiliza una arquitectura de gateway abstracta que permite:

- Agregar nuevos proveedores de pago sin modificar el código existente
- Soporte para múltiples métodos de pago por usuario
- Gestión unificada de suscripciones independiente del gateway
- Webhooks estandarizados para cada proveedor

## 📚 Documentación

Documentación completa en desarrollo. Por ahora, el código está bien comentado y sigue las convenciones de Laravel.

## 🤝 Contribuciones

Este es un proyecto de código abierto. Las contribuciones son bienvenidas:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 🐛 Reportar Issues

Si encuentras algún bug o tienes una sugerencia, por favor abre un issue en GitHub.

## 👨‍💻 Autor

**Jhon Matoma**
- Ingeniero de Software
- Colombia 🇨🇴
- GitHub: [@jhonrymat](https://github.com/jhonrymat)

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

⭐ Si este proyecto te resulta útil, considera darle una estrella en GitHub
