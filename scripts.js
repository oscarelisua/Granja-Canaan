/* ===================================
   CONFIGURACIÓN Y VARIABLES GLOBALES
   =================================== */

// Configuración de la aplicación
const CONFIG = {
    instagram: {
        accessToken: 'TU_ACCESS_TOKEN_AQUI', // Reemplazar con token real
        userId: 'TU_USER_ID_AQUI', // Reemplazar con user ID real
        limit: 6 // Número de posts a mostrar
    },
    googleMaps: {
        apiKey: 'AIzaSyA0d4xu4-gDc-EheSNxGUFZIldhF9TLhYg', // Reemplazar con API key real
        coordinates: {
            lat: -32.867476, // Coordenadas de ejemplo (Rosario)
            lng: -60.785570
        },
        zoom: 15
    },
    analytics: {
        measurementId: 'GA_MEASUREMENT_ID' // Reemplazar con ID real
    },
    form: {
        endpoint: 'https://formspree.io/f/TU_FORM_ID', // Endpoint del formulario
        webhookUrl: 'https://hook.integromat.com/TU_WEBHOOK' // Para notificaciones
    }
};

// Variables globales
let map;
let testimonialIndex = 0;
let testimonialInterval;
let isMapLoaded = false;

/* ===================================
   UTILIDADES GENERALES
   =================================== */

// Función para debugging
const log = (message, data = null) => {
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log(`[Granja Canaan] ${message}`, data);
    }
};

// Función para mostrar notificaciones
const showNotification = (message, type = 'info') => {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Estilos inline para la notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
        max-width: 400px;
    `;
    
    document.body.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remove después de 5 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
    
    // Click para cerrar
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    });
};

// Función para validar email
const validateEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Función para validar teléfono argentino
const validatePhone = (phone) => {
    const re = /^[\+]?[0-9\s\-\(\)]{8,15}$/;
    return re.test(phone.replace(/\s/g, ''));
};

/* ===================================
   NAVEGACIÓN Y HEADER
   =================================== */

class Navigation {
    constructor() {
        this.navToggle = document.getElementById('navToggle');
        this.navMenu = document.getElementById('navMenu');
        this.navLinks = document.querySelectorAll('.nav-link');
        this.header = document.querySelector('.header');
        
        this.init();
    }
    
    init() {
        // Toggle móvil
        if (this.navToggle && this.navMenu) {
            this.navToggle.addEventListener('click', () => this.toggleMobileMenu());
        }
        
        // Cerrar menú al hacer click en enlaces
        this.navLinks.forEach(link => {
            link.addEventListener('click', () => this.closeMobileMenu());
        });
        
        // Scroll para header transparente
        window.addEventListener('scroll', () => this.handleScroll());
        
        // Smooth scroll para enlaces internos
        this.setupSmoothScroll();
    }
    
    toggleMobileMenu() {
        this.navMenu.classList.toggle('active');
        this.navToggle.classList.toggle('active');
        
        // Prevenir scroll del body cuando el menú está abierto
        document.body.style.overflow = this.navMenu.classList.contains('active') ? 'hidden' : '';
    }
    
    closeMobileMenu() {
        this.navMenu.classList.remove('active');
        this.navToggle.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    handleScroll() {
        const scrolled = window.scrollY > 50;
        
        if (scrolled) {
            this.header.style.background = 'rgba(255, 255, 255, 0.98)';
            this.header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
        } else {
            this.header.style.background = 'rgba(255, 255, 255, 0.95)';
            this.header.style.boxShadow = 'none';
        }
    }
    
    setupSmoothScroll() {
        this.navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        const offsetTop = target.offsetTop - 80; // Header height
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                });
            }
        });
    }
}

/* ===================================
   SLIDER DE TESTIMONIOS
   =================================== */

class TestimonialsSlider {
    constructor() {
        this.testimonials = document.querySelectorAll('.testimonial-card');
        this.indicators = document.querySelectorAll('.indicator');
        this.prevBtn = document.querySelector('.testimonial-btn.prev');
        this.nextBtn = document.querySelector('.testimonial-btn.next');
        this.currentIndex = 0;
        this.autoPlayInterval = null;
        
        this.init();
    }
    
    init() {
        if (this.testimonials.length === 0) return;
        
        // Event listeners
        if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.prevSlide());
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.nextSlide());
        
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Auto-play
        this.startAutoPlay();
        
        // Pausar auto-play al hacer hover
        const sliderContainer = document.querySelector('.testimonials-slider');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', () => this.stopAutoPlay());
            sliderContainer.addEventListener('mouseleave', () => this.startAutoPlay());
        }
        
        // Inicializar primer slide
        this.showSlide(0);
    }
    
    showSlide(index) {
        // Ocultar todos los slides
        this.testimonials.forEach(slide => {
            slide.classList.remove('active');
        });
        
        // Remover active de todos los indicadores
        this.indicators.forEach(indicator => {
            indicator.classList.remove('active');
        });
        
        // Mostrar slide actual
        if (this.testimonials[index]) {
            this.testimonials[index].classList.add('active');
        }
        
        // Activar indicador
        if (this.indicators[index]) {
            this.indicators[index].classList.add('active');
        }
        
        this.currentIndex = index;
    }
    
    nextSlide() {
        const nextIndex = (this.currentIndex + 1) % this.testimonials.length;
        this.showSlide(nextIndex);
    }
    
    prevSlide() {
        const prevIndex = (this.currentIndex - 1 + this.testimonials.length) % this.testimonials.length;
        this.showSlide(prevIndex);
    }
    
    goToSlide(index) {
        this.showSlide(index);
    }
    
    startAutoPlay() {
        this.stopAutoPlay(); // Limpiar interval existente
        this.autoPlayInterval = setInterval(() => {
            this.nextSlide();
        }, 5000); // Cambiar cada 5 segundos
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }
}

/* ===================================
   INTEGRACIÓN CON INSTAGRAM
   =================================== */

class InstagramFeed {
    constructor() {
        this.container = document.getElementById('instagramFeed');
        this.init();
    }
    
    async init() {
        if (!this.container) return;
        
        try {
            await this.loadPosts();
        } catch (error) {
            log('Error cargando Instagram feed:', error);
            this.showError();
        }
    }
    
    async loadPosts() {
        // Simulación de datos para development - reemplazar con API real
        const mockPosts = [
            {
                id: '1',
                media_url: 'https://picsum.photos/400/400?random=1',
                caption: 'Un día de trabajo en la granja. #restauración #esperanza',
                permalink: 'https://instagram.com/p/ejemplo1'
            },
            {
                id: '2',
                media_url: 'https://picsum.photos/400/400?random=2',
                caption: 'Actividades grupales fortalecen la comunidad. #comunidad #sanidad',
                permalink: 'https://instagram.com/p/ejemplo2'
            },
            {
                id: '3',
                media_url: 'https://picsum.photos/400/400?random=3',
                caption: 'La naturaleza como parte del proceso de sanidad. #naturaleza #paz',
                permalink: 'https://instagram.com/p/ejemplo3'
            },
            {
                id: '4',
                media_url: 'https://picsum.photos/400/400?random=4',
                caption: 'Celebrando graduaciones y nuevos comienzos. #graduación #nuevavida',
                permalink: 'https://instagram.com/p/ejemplo4'
            },
            {
                id: '5',
                media_url: 'https://picsum.photos/400/400?random=5',
                caption: 'Talleres de capacitación laboral. #trabajo #dignidad',
                permalink: 'https://instagram.com/p/ejemplo5'
            },
            {
                id: '6',
                media_url: 'https://picsum.photos/400/400?random=6',
                caption: 'Tiempo de reflexión y oración. #fe #esperanza',
                permalink: 'https://instagram.com/p/ejemplo6'
            }
        ];
        
        // Para producción, usar la API real de Instagram
        // const response = await fetch(`https://graph.instagram.com/me/media?fields=id,caption,media_url,permalink&access_token=${CONFIG.instagram.accessToken}&limit=${CONFIG.instagram.limit}`);
        // const data = await response.json();
        // const posts = data.data;
        
        this.renderPosts(mockPosts);
    }
    
    renderPosts(posts) {
        this.container.innerHTML = '';
        
        posts.forEach(post => {
            const postElement = document.createElement('div');
            postElement.className = 'instagram-post';
            postElement.innerHTML = `
                <img src="${post.media_url}" alt="Instagram post" loading="lazy">
                <div class="instagram-overlay">
                    <p>${this.truncateCaption(post.caption)}</p>
                </div>
            `;
            
            postElement.addEventListener('click', () => {
                window.open(post.permalink, '_blank');
                
                // Analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'instagram_post_click', {
                        event_category: 'social_media',
                        event_label: post.id
                    });
                }
            });
            
            this.container.appendChild(postElement);
        });
    }
    
    truncateCaption(caption) {
        if (!caption) return '';
        return caption.length > 80 ? caption.substring(0, 80) + '...' : caption;
    }
    
    showError() {
        this.container.innerHTML = `
            <div class="instagram-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>No se pudieron cargar las publicaciones de Instagram</p>
                <a href="https://instagram.com/iglesiacanaan__" target="_blank" class="btn-instagram">
                    <i class="fab fa-instagram"></i>
                    Ver en Instagram
                </a>
            </div>
        `;
    }
}

/* ===================================
   GOOGLE MAPS
   =================================== */

class GoogleMapsIntegration {
    constructor() {
        this.mapContainer = document.getElementById('map');
        this.init();
    }
    
    init() {
        if (!this.mapContainer) return;
        
        // Cargar Google Maps script
        this.loadGoogleMapsScript();
    }
    
    loadGoogleMapsScript() {
        // Verificar si ya está cargado
        if (window.google && window.google.maps) {
            this.initMap();
            return;
        }
        
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${CONFIG.googleMaps.apiKey}&callback=initGoogleMap`;
        script.async = true;
        script.defer = true;
        
        // Callback global
        window.initGoogleMap = () => {
            this.initMap();
        };
        
        document.head.appendChild(script);
    }
    
    initMap() {
        const { lat, lng } = CONFIG.googleMaps.coordinates;
        
        const mapOptions = {
            center: { lat, lng },
            zoom: CONFIG.googleMaps.zoom,
            styles: this.getMapStyles(),
            disableDefaultUI: false,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: true,
            streetViewControl: true,
            rotateControl: false,
            fullscreenControl: true
        };
        
        map = new google.maps.Map(this.mapContainer, mapOptions);
        
        // Marker personalizado
        const marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: 'Granja Canaan',
            icon: {
                url: 'data:image/svg+xml;base64,' + btoa(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="50" viewBox="0 0 40 50">
                        <path fill="#2563eb" d="M20 0C9 0 0 9 0 20c0 15 20 30 20 30s20-15 20-30C40 9 31 0 20 0z"/>
                        <circle fill="white" cx="20" cy="20" r="8"/>
                        <path fill="#2563eb" d="M20 14l2 4h4l-3 3 1 4-4-2-4 2 1-4-3-3h4z"/>
                    </svg>
                `),
                scaledSize: new google.maps.Size(40, 50),
                anchor: new google.maps.Point(20, 50)
            }
        });
        
        // Info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px; max-width: 250px;">
                    <h3 style="margin: 0 0 10px 0; color: #2563eb;">Granja Canaan</h3>
                    <p style="margin: 0 0 10px 0;">Centro de rehabilitación cristiana</p>
                    <p style="margin: 0; color: #666;">
                        <i class="fas fa-phone" style="margin-right: 5px;"></i>
                        <a href="tel:+54341XXXXXXX" style="color: #2563eb;">+54 341 XXX-XXXX</a>
                    </p>
                </div>
            `
        });
        
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        // Click en el mapa para abrir Google Maps
        map.addListener('click', () => {
            const url = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            window.open(url, '_blank');
        });
        
        isMapLoaded = true;
        log('Google Maps cargado correctamente');
    }
    
    getMapStyles() {
        // Estilo personalizado para el mapa
        return [
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [{"color": "#e9e9e9"}, {"lightness": 17}]
            },
            {
                "featureType": "landscape",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}, {"lightness": 20}]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.fill",
                "stylers": [{"color": "#ffffff"}, {"lightness": 17}]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry.stroke",
                "stylers": [{"color": "#ffffff"}, {"lightness": 29}, {"weight": 0.2}]
            },
            {
                "featureType": "road.arterial",
                "elementType": "geometry",
                "stylers": [{"color": "#ffffff"}, {"lightness": 18}]
            },
            {
                "featureType": "road.local",
                "elementType": "geometry",
                "stylers": [{"color": "#ffffff"}, {"lightness": 16}]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [{"color": "#f5f5f5"}, {"lightness": 21}]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [{"color": "#dedede"}, {"lightness": 21}]
            }
        ];
    }
}

/* ===================================
   FORMULARIO DE CONTACTO
   =================================== */

class ContactForm {
    constructor() {
        this.form = document.getElementById('contactForm');
        this.submitBtn = this.form?.querySelector('.btn-submit');
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Validación en tiempo real
        this.setupRealTimeValidation();
        
        // Autocompletado inteligente
        this.setupAutoComplete();
    }
    
    setupRealTimeValidation() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }
    
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Validaciones específicas por campo
        switch (field.name) {
            case 'nombre':
                if (!value) {
                    isValid = false;
                    errorMessage = 'El nombre es requerido';
                } else if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'El nombre debe tener al menos 2 caracteres';
                }
                break;
                
            case 'email':
                if (!value) {
                    isValid = false;
                    errorMessage = 'El email es requerido';
                } else if (!validateEmail(value)) {
                    isValid = false;
                    errorMessage = 'Ingresa un email válido';
                }
                break;
                
            case 'telefono':
                if (!value) {
                    isValid = false;
                    errorMessage = 'El teléfono es requerido';
                } else if (!validatePhone(value)) {
                    isValid = false;
                    errorMessage = 'Ingresa un teléfono válido';
                }
                break;
                
            case 'tipoConsulta':
                if (!value) {
                    isValid = false;
                    errorMessage = 'Selecciona el tipo de consulta';
                }
                break;
        }
        
        this.showFieldError(field, isValid, errorMessage);
        return isValid;
    }
    
    showFieldError(field, isValid, errorMessage) {
        const formGroup = field.closest('.form-group');
        let errorElement = formGroup.querySelector('.field-error');
        
        if (!isValid) {
            field.style.borderColor = '#ef4444';
            
            if (!errorElement) {
                errorElement = document.createElement('span');
                errorElement.className = 'field-error';
                errorElement.style.cssText = 'color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem;';
                formGroup.appendChild(errorElement);
            }
            
            errorElement.textContent = errorMessage;
        } else {
            field.style.borderColor = '#10b981';
            if (errorElement) {
                errorElement.remove();
            }
        }
    }
    
    clearFieldError(field) {
        field.style.borderColor = '';
        const formGroup = field.closest('.form-group');
        const errorElement = formGroup.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    setupAutoComplete() {
        // Auto-format teléfono
        const phoneInput = this.form.querySelector('input[name="telefono"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('54')) {
                    value = '+' + value;
                } else if (value.startsWith('9')) {
                    value = '+54 9 ' + value.substring(1);
                }
                e.target.value = value;
            });
        }
    }
    
    async handleSubmit(e) {
        e.preventDefault();
        
        // Validar formulario completo
        if (!this.validateForm()) {
            showNotification('Por favor corrige los errores en el formulario', 'error');
            return;
        }
        
        // Obtener datos del formulario
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());
        
        // Mostrar loading
        this.setSubmitLoading(true);
        
        try {
            // Enviar formulario
            await this.submitForm(data);
            
            // Notificar urgencia si es necesario
            if (data.urgente) {
                await this.notifyUrgency(data);
            }
            
            // Éxito
            this.handleSuccess(data);
            
        } catch (error) {
            log('Error enviando formulario:', error);
            this.handleError(error);
        } finally {
            this.setSubmitLoading(false);
        }
    }
    
    validateForm() {
        const requiredFields = this.form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    async submitForm(data) {
        // Preparar datos para envío
        const payload = {
            ...data,
            timestamp: new Date().toISOString(),
            source: 'website',
            userAgent: navigator.userAgent,
            url: window.location.href
        };
        
        // Enviar a múltiples endpoints para redundancia
        const endpoints = [
            CONFIG.form.endpoint,
            CONFIG.form.webhookUrl
        ].filter(Boolean);
        
        const promises = endpoints.map(endpoint =>
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
        );
        
        // Esperar al menos uno exitoso
        await Promise.any(promises);
    }
    
    async notifyUrgency(data) {
        // Notificación especial para casos urgentes
        const urgencyPayload = {
            type: 'URGENCY_ALERT',
            message: `🚨 CONSULTA URGENTE - ${data.nombre}`,
            phone: data.telefono,
            email: data.email,
            situation: data.situacion,
            timestamp: new Date().toISOString()
        };
        
        try {
            await fetch('https://api.telegram.org/botTU_BOT_TOKEN/sendMessage', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    chat_id: 'TU_CHAT_ID',
                    text: `🚨 CONSULTA URGENTE\n\nNombre: ${data.nombre}\nTeléfono: ${data.telefono}\nEmail: ${data.email}\nSituación: ${data.situacion}\nMensaje: ${data.mensaje}`,
                    parse_mode: 'HTML'
                })
            });
        } catch (error) {
            log('Error enviando notificación de urgencia:', error);
        }
    }
    
    handleSuccess(data) {
        // Limpiar formulario
        this.form.reset();
        
        // Mensaje de éxito personalizado
        const isUrgent = data.urgente;
        const message = isUrgent 
            ? '¡Recibido! Te contactaremos en la próxima hora. Si es una emergencia inmediata, llama ahora.' 
            : '¡Gracias por contactarnos! Te responderemos a la brevedad.';
        
        showNotification(message, 'success');
        
        // Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submit', {
                event_category: 'contact',
                event_label: data.tipoConsulta,
                value: isUrgent ? 2 : 1
            });
        }
        
        // Redirect para casos urgentes
        if (isUrgent) {
            setTimeout(() => {
                const confirmed = confirm('¿Quieres llamar ahora para atención inmediata?');
                if (confirmed) {
                    window.location.href = 'tel:+54341XXXXXXX';
                }
            }, 3000);
        }
    }
    
    handleError(error) {
        showNotification('Hubo un error. Por favor intenta nuevamente o llámanos directamente.', 'error');
    }
    
    setSubmitLoading(loading) {
        if (!this.submitBtn) return;
        
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Consulta';
        }
    }
}

/* ===================================
   BOTÓN BACK TO TOP
   =================================== */

class BackToTop {
    constructor() {
        this.button = document.getElementById('backToTop');
        this.init();
    }
    
    init() {
        if (!this.button) return;
        
        window.addEventListener('scroll', () => this.handleScroll());
        this.button.addEventListener('click', () => this.scrollToTop());
    }
    
    handleScroll() {
        const scrolled = window.scrollY > 300;
        
        if (scrolled) {
            this.button.classList.add('visible');
        } else {
            this.button.classList.remove('visible');
        }
    }
    
    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'scroll_to_top', {
                event_category: 'navigation'
            });
        }
    }
}

/* ===================================
   ANALYTICS Y TRACKING
   =================================== */

class Analytics {
    constructor() {
        this.init();
    }
    
    init() {
        // Configurar Google Analytics
        this.setupGoogleAnalytics();
        
        // Track eventos importantes
        this.trackInteractions();
        
        // Track tiempo en página
        this.trackTimeOnPage();
    }
    
    setupGoogleAnalytics() {
        if (typeof gtag === 'undefined') return;
        
        // Configuración mejorada
        gtag('config', CONFIG.analytics.measurementId, {
            page_title: document.title,
            page_location: window.location.href,
            custom_map: {
                'dimension1': 'page_type',
                'dimension2': 'user_type'
            }
        });
        
        // Eventos de conversion
        gtag('event', 'page_view', {
            page_title: document.title,
            page_location: window.location.href,
            page_type: 'landing'
        });
    }
    
    trackInteractions() {
        // Track llamadas telefónicas
        document.querySelectorAll('a[href^="tel:"]').forEach(link => {
            link.addEventListener('click', () => {
                gtag('event', 'phone_call', {
                    event_category: 'contact',
                    event_label: link.href,
                    value: 10 // Valor alto para conversiones
                });
            });
        });
        
        // Track WhatsApp
        document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(link => {
            link.addEventListener('click', () => {
                gtag('event', 'whatsapp_click', {
                    event_category: 'contact',
                    event_label: 'whatsapp_button'
                });
            });
        });
        
        // Track scroll depth
        let maxScroll = 0;
        window.addEventListener('scroll', () => {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            
            if (scrollPercent > maxScroll && scrollPercent % 25 === 0) {
                maxScroll = scrollPercent;
                gtag('event', 'scroll', {
                    event_category: 'engagement',
                    event_label: `${scrollPercent}%`,
                    value: scrollPercent
                });
            }
        });
        
        // Track video plays
        document.querySelectorAll('iframe[src*="youtube"]').forEach(iframe => {
            iframe.addEventListener('load', () => {
                gtag('event', 'video_load', {
                    event_category: 'video',
                    event_label: 'youtube_embed'
                });
            });
        });
    }
    
    trackTimeOnPage() {
        const startTime = Date.now();
        
        // Track cuando el usuario sale de la página
        window.addEventListener('beforeunload', () => {
            const timeOnPage = Math.round((Date.now() - startTime) / 1000);
            
            gtag('event', 'timing_complete', {
                name: 'time_on_page',
                value: timeOnPage
            });
        });
        
        // Track milestone de tiempo (cada 30 segundos)
        setInterval(() => {
            const timeOnPage = Math.round((Date.now() - startTime) / 1000);
            
            if (timeOnPage % 30 === 0 && timeOnPage > 0) {
                gtag('event', 'time_milestone', {
                    event_category: 'engagement',
                    event_label: `${timeOnPage}s`,
                    value: timeOnPage
                });
            }
        }, 1000);
    }
}

/* ===================================
   OPTIMIZACIONES DE PERFORMANCE
   =================================== */

class PerformanceOptimizations {
    constructor() {
        this.init();
    }
    
    init() {
        // Lazy loading para imágenes
        this.setupLazyLoading();
        
        // Preload recursos críticos
        this.preloadCriticalResources();
        
        // Optimizar fonts
        this.optimizeFonts();
        
        // Reducir repaints/reflows
        this.optimizeScrolling();
    }
    
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    preloadCriticalResources() {
        // Preload hero image
        const heroImg = document.querySelector('.hero-img');
        if (heroImg && heroImg.src) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = heroImg.src;
            document.head.appendChild(link);
        }
        
        // Preload critical CSS
        const criticalCss = document.createElement('link');
        criticalCss.rel = 'preload';
        criticalCss.as = 'style';
        criticalCss.href = 'styles.css';
        document.head.appendChild(criticalCss);
    }
    
    optimizeFonts() {
        // Font display swap para mejorar CLS
        if (document.fonts) {
            document.fonts.ready.then(() => {
                log('Fonts loaded');
            });
        }
    }
    
    optimizeScrolling() {
        let ticking = false;
        
        const updateScrolling = () => {
            // Actualizar elementos que dependen del scroll
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateScrolling);
                ticking = true;
            }
        }, { passive: true });
    }
}

/* ===================================
   FUNCIONES AUXILIARES
   =================================== */

// Función para mostrar política de privacidad
window.mostrarPoliticaPrivacidad = () => {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Política de Privacidad</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>En Granja Canaan respetamos y protegemos la privacidad de nuestros usuarios...</p>
                <p>Toda la información proporcionada será tratada con absoluta confidencialidad...</p>
            </div>
        </div>
    `;
    
    // Estilos del modal
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8); z-index: 1000;
        display: flex; align-items: center; justify-content: center;
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar modal
    modal.querySelector('.modal-close').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
};

// Función para mostrar términos
window.mostrarTerminos = () => {
    showNotification('Términos de uso disponibles próximamente', 'info');
};

// Función para mostrar código ético
window.mostrarEtica = () => {
    showNotification('Código ético disponible próximamente', 'info');
};

/* ===================================
   INICIALIZACIÓN DE LA APLICACIÓN
   =================================== */

class App {
    constructor() {
        this.components = [];
        this.init();
    }
    
    init() {
        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initializeComponents());
        } else {
            this.initializeComponents();
        }
    }
    
    initializeComponents() {
        try {
            // Inicializar todos los componentes
            this.components = [
                new Navigation(),
                new TestimonialsSlider(),
                new InstagramFeed(),
                new GoogleMapsIntegration(),
                new ContactForm(),
                new BackToTop(),
                new Analytics(),
                new PerformanceOptimizations()
            ];
            
            log('Aplicación inicializada correctamente');
            
            // Evento personalizado para notificar que la app está lista
            window.dispatchEvent(new CustomEvent('appReady'));
            
        } catch (error) {
            console.error('Error inicializando la aplicación:', error);
        }
    }
}

// Inicializar aplicación
const app = new App();

/* ===================================
   SERVICE WORKER REGISTRATION
   =================================== */

// Registrar service worker para PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                log('SW registered:', registration);
            })
            .catch(registrationError => {
                log('SW registration failed:', registrationError);
            });
    });
}

/* ===================================
   EXPORT PARA MÓDULOS (SI ES NECESARIO)
   =================================== */

// Para usar en otros scripts si es necesario
window.GranjaCanaan = {
    CONFIG,
    showNotification,
    validateEmail,
    validatePhone,
    log
};