<?php defined('ABSPATH') or die;
/**
 * Proveedor de datos de formulario para procesar peticiones de registro, inicio de sesión y perfil de inversores
 * 
 * Incluye funciones de validación de datos del form e importación directa desde los eventos generados
 * por la entrada de inputs GET y POST
 * 
 * Este elemento debería servir únicamente para el frontend,
 * pero puede que interese para visualizar elementos del back-end también
 */
class AirBoxFormModel extends AirBoxDictionary implements AirBoxIModel{
    
    private $_name = null;
    
    protected function __construct(array $dataSet = null) {
        parent::__construct($dataSet);
    }
    /**
     * Crea un nuevo campo de formulario
     * @param string $name
     * @param string $type
     * @param array $properties
     */
    public final function addFormField($name, $type = parent::FIELD_TYPE_TEXT, array $properties = null){
        $this->add($name, $type, $properties);
    }
    /**
     * Crea un form de datos
     * @param type $name
     * @param array $formData
     * @return \AirBoxFormModel
     */
    public static final function CreateForm( $name ){
        
        $form = new AirBoxFormModel( );
        
        $form->_name = $name;
        
        return $form;
    }
    /**
     * Importa los valores desde los datos adjuntos del evento
     * @param AirBoxEventModel $e
     */
    public final function importFromEvent( AirBoxEventModel $e ){
        foreach( $this->getDictionary() as $definition ){
            if( !is_null( $value = $e->get($definition['name'])) ){
                if(!$this->setValue($definition['name'], $value)){
                    //print_r($definition['name'].' no establecido');
                }
            }
        }
    }
    /**
     * Importa los valores de un modelo de diccionario existente
     * @param AirBoxDictionary $model
     * @return int Contador de valores actualizados
     */
    public final function importFromModel( AirBoxDictionary $model ){
        
        $modelDef = $model->getDictionary();
        
        $counter = 0;
        
        foreach( $this->getDictionary() as $name => $definition ){
            if( isset($modelDef[$name])
                    && $modelDef[$name]['type'] === $definition['type']
                    && !is_null($modelDef[$name]['value']) ){
                
                $this->setValue($name, $modelDef[$name]['value']);
                
                $counter++;
            }
        }
        
        return $counter;
    }
    /**
     * @return array Lista de campos por tipo y valor
     */
    public final function getFormFields(){ return $this->getDictionary(); }
    /**
     * Valida los datos del formulario retornando TRUE si el form ha superado la validación, FALSE si hay errores que revisar
     * @return boolean Estado de la validación
     * 
     * Si se causa una excepción el resto de probables errores no se registrará en el formulario y el método
     * será automáticamente interrumpido
     */
    public final function validateFormData( ){
        
        $success = true;
        
        foreach( $this->getDictionary() as $definition ){
            if( $definition['required'] ){
                switch( $definition['type']){
                    //case AirBoxDictionary::FIELD_TYPE_NUMBER:
                    //    break;
                    case AirBoxDictionary::FIELD_TYPE_EMAIL:
                        if( !isset($definition['value']) || strlen($definition['value']) === 0 ){
                            
                            $error = AirBoxStringModel::__(
                                    AirBoxStringModel::LBL_ERROR_INVALID_VALUE );
                            
                            $this->setMeta($definition['name'], 'error', $error );
                            
                            AirBoxNotifyModel::RegisterLog($error, AirBoxNotifyModel::LOG_TYPE_ERROR);

                            $success = false;
                        }
                        else{
                            //validar email
                            $at = strrpos($definition['value'], '@');
                            $dot = strrpos($definition['value'], '.');
                            //hay una @ en alguna posición superior a 0 y luego hay un punto para definir el dominio
                            if( $at < 1 || $at > $dot ){
                                
                                $error = AirBoxStringModel::__(
                                        AirBoxStringModel::LBL_ERROR_INVALID_EMAIL );
                                
                                $this->setMeta($definition['name'], 'error', $error );
                                
                                AirBoxNotifyModel::RegisterLog($error, AirBoxNotifyModel::LOG_TYPE_ERROR);

                                $success = false;
                            }
                        }
                        break;
                    default:
                        if( !isset($definition['value']) || strlen($definition['value']) === 0 ){

                            $error = AirBoxStringModel::__(
                                    AirBoxStringModel::LBL_ERROR_INVALID_VALUE );
                            
                            $this->setMeta($definition['name'], 'error', $error );
                            
                            AirBoxNotifyModel::RegisterLog($error, AirBoxNotifyModel::LOG_TYPE_ERROR);
                            
                            $success = false;
                        }
                        break;
                }
            }
        }
        
        return $success;
    }
    /**
     * @return array Lista los errores del formulario, si no hay, retorna una lista vacía
     */
    public final function listErrors(){
        $error_list = array();
        foreach($this->getDictionary() as $field => $definition ){
            if( isset( $definition['error'] ) ){
                $error_list[$field] = $definition['error'];
            }
        }
        return $error_list;
    }
    /**
     * Devuelve el nombre o etiqueta del campo solicitado
     * @param string $field
     * @return string
     */
    public final function getLabel($field){
        return $this->getMeta($field, 'label' ,'');
    }
    /**
     * @return String Nombre del form
     */
    public final function getName(){ return $this->_name; }
}