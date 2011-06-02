<?php
if (!defined('XOOPS_ROOT_PATH')) die('Root path not defined');
// Module Info

// The name and description of module
define('_MI_GWLOTO_NAME', 'gwloto');
define('_MI_GWLOTO_DESC', 'geekwright Lock Out Tag Out');

// Admin Menu
define('_MI_GWLOTO_ADMENU', 'gwloto Centro');
define('_MI_GWLOTO_ADMENU_ABOUT', 'Acerca');
define('_MI_GWLOTO_ADMENU_PLACE', 'Lugares de nivel superior');
define('_MI_GWLOTO_ADMENU_LANG', 'Idiomas');
define('_MI_GWLOTO_ADMENU_PLUGINS', 'Plugins');
define('_MI_GWLOTO_ADMENU_PREF', 'Preferencias');
define('_MI_GWLOTO_ADMENU_GOMOD', 'Ir al Módulo');

// Admin Root Places
define('_MI_GWLOTO_AD_PLACE_FORMNAME', 'Agregar un sitio de nivel superior');
define('_MI_GWLOTO_AD_PLACE_ID', 'Lugar ID');
define('_MI_GWLOTO_AD_PLACE_NAME', 'Nombre Lugar de nivel superior');
define('_MI_GWLOTO_AD_PLACE_ADMIN', 'El administrador de este Lugar');
define('_MI_GWLOTO_AD_PLACE_EDIT_BUTTON', 'Agregar Lugar');
define('_MI_GWLOTO_AD_PLACE_EDIT_CAPTION', 'Agregar Lugar con el Administrador');
define('_MI_GWLOTO_AD_PLACE_LISTNAME', 'Definido lugares de nivel superior');
define('_MI_GWLOTO_AD_PLACE_LISTEMPTY', 'No hay lugares definidos');
define('_MI_GWLOTO_AD_PLACE_ADD_OK', 'Lugar agregó.');
define('_MI_GWLOTO_AD_PLACE_ADD_ERR', 'No se pudo agregar Lugar.');
define('_MI_GWLOTO_AD_PLACE_ADD_ADMIN', 'Añadir Admin');
define('_MI_GWLOTO_AD_PLACE_ADD_ADMIN_OK', 'Admin agregó');
define('_MI_GWLOTO_AD_PLACE_ADD_ADMIN_ERR', 'No se pudo agregar admin');

// Admin Plugins
define('_MI_GWLOTO_AD_PLUGINS_FORMNAME', 'Plugins disponibles');
define('_MI_GWLOTO_AD_PLUGINS_TYPE', 'Clasificar');
define('_MI_GWLOTO_AD_PLUGINS_FILE', 'Nombre de archivo');
define('_MI_GWLOTO_AD_PLUGINS_NAME', 'Nombre');
define('_MI_GWLOTO_AD_PLUGINS_DESC', 'Descripción');
define('_MI_GWLOTO_AD_PLUGINS_STATUS', 'Estado');
define('_MI_GWLOTO_AD_PLUGINS_INSTALLED', 'Instalado');
define('_MI_GWLOTO_AD_PLUGINS_NOTINSTALLED', '');
define('_MI_GWLOTO_AD_PLUGINS_ACTION', 'Acción');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_ADD', 'Instale');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_DEL', 'Quitar');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_EDIT', 'Editar');
define('_MI_GWLOTO_AD_PLUGINS_ACTION_SORT', 'Reordenar Plugins');
define('_MI_GWLOTO_AD_PLUGINS_ADD_OK', 'Plugin agregó');
define('_MI_GWLOTO_AD_PLUGINS_ADD_ERR', 'No se pudo agregar el plugin');
define('_MI_GWLOTO_AD_PLUGINS_DEL_OK', 'Plugin eliminado');
define('_MI_GWLOTO_AD_PLUGINS_DEL_ERR', 'No se pudo eliminar el plugin');
define('_MI_GWLOTO_AD_PLUGINS_LISTEMPTY', 'No se han encontrado plugins');
define('_MI_GWLOTO_AD_PLUGINS_DEL_CONFIRM', 'Desinstalar este plugin?');
define('_MI_GWLOTO_AD_PLUGINS_INVALID', 'No se pudo cargar el archivo como plugin.');


// Admin Languages
define('_MI_GWLOTO_AD_LANG_FORMNAME', 'Añadir un idioma');
define('_MI_GWLOTO_AD_LANG_ID', 'Idioma ID');
define('_MI_GWLOTO_AD_LANG_NAME', 'Idioma Leyenda');
define('_MI_GWLOTO_AD_LANG_CODE', 'Código ISO 639-1');
define('_MI_GWLOTO_AD_LANG_FOLDER', 'Carpeta');
define('_MI_GWLOTO_AD_LANG_ADD_BUTTON', 'Agregar');
define('_MI_GWLOTO_AD_LANG_ADD_CAPTION', 'Agregar idioma');
define('_MI_GWLOTO_AD_LANG_LISTNAME', 'Idiomas definidos');
define('_MI_GWLOTO_AD_LANG_LISTEMPTY', 'No idiomas definidos');
define('_MI_GWLOTO_AD_LANG_ADD_OK', 'Idioma agregó');
define('_MI_GWLOTO_AD_LANG_ADD_ERR', 'No se pudo agregar el idioma');
define('_MI_GWLOTO_AD_LANG_FORMNAME_EDIT', 'Actualización del Idioma');
define('_MI_GWLOTO_AD_LANG_UPDATE_BUTTON', 'Actualizar');
define('_MI_GWLOTO_AD_LANG_UPDATE_CAPTION', 'Actualización del Idioma');
define('_MI_GWLOTO_AD_LANG_EDIT_BUTTON', 'Editar');
define('_MI_GWLOTO_AD_LANG_EDIT_OK', 'Idioma actualizado');
define('_MI_GWLOTO_AD_LANG_EDIT_ERR', 'No se pudo actualizar el idioma');

// todo list messages
define('_MI_GWLOTO_AD_TODO_TITLE', 'Acción necesarias');
define('_MI_GWLOTO_AD_TODO_ACTION', 'Acción');
define('_MI_GWLOTO_AD_TODO_MESSAGE', 'Mensaje');
define('_MI_GWLOTO_AD_TODO_PLACES', 'Debe agregar al menos un lugar de primer nivel.');
define('_MI_GWLOTO_AD_TODO_MYSQL', 'versión de MySQL %1$s o superior se requiere. (Detectado=%2$s)');
define('_MI_GWLOTO_AD_TODO_UPGRADE', 'Una versión más reciente de '._MI_GWLOTO_NAME.' está disponible (La versión instalada es %1$s, la versión actual es %2$s)');
define('_MI_GWLOTO_AD_TODO_UPLOAD', 'La subida ruta %1$s no se puede escribir. Compruebe que existe y los permisos son correctos.');
define('_MI_GWLOTO_AD_TODO_TCPDF_NOTFND', 'TCPDF no se encontró en el lugar especificado en las preferencias del módulo. Por favor, corrija la configuración de las preferencias.');
define('_MI_GWLOTO_AD_TODO_TCPDF_INSTALL', 'TCPDF no fue encontrada. Si se instala y no se detecta automáticamente, por favor, especifique la ubicación en las preferencias del módulo.');
define('_MI_GWLOTO_AD_TODO_TCPDF_UPGRADE', 'La versión TCPDF localizado puede quedar obsoleta, y puede resultar en problemas visibles en la salida de algunos plugins. Considere la instalación de la versión actual de los mejores resultados.');
define('_MI_GWLOTO_AD_TODO_TCPDF_GENERAL', '<br /><br />TCPDF es una clase PHP para generar documentos PDF y se requiere para la mayoría de plug-ins se incluyen para operar. Para obtener más información sobre TCPDF consulte  <a href="http://wwww.tcpdf.org/">www.tcpdf.org</a>.<br /><br />TCPDF se detecta automáticamente si está instalado en el directorio que se muestra aquí:<br />%s');

define('_MI_GWLOTO_AD_TODO_RETRY', 'Inténtelo de nuevo');
define('_MI_GWLOTO_AD_TODO_FIX', 'Trate de arreglar');
define('_MI_GWLOTO_AD_TODO_FIX_FAILED', 'No se pudo arreglar');

// config options
define('_MI_GWLOTO_CFG_MAXTAG', 'Número máximo de copias de etiquetas');
define('_MI_GWLOTO_CFG_MAXTAG_DSC', 'Número máximo de copias de etiquetas y bloqueos necesarios');

define ('_MI_GWLOTO_CFG_PREF_DATE', "Formato de fecha");
define ('_MI_GWLOTO_CFG_PREF_DATE_DSC', "Formato pasa a formatTimeStamp ()");

define ('_MI_GWLOTO_CFG_SHOW_RECON', 'Vuelva a conectar el uso fase');
define ('_MI_GWLOTO_CFG_SHOW_RECON_DSC', 'Mostrar vuelva a conectar las instrucciones y secuencias');
define ('_MI_GWLOTO_CFG_SHOW_INSPECT', 'Uso Inspeccione etapa');
define ('_MI_GWLOTO_CFG_SHOW_INSPECT_DSC', 'Mostrar las instrucciones de inspección y secuencias');

define('_MI_GWLOTO_CFG_JOB_REQUIRES','Los campos obligatorios en las entradas de trabajo');
define('_MI_GWLOTO_CFG_JOB_REQUIRES_DSC',"Lista separada por comas de los campos necesarios para Nuevo trabajo, Editar Trabajo y añadiendo el paso. Los valores posibles son: 'workorder', 'supervisor', 'startdate', 'enddate', 'description' and 'stepname'");

define('_MI_GWLOTO_CFG_PLAN_REQUIRES','Los campos obligatorios en el Plan de Control de las entradas');
define('_MI_GWLOTO_CFG_PLAN_REQUIRES_DSC',"Lista separada por comas de los campos necesarios para el Plan de Control de las entradas y puntos de control. Los valores posibles son: 'review', 'hazard_inventory', 'required_ppe', 'authorized_personnel', 'additional_requirements', 'disconnect_instructions', 'reconnect_instructions', 'inspection_instructions' and 'inspection_state'");

define('_MI_GWLOTO_CFG_MEDIA_PATH','Ruta de acceso a las cargas de archivos multimedia');
define('_MI_GWLOTO_CFG_MEDIA_PATH_DSC','Directorio donde los archivos multimedia se colocan cuando subido al servidor. Debe tener permisos de escritura por el servidor web.');

define('_MI_GWLOTO_CFG_MAX_MEDIA_SIZE','Número máximo de tamaño de archivos multimedia');
define('_MI_GWLOTO_CFG_MAX_MEDIA_SIZE_DSC','El tamaño máximo de archivo en bytes para que en la subida de archivos nuevos.');

// removed in 1.1
//define('_MI_GWLOTO_CFG_ENABLE_GOOGLE_TRANSLATE','Habilitar la traducción de Google');
//define('_MI_GWLOTO_CFG_ENABLE_GOOGLE_TRANSLATE_DSC','Esto permitirá acceso a las aplicaciones de Google API AJAX de idiomas para la traducción. Esto permite que las características adicionales para los usuarios con las autoridades de la traducción. Para conocer los términos y la información adicional, consulte: http://code.google.com/apis/ajaxlanguage/');

define('_MI_GWLOTO_CFG_TCPDF_PATH','Ruta de acceso a TCPDF');
define('_MI_GWLOTO_CFG_TCPDF_PATH_DSC','TCPDF se requiere para la mayoría de plugins estándar. Si se instala, pero no en un lugar que es detectada automáticamente, por favor, especifique la ruta completa (i.e. /www/libraries/tcpdf/tcpdf.php) ');

// Blocks
define('_MI_GWLOTO_ASSIGNED_BLOCK', 'Asignación de trabajo');
define('_MI_GWLOTO_ASSIGNED_BLOCK_DESC', 'Listas de trabajo activas etapas asignado al usuario actual.');

//new since 1.0
define('_MI_GWLOTO_CFG_ENABLE_TRANSLATE','Permitir la traducción');
define('_MI_GWLOTO_CFG_ENABLE_TRANSLATE_DSC','Esto permitirá el acceso AJAX traducción al API de Google Translate o Microsoft Translator API');
define('_MI_GWLOTO_CFG_ENABLE_TRANSLATE_OFF','Off');
define('_MI_GWLOTO_CFG_ENABLE_TRANSLATE_GOOGLE','Google');
define('_MI_GWLOTO_CFG_ENABLE_TRANSLATE_BING','Bing');

define('_MI_GWLOTO_CFG_TRANSLATE_KEY','Traducción clave API');
define('_MI_GWLOTO_CFG_TRANSLATE_KEY_DSC','Una clave de API puede ser necesario para acceder a los servicios tranalation AJAX. Por favor, consulte la documentación para obtener más información.');
?>