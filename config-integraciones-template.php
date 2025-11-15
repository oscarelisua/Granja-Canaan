<?php
/**
 * Configuración de Integraciones para Granja de Rehabilitación Cristiana
 * 
 * Este archivo contiene todas las configuraciones necesarias para:
 * - Instagram Feed API
 * - Google Maps Integration
 * - Formularios seguros (Formspree/Jotform)
 * - WhatsApp Business API
 * - Google Analytics 4
 * - Email automático y notificaciones
 * - Telegram Bot para alertas urgentes
 * 
 * IMPORTANTE: Reemplaza todos los valores de ejemplo con tus credenciales reales
 * 
 * @version 1.0
 * @author Granja Esperanza Dev Team
 */

// Prevenir acceso directo al archivo
if (!defined('ABSPATH')) {
    exit('Acceso directo no permitido');
}

/* ===================================
   CONFIGURACIÓN GENERAL
   =================================== */

// Información básica del sitio
define('GRANJA_SITE_NAME', 'Granja de Rehabilitación Cristiana Esperanza');
define('GRANJA_SITE_URL', 'https://granjaesperanza.org');
define('GRANJA_CONTACT_EMAIL', 'info@granjaesperanza.org');
define('GRANJA_EMERGENCY_PHONE', '+54341XXXXXXX');
define('GRANJA_WHATSAPP_NUMBER', '5493412345678'); // Sin espacios ni caracteres especiales

// Ubicación de la granja
define('GRANJA_ADDRESS', 'Ruta Provincial XX, Km XX, Localidad, Santa Fe, Argentina');
define('GRANJA_LATITUDE', '-32.9442'); // Reemplazar con coordenadas reales
define('GRANJA_LONGITUDE', '-60.6505'); // Reemplazar con coordenadas reales

/* ===================================
   INSTAGRAM BASIC DISPLAY API
   =================================== */

// Para obtener estas credenciales:
// 1. Ve a developers.facebook.com
// 2. Crea una nueva app
// 3. Agrega "Instagram Basic Display"
// 4. Configura redirect URI y obtén access token

$granja_instagram_config = array(
    'access_token' => 'IGQVJ...', // Tu Long-Lived Access Token aquí
    'user_id' => '17841405309213570', // Tu Instagram User ID
    'app_id' => '1234567890123456', // Tu App ID de Facebook
    'app_secret' => 'abc123def456...', // Tu App Secret
    'redirect_uri' => GRANJA_SITE_URL . '/auth/instagram-callback',
    'fields' => 'id,media_type,media_url,thumbnail_url,permalink,caption,timestamp',
    'limit' => 6, // Número de posts a mostrar
    'cache_duration' => 3600, // 1 hora en segundos
);

/* ===================================
   GOOGLE MAPS API
   =================================== */

// Para obtener API Key:
// 1. Ve a console.cloud.google.com
// 2. Crea nuevo proyecto
// 3. Habilita "Maps JavaScript API"
// 4. Genera API Key con restricciones

$granja_google_maps_config = array(
    'api_key' => 'AIzaSyC...', // Tu Google Maps API Key aquí
    'map_id' => 'granjaesperanza_map', // ID único para tu mapa
    'center' => array(
        'lat' => floatval(GRANJA_LATITUDE),
        'lng' => floatval(GRANJA_LONGITUDE)
    ),
    'zoom' => 15,
    'marker_title' => GRANJA_SITE_NAME,
    'marker_info' => 'Centro de rehabilitación cristiana especializado en tratamiento de adicciones',
    'styles' => array(
        // Estilo personalizado del mapa (opcional)
        array(
            'featureType' => 'all',
            'elementType' => 'geometry.fill',
            'stylers' => array(
                array('weight' => '2.00')
            )
        ),
        array(
            'featureType' => 'water',
            'elementType' => 'geometry',
            'stylers' => array(
                array('color' => '#e9e9e9'),
                array('lightness' => 17)
            )
        )
    )
);

/* ===================================
   CONFIGURACIÓN DE FORMULARIOS
   =================================== */

// Formspree Configuration (Recomendado para sitios estáticos)
$granja_formspree_config = array(
    'endpoint' => 'https://formspree.io/f/xvgpkwql', // Tu endpoint de Formspree
    'api_key' => 'fs-api-...', // Tu API Key de Formspree (plan pro)
    'success_url' => GRANJA_SITE_URL . '/gracias',
    'error_url' => GRANJA_SITE_URL . '/error',
    'spam_protection' => true,
    'honeypot_field' => '_gotcha', // Campo oculto para detectar spam
);

// Jotform Configuration (Alternativa profesional HIPAA-compliant)
$granja_jotform_config = array(
    'form_id' => '221234567890123', // Tu Form ID de Jotform
    'api_key' => 'abc123def456...', // Tu API Key de Jotform
    'enterprise_plan' => true, // true si tienes plan HIPAA
    'webhook_url' => GRANJA_SITE_URL . '/webhook/jotform',
    'encryption' => true, // Encriptación de datos sensibles
);

/* ===================================
   WHATSAPP BUSINESS API
   =================================== */

// Para WhatsApp Business API avanzado (opcional)
$granja_whatsapp_config = array(
    'phone_number' => GRANJA_WHATSAPP_NUMBER,
    'business_account_id' => '1234567890123456', // Si usas WhatsApp Business API
    'access_token' => 'EAAbc123...', // Token para WhatsApp Business API
    'webhook_verify_token' => 'granja_webhook_2024', // Token de verificación
    'welcome_message' => '¡Hola! Gracias por contactarnos. ¿En qué podemos ayudarte?',
    'crisis_keywords' => array('urgente', 'crisis', 'emergencia', 'suicidio', 'sobredosis'),
    'auto_response' => true,
);

// URLs de WhatsApp simples (más fácil de implementar)
$granja_whatsapp_links = array(
    'general' => 'https://wa.me/' . GRANJA_WHATSAPP_NUMBER . '?text=Hola%2C%20necesito%20información%20sobre%20el%20programa%20de%20rehabilitación',
    'emergency' => 'https://wa.me/' . GRANJA_WHATSAPP_NUMBER . '?text=🚨%20EMERGENCIA%20-%20Necesito%20ayuda%20inmediata',
    'family' => 'https://wa.me/' . GRANJA_WHATSAPP_NUMBER . '?text=Hola%2C%20soy%20familiar%20de%20alguien%20que%20necesita%20ayuda',
    'follow_up' => 'https://wa.me/' . GRANJA_WHATSAPP_NUMBER . '?text=Hola%2C%20necesito%20seguimiento%20post-tratamiento',
);

/* ===================================
   GOOGLE ANALYTICS 4
   =================================== */

$granja_analytics_config = array(
    'measurement_id' => 'G-XXXXXXXXXX', // Tu Measurement ID de GA4
    'api_secret' => 'abc123def456...', // Para Measurement Protocol API (opcional)
    'stream_id' => '1234567890', // Data Stream ID
    'events' => array(
        'phone_call' => array(
            'event_name' => 'phone_call',
            'parameters' => array(
                'event_category' => 'contact',
                'event_label' => 'emergency_hotline',
                'value' => 10
            )
        ),
        'form_submit' => array(
            'event_name' => 'form_submit',
            'parameters' => array(
                'event_category' => 'contact',
                'event_label' => 'contact_form',
                'value' => 5
            )
        ),
        'whatsapp_click' => array(
            'event_name' => 'whatsapp_click',
            'parameters' => array(
                'event_category' => 'contact',
                'event_label' => 'whatsapp_button',
                'value' => 3
            )
        )
    ),
    'enhanced_ecommerce' => false, // No aplicable para esta web
    'demographics' => true,
    'remarketing' => false, // Por privacidad
);

/* ===================================
   EMAIL Y NOTIFICACIONES
   =================================== */

// SMTP Configuration (para emails desde el sitio)
$granja_smtp_config = array(
    'host' => 'smtp.gmail.com', // o tu proveedor de email
    'port' => 587,
    'security' => 'tls', // o 'ssl'
    'username' => 'noreply@granjaesperanza.org',
    'password' => 'tu_password_aqui', // Usar App Password si es Gmail
    'from_name' => GRANJA_SITE_NAME,
    'from_email' => 'noreply@granjaesperanza.org',
);

// SendGrid Configuration (Alternativa profesional)
$granja_sendgrid_config = array(
    'api_key' => 'SG.abc123...', // Tu API Key de SendGrid
    'from_email' => 'noreply@granjaesperanza.org',
    'from_name' => GRANJA_SITE_NAME,
    'templates' => array(
        'welcome' => 'd-1234567890abcdef',
        'emergency_response' => 'd-abcdef1234567890',
        'follow_up' => 'd-567890abcdef1234',
    ),
    'lists' => array(
        'newsletter' => 'abc123...',
        'families' => 'def456...',
        'graduates' => 'ghi789...',
    )
);

/* ===================================
   TELEGRAM BOT (Para alertas internas)
   =================================== */

// Para crear bot:
// 1. Habla con @BotFather en Telegram
// 2. Crea nuevo bot con /newbot
// 3. Obtén token y chat ID

$granja_telegram_config = array(
    'bot_token' => '1234567890:ABCdef123456789...', // Token de tu bot
    'chat_id' => '-1001234567890', // ID del grupo/canal de alertas
    'emergency_chat_id' => '-1009876543210', // Chat específico para emergencias
    'webhook_url' => GRANJA_SITE_URL . '/webhook/telegram',
    'notifications' => array(
        'form_urgent' => true,
        'phone_calls' => false, // Solo para emergencias extremas
        'new_testimonials' => true,
        'site_errors' => true,
    )
);

/* ===================================
   SLACK INTEGRATION (Opcional)
   =================================== */

$granja_slack_config = array(
    'webhook_url' => 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX',
    'channel' => '#granja-alerts',
    'username' => 'Granja Bot',
    'icon_emoji' => ':pray:',
    'notifications' => array(
        'urgent_forms' => true,
        'errors' => true,
        'testimonials' => false,
    )
);

/* ===================================
   MICROSOFT TEAMS (Alternativa a Slack)
   =================================== */

$granja_teams_config = array(
    'webhook_url' => 'https://outlook.office.com/webhook/...',
    'notifications' => array(
        'urgent_forms' => true,
        'daily_summary' => true,
    )
);

/* ===================================
   CONFIGURACIÓN DE SEGURIDAD
   =================================== */

$granja_security_config = array(
    'rate_limit' => array(
        'forms' => 5, // máximo 5 envíos por IP por hora
        'api_calls' => 100, // máximo 100 llamadas API por hora
    ),
    'csrf_protection' => true,
    'honeypot' => true,
    'ip_whitelist' => array(
        // IPs de confianza (oficina, staff, etc.)
        '192.168.1.100',
        '10.0.0.50',
    ),
    'blocked_countries' => array(), // Códigos de país a bloquear (opcional)
    'ssl_required' => true,
    'content_security_policy' => array(
        'default-src' => "'self'",
        'script-src' => "'self' 'unsafe-inline' *.google.com *.googleapis.com *.facebook.com *.instagram.com",
        'style-src' => "'self' 'unsafe-inline' *.googleapis.com *.cloudflare.com",
        'img-src' => "'self' data: *.instagram.com *.facebook.com *.google.com *.googleapis.com",
        'connect-src' => "'self' *.google.com *.googleapis.com api.instagram.com graph.facebook.com",
    )
);

/* ===================================
   CONFIGURACIÓN DE CACHE
   =================================== */

$granja_cache_config = array(
    'instagram_posts' => 3600, // 1 hora
    'google_maps' => 86400, // 24 horas
    'analytics_data' => 1800, // 30 minutos
    'testimonials' => 7200, // 2 horas
    'contact_forms' => 0, // No cachear
);

/* ===================================
   FUNCIONES AUXILIARES
   =================================== */

/**
 * Obtener configuración específica
 */
function granja_get_config($service) {
    global $granja_instagram_config, $granja_google_maps_config, 
           $granja_formspree_config, $granja_whatsapp_config,
           $granja_analytics_config, $granja_telegram_config;
    
    switch ($service) {
        case 'instagram':
            return $granja_instagram_config;
        case 'google_maps':
            return $granja_google_maps_config;
        case 'formspree':
            return $granja_formspree_config;
        case 'whatsapp':
            return $granja_whatsapp_config;
        case 'analytics':
            return $granja_analytics_config;
        case 'telegram':
            return $granja_telegram_config;
        default:
            return false;
    }
}

/**
 * Enviar notificación de emergencia
 */
function granja_send_emergency_notification($data) {
    $config = granja_get_config('telegram');
    
    if (!$config || !$config['bot_token']) {
        return false;
    }
    
    $message = "🚨 ALERTA DE EMERGENCIA - Granja Esperanza\n\n";
    $message .= "Nombre: {$data['nombre']}\n";
    $message .= "Teléfono: {$data['telefono']}\n";
    $message .= "Email: {$data['email']}\n";
    $message .= "Situación: {$data['situacion']}\n";
    $message .= "Mensaje: {$data['mensaje']}\n\n";
    $message .= "Tiempo: " . date('Y-m-d H:i:s') . "\n";
    $message .= "IP: " . $_SERVER['REMOTE_ADDR'];
    
    $url = "https://api.telegram.org/bot{$config['bot_token']}/sendMessage";
    
    $post_data = array(
        'chat_id' => $config['emergency_chat_id'],
        'text' => $message,
        'parse_mode' => 'HTML'
    );
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result !== false;
}

/**
 * Registrar evento en Google Analytics
 */
function granja_track_event($event_name, $parameters = array()) {
    $config = granja_get_config('analytics');
    
    if (!$config || !$config['measurement_id']) {
        return false;
    }
    
    // Implementar Measurement Protocol API para server-side tracking
    $url = "https://www.google-analytics.com/mp/collect";
    
    $data = array(
        'client_id' => isset($_SESSION['ga_client_id']) ? $_SESSION['ga_client_id'] : uniqid(),
        'events' => array(
            array(
                'name' => $event_name,
                'params' => $parameters
            )
        )
    );
    
    $headers = array(
        'Content-Type: application/json',
    );
    
    if ($config['api_secret']) {
        $url .= "?measurement_id={$config['measurement_id']}&api_secret={$config['api_secret']}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result !== false;
    }
    
    return false;
}

/**
 * Validar configuración
 */
function granja_validate_config() {
    $errors = array();
    
    // Verificar configuraciones críticas
    $config = granja_get_config('formspree');
    if (!$config || !$config['endpoint']) {
        $errors[] = 'Formspree endpoint no configurado';
    }
    
    $config = granja_get_config('google_maps');
    if (!$config || !$config['api_key']) {
        $errors[] = 'Google Maps API key no configurado';
    }
    
    $config = granja_get_config('analytics');
    if (!$config || !$config['measurement_id']) {
        $errors[] = 'Google Analytics Measurement ID no configurado';
    }
    
    return $errors;
}

/**
 * Test de conectividad de servicios
 */
function granja_test_services() {
    $results = array();
    
    // Test Instagram API
    $config = granja_get_config('instagram');
    if ($config && $config['access_token']) {
        $url = "https://graph.instagram.com/me/media?fields=id&access_token={$config['access_token']}&limit=1";
        $response = @file_get_contents($url);
        $results['instagram'] = $response !== false;
    }
    
    // Test Formspree
    $config = granja_get_config('formspree');
    if ($config && $config['endpoint']) {
        $ch = curl_init($config['endpoint']);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $results['formspree'] = $http_code < 400;
    }
    
    // Test Telegram Bot
    $config = granja_get_config('telegram');
    if ($config && $config['bot_token']) {
        $url = "https://api.telegram.org/bot{$config['bot_token']}/getMe";
        $response = @file_get_contents($url);
        $results['telegram'] = $response !== false;
    }
    
    return $results;
}

/* ===================================
   HOOKS Y ACCIONES DE WORDPRESS
   =================================== */

// Solo ejecutar si estamos en WordPress
if (function_exists('add_action')) {
    
    // Agregar scripts necesarios al header
    function granja_add_integration_scripts() {
        $analytics_config = granja_get_config('analytics');
        $maps_config = granja_get_config('google_maps');
        
        // Google Analytics
        if ($analytics_config && $analytics_config['measurement_id']) {
            ?>
            <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $analytics_config['measurement_id']; ?>"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', '<?php echo $analytics_config['measurement_id']; ?>');
            </script>
            <?php
        }
        
        // Google Maps
        if ($maps_config && $maps_config['api_key']) {
            ?>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $maps_config['api_key']; ?>&callback=initGoogleMap"></script>
            <?php
        }
    }
    add_action('wp_head', 'granja_add_integration_scripts');
    
    // AJAX handler para cargar posts de Instagram
    function granja_ajax_load_instagram_posts() {
        check_ajax_referer('instagram_nonce', 'nonce');
        
        $config = granja_get_config('instagram');
        
        if (!$config || !$config['access_token']) {
            wp_die('Instagram no configurado');
        }
        
        $cache_key = 'granja_instagram_posts';
        $posts = get_transient($cache_key);
        
        if (false === $posts) {
            $url = "https://graph.instagram.com/me/media";
            $url .= "?fields={$config['fields']}";
            $url .= "&access_token={$config['access_token']}";
            $url .= "&limit={$config['limit']}";
            
            $response = wp_remote_get($url);
            
            if (is_wp_error($response)) {
                wp_die('Error obteniendo posts de Instagram');
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['data'])) {
                $posts = $data['data'];
                set_transient($cache_key, $posts, $config['cache_duration']);
            } else {
                wp_die('Error en respuesta de Instagram API');
            }
        }
        
        wp_send_json_success($posts);
    }
    add_action('wp_ajax_load_instagram_posts', 'granja_ajax_load_instagram_posts');
    add_action('wp_ajax_nopriv_load_instagram_posts', 'granja_ajax_load_instagram_posts');
    
    // AJAX handler para envío de formularios urgentes
    function granja_ajax_emergency_form() {
        check_ajax_referer('emergency_nonce', 'nonce');
        
        $data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'telefono' => sanitize_text_field($_POST['telefono']),
            'email' => sanitize_email($_POST['email']),
            'situacion' => sanitize_text_field($_POST['situacion']),
            'mensaje' => sanitize_textarea_field($_POST['mensaje']),
            'urgente' => isset($_POST['urgente']) ? true : false,
        );
        
        // Enviar notificación de emergencia
        if ($data['urgente']) {
            granja_send_emergency_notification($data);
        }
        
        // Registrar evento en Analytics
        granja_track_event('form_submit_urgent', array(
            'event_category' => 'crisis_intervention',
            'value' => 10
        ));
        
        wp_send_json_success('Formulario enviado correctamente');
    }
    add_action('wp_ajax_emergency_form', 'granja_ajax_emergency_form');
    add_action('wp_ajax_nopriv_emergency_form', 'granja_ajax_emergency_form');
    
    // Shortcode para mostrar información de contacto
    function granja_contact_info_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'all', // all, phone, email, address, whatsapp
            'emergency' => false,
        ), $atts);
        
        $output = '<div class="granja-contact-info">';
        
        if ($atts['type'] === 'all' || $atts['type'] === 'phone') {
            $phone_class = $atts['emergency'] ? 'emergency-phone' : 'normal-phone';
            $output .= '<div class="contact-item phone">';
            $output .= '<i class="fas fa-phone"></i>';
            $output .= '<a href="tel:' . GRANJA_EMERGENCY_PHONE . '" class="' . $phone_class . '">';
            $output .= GRANJA_EMERGENCY_PHONE . '</a>';
            $output .= '</div>';
        }
        
        if ($atts['type'] === 'all' || $atts['type'] === 'whatsapp') {
            $whatsapp_config = granja_get_config('whatsapp');
            $output .= '<div class="contact-item whatsapp">';
            $output .= '<i class="fab fa-whatsapp"></i>';
            $output .= '<a href="' . $whatsapp_config['general'] . '" target="_blank">';
            $output .= 'WhatsApp</a>';
            $output .= '</div>';
        }
        
        if ($atts['type'] === 'all' || $atts['type'] === 'email') {
            $output .= '<div class="contact-item email">';
            $output .= '<i class="fas fa-envelope"></i>';
            $output .= '<a href="mailto:' . GRANJA_CONTACT_EMAIL . '">';
            $output .= GRANJA_CONTACT_EMAIL . '</a>';
            $output .= '</div>';
        }
        
        if ($atts['type'] === 'all' || $atts['type'] === 'address') {
            $output .= '<div class="contact-item address">';
            $output .= '<i class="fas fa-map-marker-alt"></i>';
            $output .= '<span>' . GRANJA_ADDRESS . '</span>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    add_shortcode('granja_contacto', 'granja_contact_info_shortcode');
}

/* ===================================
   LOGGING Y DEBUGGING
   =================================== */

function granja_log_integration_error($service, $error) {
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        error_log("Granja Esperanza - {$service} Error: " . $error);
    }
}

function granja_log_form_submission($form_type, $data) {
    if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
        error_log("Granja Esperanza - Form Submission ({$form_type}): " . json_encode($data));
    }
}

/* ===================================
   CLEANUP Y MANTENIMIENTO
   =================================== */

// Limpiar transients expirados cada día
if (function_exists('add_action')) {
    add_action('wp_scheduled_delete', function() {
        delete_expired_transients();
    });
    
    // Renovar tokens automáticamente
    add_action('granja_refresh_tokens', function() {
        granja_refresh_instagram_token();
    });
    
    // Programar renovación de tokens si no existe
    if (!wp_next_scheduled('granja_refresh_tokens')) {
        wp_schedule_event(time(), 'weekly', 'granja_refresh_tokens');
    }
}

/**
 * Renovar token de Instagram automáticamente
 */
function granja_refresh_instagram_token() {
    $config = granja_get_config('instagram');
    
    if (!$config || !$config['access_token']) {
        return false;
    }
    
    $url = "https://graph.instagram.com/refresh_access_token";
    $url .= "?grant_type=ig_refresh_token";
    $url .= "&access_token={$config['access_token']}";
    
    $response = wp_remote_get($url);
    
    if (!is_wp_error($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['access_token'])) {
            // Actualizar token en configuración
            // (Implementar según tu método de almacenamiento)
            granja_log_integration_error('instagram', 'Token renovado exitosamente');
        }
    }
}

// Información de configuración para debugging
if (defined('WP_DEBUG') && WP_DEBUG) {
    function granja_debug_info() {
        echo "<!-- Granja Esperanza - Debug Info -->\n";
        echo "<!-- Instagram configurado: " . (granja_get_config('instagram') ? 'Si' : 'No') . " -->\n";
        echo "<!-- Google Maps configurado: " . (granja_get_config('google_maps') ? 'Si' : 'No') . " -->\n";
        echo "<!-- Formspree configurado: " . (granja_get_config('formspree') ? 'Si' : 'No') . " -->\n";
        echo "<!-- Analytics configurado: " . (granja_get_config('analytics') ? 'Si' : 'No') . " -->\n";
    }
    
    if (function_exists('add_action')) {
        add_action('wp_footer', 'granja_debug_info');
    }
}

?>
