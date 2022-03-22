<?php defined('ABSPATH') or die;
/**
 * Widget de inicio de sesión en la intranet
 */
class AirBoxFormWidget extends WP_Widget implements AirBoxIWidget{
    
    const FORM_TYPE_LOGIN = 'login';
    
    const FORM_TYPE_AFFILIATE = 'affiliate';
    
    const WIDGET_META_TITLE = 'title';
    
    const WIDGET_META_VIEW = 'view';
    
    const WIDGET_META_ID = 'coinbox_form';
    
    private static $_formType = array(
        self::FORM_TYPE_AFFILIATE => 'Formulario de afiliaci&oacute;n',
        self::FORM_TYPE_LOGIN => 'Formulario de inicio de sesi&oacute;n'
    );
    
    function __construct( ) {
        parent::__construct(
                self::WIDGET_META_ID,
                AirBoxStringModel::__( AirBoxStringModel::LBL_WIDGET_COINBOX_FORM ),
                array('description' => AirBoxStringModel::__(
                    AirBoxStringModel::LBL_WIDGET_COINBOX_FORM_DESC )));
    }
    
    /**
     * Inicialización y proceso del widget
     */
    function AirBoxFormWidget(){
        $widget_ops = array(
            'classname' => self::WIDGET_META_ID,
            'description' => AirBoxStringModel::__( AirBoxStringModel::LBL_WIDGET_COINBOX_FORM ) );
        
        parent::WP_Widget(
                self::WIDGET_META_ID,
                AirBoxStringModel::__( AirBoxStringModel::LBL_WIDGET_COINBOX_FORM ),
                $widget_ops);

    }
    /**
     * 
     * @param type $instance
     */
    function form($instance) {
        
        $title = $instance[self::WIDGET_META_TITLE];
        
        $view = $instance[self::WIDGET_META_VIEW];
        
        echo $this->renderTitleInput($title);
        
        echo $this->renderSelectInput($view);
        
    }
    /**
     * Genera la lista html de selección de vista
     * @param string $selected
     * @return HTML
     */
    private final function renderSelectInput( $selected ){

        $html = sprintf('<p class="form-select"><select class="widefat" id="%s" name="%s">',
                $this->get_field_id(self::WIDGET_META_VIEW),
                $this->get_field_name(self::WIDGET_META_VIEW));
        
        foreach( self::$_formType as $value=>$label ){
            if( $value === $selected ){
                $html .= sprintf('<option value="%s" selected="selected">%s</option>',
                        $value, AirBoxStringModel::__($label));
            }
            else{
                $html .= sprintf('<option value="%s">%s</option>',
                        $value, AirBoxStringModel::__($label));
            }
        }
        
        return $html . '</select></p>';
    }
    /**
     * Genera la vista html del input de título
     * @param string $title
     * @return HTML
     */
    private final function renderTitleInput( $title ){
        return sprintf(
                '<p class="title"><input class="widefat" type="text" id="%s" name="%s" value="%s"/></p>',
                $this->get_field_id(self::WIDGET_META_TITLE),
                $this->get_field_name(self::WIDGET_META_TITLE),
                $title);
    }
    /**
     * 
     * @param type $new_instance
     * @param type $old_instance
     * @return type
     */
    function update($new_instance, $old_instance) {
        
        $instance = $old_instance;
        
        $instance[self::WIDGET_META_TITLE] = strip_tags($new_instance[self::WIDGET_META_TITLE]);
        
        $instance[self::WIDGET_META_VIEW] = $new_instance[self::WIDGET_META_VIEW];
        
        return $instance;
    }
    /**
     * 
     * @param type $args
     * @param type $instance
     */
    function widget($args, $instance) {
        
        extract($args);
        
        $available_units = AirBox::cache(AirBoxCacheModel::CACHE_GLOBAL_AVAILABLE,0);
        
        $form = $instance[self::WIDGET_META_VIEW];
        
        switch( $form ){
            case 'affiliate':
                //cargar script solo para form de afiliación con control de boxes por JS
                //wp_enqueue_script( 'coinbox-register-form',AirBoxRouter::Asset( 'register-form.js' ) , 'jquery');
                break;
        }
        
        $title = $instance[self::WIDGET_META_TITLE];
        
        $html = sprintf('%shtml/%s_form.html.php',CODERS__COINBOX__DIR,
                !empty($form) ? $form : self::FORM_TYPE_LOGIN );
        
        if (!empty($title)){
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo $args['before_widget'];
        
        
        if( file_exists($html) ){
            
            if( $form === self::FORM_TYPE_LOGIN || $available_units > 0 ){
                echo sprintf('<form name="%s_%s" method="post" action="%s">', self::WIDGET_META_ID, $form, AirBoxRouter::RoutePublic());

                    require $html;

                    echo '</form>';
            }
            else{
                echo sprintf('<div class="project-finished">%s</div>',
                        AirBoxStringModel::__(AirBoxStringModel::LBL_INFO_PROJECT_FINISHED));
            }
        }
        else{
            echo sprintf('<p>%s: %s</p>',
                    AirBoxStringModel::__(AirBoxStringModel::LBL_ERROR_VIEW_NOT_FOUND), $html);
        }

        echo $args['after_widget'];
    }
    /**
     * Extrae información de la caché de la vista o del plugin
     * @param name $var
     * @param mixed $default
     * @return mixed
     */
    public function get_data($var, $default = null) {
        
        $method = sprintf( 'get_%s_data', $var );
        
        return method_exists($this, $method) ? $this->$method() : $default;
    }
}