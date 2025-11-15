<?php
/**
 * Template Name: Página de Testimonios
 * Description: Template especializado para mostrar testimonios de graduados del programa de rehabilitación
 * Version: 1.0
 * Author: Granja Esperanza Dev Team
 * 
 * Este template está optimizado para:
 * - Protección de privacidad de los testimonios
 * - SEO específico para testimonios de rehabilitación
 * - Formulario de envío de nuevos testimonios
 * - Sistema de consentimiento y aprobación
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<!-- Schema.org para testimonios -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Testimonios de Graduados - Granja Esperanza",
    "description": "Historias reales de transformación y recuperación en nuestro centro de rehabilitación cristiano",
    "url": "<?php echo get_permalink(); ?>",
    "mainEntity": {
        "@type": "Organization",
        "@id": "<?php echo home_url(); ?>",
        "name": "<?php bloginfo('name'); ?>"
    }
}
</script>

<div class="testimonials-page">
    <!-- Hero Section específico para testimonios -->
    <section class="testimonials-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="page-title">
                    Historias de <span class="highlight">Transformación</span>
                </h1>
                <p class="hero-description">
                    Cada historia es única, pero todas comparten algo en común: 
                    la esperanza de una vida nueva en Cristo. Conoce a quienes han 
                    caminado este sendero de restauración.
                </p>
                
                <div class="hero-stats">
                    <?php
                    // Obtener estadísticas dinámicas
                    $total_graduados = get_option('granja_total_graduados', 200);
                    $anos_operacion = get_option('granja_anos_operacion', 10);
                    $tasa_exito = get_option('granja_tasa_exito', 85);
                    ?>
                    
                    <div class="stat">
                        <span class="stat-number"><?php echo $total_graduados; ?>+</span>
                        <span class="stat-label">Graduados</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo $anos_operacion; ?>+</span>
                        <span class="stat-label">Años de ministerio</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number"><?php echo $tasa_exito; ?>%</span>
                        <span class="stat-label">Tasa de éxito</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtros de testimonios -->
    <section class="testimonials-filters">
        <div class="container">
            <div class="filters-container">
                <h3>Filtrar testimonios:</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">Todos</button>
                    <button class="filter-btn" data-filter="reciente">Recientes</button>
                    <button class="filter-btn" data-filter="programa-residencial">Programa Residencial</button>
                    <button class="filter-btn" data-filter="programa-ambulatorio">Programa Ambulatorio</button>
                    <button class="filter-btn" data-filter="familia">Testimonios Familiares</button>
                    <button class="filter-btn" data-filter="voluntario">Ex-graduados Voluntarios</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Grid de testimonios -->
    <section class="testimonials-grid">
        <div class="container">
            <div class="testimonials-container">
                <?php
                // Query personalizada para testimonios
                $testimonios_args = array(
                    'post_type' => 'testimonio',
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'testimonio_aprobado',
                            'value' => 'si',
                            'compare' => '='
                        ),
                        array(
                            'key' => 'consentimiento_publicacion',
                            'value' => 'si',
                            'compare' => '='
                        )
                    ),
                    'orderby' => 'date',
                    'order' => 'DESC'
                );

                $testimonios_query = new WP_Query($testimonios_args);

                if ($testimonios_query->have_posts()) :
                    while ($testimonios_query->have_posts()) : $testimonios_query->the_post();
                        
                        // Obtener metadatos del testimonio
                        $nombre_publico = get_post_meta(get_the_ID(), 'nombre_publico', true);
                        $programa_completado = get_post_meta(get_the_ID(), 'programa_completado', true);
                        $ano_graduacion = get_post_meta(get_the_ID(), 'ano_graduacion', true);
                        $tiempo_sobriedad = get_post_meta(get_the_ID(), 'tiempo_sobriedad', true);
                        $categoria_testimonio = get_post_meta(get_the_ID(), 'categoria_testimonio', true);
                        $mostrar_foto = get_post_meta(get_the_ID(), 'mostrar_foto', true);
                        $foto_testimonio = get_post_meta(get_the_ID(), 'foto_testimonio', true);
                        $es_familia = get_post_meta(get_the_ID(), 'es_testimonio_familiar', true);
                        $es_voluntario = get_post_meta(get_the_ID(), 'es_voluntario_actual', true);
                        
                        // Clases CSS para filtros
                        $filter_classes = array('testimonio-card');
                        if ($programa_completado) $filter_classes[] = 'programa-' . sanitize_title($programa_completado);
                        if ($categoria_testimonio) $filter_classes[] = sanitize_title($categoria_testimonio);
                        if ($es_familia === 'si') $filter_classes[] = 'familia';
                        if ($es_voluntario === 'si') $filter_classes[] = 'voluntario';
                        
                        // Verificar si es reciente (últimos 6 meses)
                        $fecha_post = get_the_date('Y-m-d');
                        $hace_6_meses = date('Y-m-d', strtotime('-6 months'));
                        if ($fecha_post >= $hace_6_meses) {
                            $filter_classes[] = 'reciente';
                        }
                        ?>
                        
                        <article class="<?php echo implode(' ', $filter_classes); ?>" data-id="<?php echo get_the_ID(); ?>">
                            <div class="testimonio-content">
                                
                                <!-- Foto del testimonio (si está permitida) -->
                                <?php if ($mostrar_foto === 'si' && $foto_testimonio) : ?>
                                    <div class="testimonio-foto">
                                        <img src="<?php echo esc_url($foto_testimonio); ?>" 
                                             alt="Testimonio de <?php echo esc_attr($nombre_publico); ?>"
                                             loading="lazy">
                                    </div>
                                <?php else : ?>
                                    <div class="testimonio-avatar">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Contenido del testimonio -->
                                <div class="testimonio-text">
                                    <blockquote class="testimonio-quote">
                                        <?php 
                                        // Truncar testimonio para vista previa
                                        $contenido = get_the_content();
                                        $contenido_limpio = wp_strip_all_tags($contenido);
                                        $preview = strlen($contenido_limpio) > 200 
                                            ? substr($contenido_limpio, 0, 200) . '...'
                                            : $contenido_limpio;
                                        echo esc_html($preview);
                                        ?>
                                    </blockquote>
                                </div>
                                
                                <!-- Información del graduado -->
                                <div class="testimonio-author">
                                    <h4 class="author-name">
                                        <?php echo esc_html($nombre_publico ?: 'Anónimo'); ?>
                                    </h4>
                                    <div class="author-details">
                                        <?php if ($programa_completado) : ?>
                                            <span class="programa">
                                                <i class="fas fa-graduation-cap"></i>
                                                <?php echo esc_html($programa_completado); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($ano_graduacion) : ?>
                                            <span class="graduacion">
                                                <i class="fas fa-calendar"></i>
                                                Graduado <?php echo esc_html($ano_graduacion); ?>
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($tiempo_sobriedad) : ?>
                                            <span class="sobriedad">
                                                <i class="fas fa-clock"></i>
                                                <?php echo esc_html($tiempo_sobriedad); ?> en recuperación
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($es_voluntario === 'si') : ?>
                                            <span class="voluntario">
                                                <i class="fas fa-heart"></i>
                                                Voluntario actual
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Botón para leer más -->
                                <div class="testimonio-actions">
                                    <button class="btn-leer-mas" 
                                            data-testimonio-id="<?php echo get_the_ID(); ?>"
                                            data-toggle="modal" 
                                            data-target="#testimonioModal">
                                        Leer testimonio completo
                                    </button>
                                </div>
                                
                                <!-- Tags del testimonio -->
                                <div class="testimonio-tags">
                                    <?php if ($es_familia === 'si') : ?>
                                        <span class="tag familia">Testimonio Familiar</span>
                                    <?php endif; ?>
                                    
                                    <?php if ($categoria_testimonio) : ?>
                                        <span class="tag categoria">
                                            <?php echo esc_html(ucfirst($categoria_testimonio)); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                        
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                    <div class="no-testimonios">
                        <i class="fas fa-heart"></i>
                        <h3>Próximamente</h3>
                        <p>Estamos preparando más testimonios para compartir contigo.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>
            
            <!-- Botón para cargar más -->
            <div class="load-more-container">
                <button id="loadMoreTestimonios" class="btn-secondary">
                    <i class="fas fa-plus"></i>
                    Cargar más testimonios
                </button>
            </div>
        </div>
    </section>

    <!-- Call to Action para enviar testimonio -->
    <section class="submit-testimonio-cta">
        <div class="container">
            <div class="cta-content">
                <div class="cta-text">
                    <h2>¿Eres graduado de nuestro programa?</h2>
                    <p>
                        Tu historia puede inspirar a otros que están luchando. 
                        Comparte tu testimonio de transformación y esperanza.
                    </p>
                </div>
                <div class="cta-actions">
                    <button class="btn-primary" data-toggle="modal" data-target="#testimonioSubmitModal">
                        <i class="fas fa-heart"></i>
                        Compartir mi testimonio
                    </button>
                    <a href="#contacto" class="btn-outline">
                        <i class="fas fa-phone"></i>
                        Hablar con un consejero
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para testimonio completo -->
<div class="modal fade" id="testimonioModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Testimonio completo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="testimonioCompleto">
                    <!-- El contenido se carga dinámicamente via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn-secondary" id="shareTestimonio">
                    <i class="fas fa-share"></i>
                    Compartir
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para enviar nuevo testimonio -->
<div class="modal fade" id="testimonioSubmitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Compartir tu testimonio</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testimonioSubmitForm" class="testimonio-form">
                    <div class="form-step active" data-step="1">
                        <h6>Información básica</h6>
                        
                        <div class="form-group">
                            <label for="nombreCompleto">Nombre completo *</label>
                            <input type="text" id="nombreCompleto" name="nombreCompleto" required>
                            <small>Solo para verificación. No se publicará.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="nombrePublico">¿Cómo quieres aparecer? *</label>
                            <select id="nombrePublico" name="nombrePublico" required>
                                <option value="">Seleccionar</option>
                                <option value="nombre_inicial">Solo nombre + inicial apellido</option>
                                <option value="iniciales">Solo iniciales</option>
                                <option value="anonimo">Anónimo</option>
                                <option value="nombre_completo">Nombre completo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="programaCompletado">Programa completado *</label>
                            <select id="programaCompletado" name="programaCompletado" required>
                                <option value="">Seleccionar</option>
                                <option value="residencial">Programa Residencial</option>
                                <option value="ambulatorio">Programa Ambulatorio</option>
                                <option value="familiar">Programa Familiar</option>
                                <option value="seguimiento">Programa de Seguimiento</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="anoGraduacion">Año de graduación *</label>
                                <select id="anoGraduacion" name="anoGraduacion" required>
                                    <option value="">Seleccionar</option>
                                    <?php for ($year = date('Y'); $year >= (date('Y') - 20); $year--) : ?>
                                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tiempoSobriedad">Tiempo en recuperación *</label>
                                <select id="tiempoSobriedad" name="tiempoSobriedad" required>
                                    <option value="">Seleccionar</option>
                                    <option value="6 meses">6 meses</option>
                                    <option value="1 año">1 año</option>
                                    <option value="2 años">2 años</option>
                                    <option value="3 años">3 años</option>
                                    <option value="5 años">5 años</option>
                                    <option value="10+ años">10+ años</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-step" data-step="2">
                        <h6>Tu testimonio</h6>
                        
                        <div class="form-group">
                            <label for="testimonioTexto">Comparte tu historia *</label>
                            <textarea id="testimonioTexto" name="testimonioTexto" rows="10" required
                                placeholder="Cuéntanos sobre tu experiencia en la granja, cómo llegaste, qué aprendiste, cómo cambió tu vida..."></textarea>
                            <small>Evita detalles específicos sobre drogas o actividades ilegales. Enfócate en la transformación y esperanza.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="versiculo">Versículo bíblico favorito</label>
                            <input type="text" id="versiculo" name="versiculo" 
                                placeholder="Ej: Jeremías 29:11">
                        </div>
                        
                        <div class="form-group">
                            <label for="mensaje">Mensaje para otros en lucha</label>
                            <textarea id="mensaje" name="mensaje" rows="3"
                                placeholder="¿Qué le dirías a alguien que está pasando por lo que tú pasaste?"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-step" data-step="3">
                        <h6>Permisos y privacidad</h6>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="mostrarFoto" id="mostrarFoto">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    <strong>Incluir mi foto</strong><br>
                                    <small>Opcional. Solo se mostrará si das tu consentimiento explícito.</small>
                                </span>
                            </label>
                        </div>
                        
                        <div class="form-group" id="fotoUpload" style="display: none;">
                            <label for="fotoTestimonio">Subir foto</label>
                            <input type="file" id="fotoTestimonio" name="fotoTestimonio" accept="image/*">
                            <small>La foto será moderada antes de publicarse.</small>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="consentimientoPublicacion" id="consentimientoPublicacion" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    <strong>Acepto que mi testimonio sea publicado</strong><br>
                                    <small>Entiendo que será revisado antes de la publicación y que puedo solicitar su remoción en cualquier momento.</small>
                                </span>
                            </label>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="contactoFuturo" id="contactoFuturo">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">
                                    <strong>Acepto ser contactado</strong><br>
                                    <small>Para participar en eventos, mentorías o seguimiento (opcional).</small>
                                </span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label for="emailContacto">Email de contacto</label>
                            <input type="email" id="emailContacto" name="emailContacto">
                            <small>Solo para comunicación interna. No se publicará.</small>
                        </div>
                    </div>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn-outline" id="prevStep" style="display: none;">
                            <i class="fas fa-arrow-left"></i>
                            Anterior
                        </button>
                        <button type="button" class="btn-primary" id="nextStep">
                            Siguiente
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn-success" id="submitTestimonio" style="display: none;">
                            <i class="fas fa-heart"></i>
                            Enviar testimonio
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para la página de testimonios */
.testimonials-page {
    margin-top: 80px; /* Offset del header fijo */
}

.testimonials-hero {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 4rem 0;
    position: relative;
}

.testimonials-hero .highlight {
    background: linear-gradient(135deg, #2563eb, #059669);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-stats {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
    justify-content: center;
}

.stat {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: 700;
    color: #2563eb;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
}

.testimonials-filters {
    background: white;
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.filters-container {
    text-align: center;
}

.filter-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.filter-btn {
    padding: 0.5rem 1rem;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 2rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover,
.filter-btn.active {
    border-color: #2563eb;
    background: #2563eb;
    color: white;
}

.testimonials-grid {
    padding: 3rem 0;
}

.testimonials-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.testimonio-card {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.testimonio-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.testimonio-foto img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 1rem;
}

.testimonio-avatar {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 50%;
    margin-bottom: 1rem;
}

.testimonio-avatar i {
    font-size: 2rem;
    color: #9ca3af;
}

.testimonio-quote {
    font-style: italic;
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.author-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.author-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.author-details span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.autor-details i {
    color: #2563eb;
}

.btn-leer-mas {
    width: 100%;
    padding: 0.75rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    color: #2563eb;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 1rem 0;
}

.btn-leer-mas:hover {
    background: #2563eb;
    color: white;
}

.testimonio-tags {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.tag {
    padding: 0.25rem 0.75rem;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.tag.familia {
    background: #ecfdf5;
    color: #059669;
}

.submit-testimonio-cta {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.cta-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.no-testimonios {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.no-testimonios i {
    font-size: 3rem;
    color: #2563eb;
    margin-bottom: 1rem;
}

/* Form Steps */
.form-step {
    display: none;
}

.form-step.active {
    display: block;
}

.form-navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .filter-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .testimonials-container {
        grid-template-columns: 1fr;
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Filtros de testimonios
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        
        // Actualizar botón activo
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filtrar testimonios
        if (filter === 'all') {
            $('.testimonio-card').show();
        } else {
            $('.testimonio-card').hide();
            $(`.testimonio-card.${filter}`).show();
        }
    });
    
    // Modal de testimonio completo
    $('.btn-leer-mas').on('click', function() {
        const testimonioId = $(this).data('testimonio-id');
        
        // Cargar testimonio completo via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'cargar_testimonio_completo',
                testimonio_id: testimonioId,
                nonce: '<?php echo wp_create_nonce("testimonio_nonce"); ?>'
            },
            success: function(response) {
                $('#testimonioCompleto').html(response);
            },
            error: function() {
                $('#testimonioCompleto').html('<p>Error cargando el testimonio.</p>');
            }
        });
    });
    
    // Form steps para envío de testimonio
    let currentStep = 1;
    const totalSteps = 3;
    
    $('#nextStep').on('click', function() {
        if (validateCurrentStep()) {
            currentStep++;
            updateFormStep();
        }
    });
    
    $('#prevStep').on('click', function() {
        currentStep--;
        updateFormStep();
    });
    
    function updateFormStep() {
        // Ocultar todos los steps
        $('.form-step').removeClass('active');
        
        // Mostrar step actual
        $(`.form-step[data-step="${currentStep}"]`).addClass('active');
        
        // Actualizar botones
        $('#prevStep').toggle(currentStep > 1);
        $('#nextStep').toggle(currentStep < totalSteps);
        $('#submitTestimonio').toggle(currentStep === totalSteps);
    }
    
    function validateCurrentStep() {
        const currentStepElement = $(`.form-step[data-step="${currentStep}"]`);
        const requiredFields = currentStepElement.find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).addClass('error');
                isValid = false;
            } else {
                $(this).removeClass('error');
            }
        });
        
        return isValid;
    }
    
    // Toggle foto upload
    $('#mostrarFoto').on('change', function() {
        $('#fotoUpload').toggle($(this).is(':checked'));
    });
    
    // Envío del formulario
    $('#testimonioSubmitForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        const formData = new FormData(this);
        formData.append('action', 'enviar_testimonio');
        formData.append('nonce', '<?php echo wp_create_nonce("enviar_testimonio_nonce"); ?>');
        
        // Mostrar loading
        $('#submitTestimonio').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('¡Gracias por compartir tu testimonio! Lo revisaremos y te contactaremos pronto.');
                    $('#testimonioSubmitModal').modal('hide');
                    $('#testimonioSubmitForm')[0].reset();
                    currentStep = 1;
                    updateFormStep();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('Error enviando el testimonio. Por favor intenta nuevamente.');
            },
            complete: function() {
                $('#submitTestimonio').prop('disabled', false).html('<i class="fas fa-heart"></i> Enviar testimonio');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
