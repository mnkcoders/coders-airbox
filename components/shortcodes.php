<?php defined('ABSPATH') or die;
/**
 * Genera una barra de progreso del progreso global/ciclo actual
 * 
 * * global
 * 
 * * milestone
 * 
 * @param array $atts
 * @return HTML
 */
function coinbox_progressbar_shortcode( $atts ) {
    
    $a = shortcode_atts( array(
        //'label' => AirBoxStringModel::__('Progreso'),
        'data' => 'global',
        'mode' => 'progress',
    ), $atts );
    
    $display = $a['mode'];
    
    $label = isset( $a['label']) ? sprintf('<label>%s</label>',$a['label']) : '';
    
    switch( $a['data'] ){
        case AirBoxCacheModel::CACHE_MILESTONE_CURRENT:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_COUNT,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT,0);
        case AirBoxCacheModel::CACHE_MILESTONE_UNITS:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_OWNED,0);
            break;
        case AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE,0);
            break;
        case AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
            break;
        case AirBoxCacheModel::CACHE_GLOBAL_UNITS:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
            break;
        default:
            $total = 0;
            $counter = 0;
            break;
    }

    return sprintf( '<div class="coinbox-shortcode progress-bar">%s%s</div>', $label,
            AirBoxRenderer::renderProgressBar( 'coinbox_progress_shortcode', $total, $counter, $display ) );
}
/**
 * Genera una barra de progreso radial del progreso global/ciclo actual
 * 
 * -label: etiqueta a mostrar (debajo/fuera del circulo)
 * -data: tipo de origen de datos a mostrar. Ver constantes de caché para mas detalles
 * -mode: modo del display: units, progress, percent
 * 
 * @param array $atts
 * @return HTML
 */
function coinbox_radialprogressbar_shortcode( $atts ) {
    
    $a = shortcode_atts( array(
        'label' => AirBoxStringModel::__('Progreso'),
        'data' => 'global',
        'mode' => 'units',
    ), $atts );
    
    $display = $a['mode'];
    
    switch( $a['data'] ){
        case AirBoxCacheModel::CACHE_MILESTONE_CURRENT:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_COUNT,0);
        case AirBoxCacheModel::CACHE_MILESTONE_UNITS:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_OWNED,0);
            break;
        case AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE,0);
            break;
        case AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
            break;
        case AirBoxCacheModel::CACHE_GLOBAL_UNITS:
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
            break;
        default:
            $total = 0;
            $counter = 0;
            break;
    }
    
    $label = isset( $a['label'] ) ? sprintf('<label>%s</label>',$a['label']) : '';

    return sprintf( '<div class="coinbox-shortcode radial-progress">%s<label>%s</label></div>',
            AirBoxRenderer::renderRadialProgress( 'coinbox_progress_shortcode',
                    $total, $counter, $display ), $label );
}
/**
 * Contador de unidades N
 *
 * * investors
 * * available: Unidades globales disponibles
 * * owned: Unidades globales en propiedad
 * * completed: Unidades de los objetivos completados
 * * units: Unidades globales totales del proyecto
 * * milestone_remaining: Unidades faltantes para completar el objetivo
 * * milestone_completed: Unidades completadas en el objetivo
 * * milestone_units: Unidades totales del objetivo
 *
 * @param array $atts
 * @return HTML
 */
function coinbox_units_shortcode( $atts ){
    
    $a = shortcode_atts( array(
        //'label' => AirBoxStringModel::__('Unidades' ),
        'data' => 'available',
        //'data' => 'completed',
    ), $atts );
    
    $label = isset( $a['label'] ) ?
            sprintf('<label>%s</label>',$a['label']) : '';
    
    switch( $a['data'] ){
        case AirBoxCacheModel::CACHE_GLOBAL_INVESTORS:
        case AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE:
        case AirBoxCacheModel::CACHE_GLOBAL_OWNED:
        case AirBoxCacheModel::CACHE_GLOBAL_COMPLETED:
        case AirBoxCacheModel::CACHE_MILESTONE_UNITS:
        case AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE:
        case AirBoxCacheModel::CACHE_MILESTONE_OWNED:
        case AirBoxCacheModel::CACHE_MILESTONE_COUNT:
            $counter = AirBox::cache($a['data'],0);
            return sprintf(
                '<span class="coinbox-shortcode units-counter"><span class="units">%s</span>%s</span>',
                $counter, $label );
        default:
            return '#';
    }
}
/**
 * Progreso de unidades A / B
 *
 * * global_available: Unidades globales disponibles
 * * global_progress: Unidades globales adquiridas
 * * milestone_available: Unidades disponibles en el ciclo actual
 * * milestone_progress: Unidades adquiridas en el ciclo actual
 * * milestone_current: Ciclo actual
 *
 * @param array $atts
 * @return HTML
 */
function coinbox_progress_shortcode( $atts ){
    
    $a = shortcode_atts( array(
        //'label' => AirBoxStringModel::__('Unidades' ),
        'data' => AirBoxCacheModel::CACHE_GLOBAL_PROGRESS,
    ), $atts );
    
    $counter = 0;
    $total = 0;
    
    $label = isset( $a['label'] ) ?
            sprintf('<label>%s</label>',$a['label']) : '';

    switch( $a['data'] ){
        case AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            break;
        case AirBoxCacheModel::CACHE_GLOBAL_UNITS:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_OWNED,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_UNITS,0);
            break;
        case AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_AVAILABLE,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            break;
        case AirBoxCacheModel::CACHE_MILESTONE_UNITS:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_OWNED,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_UNITS,0);
            break;
        case AirBoxCacheModel::CACHE_MILESTONE_CURRENT:
            $counter = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_CURRENT,0);
            $total = AirBox::cache(AirBoxCacheModel::CACHE_MILESTONE_COUNT,0);
        default:
            break;
    }
    
    return ( $counter + $total > 0 ) ?  sprintf(
        '<span class="coinbox-shortcode progress-counter"><span class="progress">%s<span class="separator"></span>%s</span>%s</span>',
        $a['data'], $counter, $total, $label ) : '';
}
add_shortcode( 'coinbox_progressbar', 'coinbox_progressbar_shortcode' );
add_shortcode( 'coinbox_radialprogressbar', 'coinbox_radialprogressbar_shortcode' );
add_shortcode( 'coinbox_units', 'coinbox_units_shortcode' );
add_shortcode( 'coinbox_progress', 'coinbox_progress_shortcode' );
