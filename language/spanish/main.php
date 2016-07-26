<?php
// Tenga en cuenta que la traducción al español de este módulo es incompleta y puede contener errores.
if (!defined('XOOPS_ROOT_PATH')) {
    die('Root path not defined');
}
define('_MD_GWLOTO_TITLE', 'geekwright Lockout Tagout');
define('_MD_GWLOTO_TITLE_SHORT', 'gwloto : '); // prepends to html page title

define('_MD_GWLOTO_TITLE_INDEX', 'Lugar navegador');
define('_MD_GWLOTO_TITLE_EDITAUTHS', 'Establecer las autoridades del usuario');
define('_MD_GWLOTO_TITLE_SELECT',   'Portapapeles de Acciones');

define('_MD_GWLOTO_TITLE_NEWJOB',   'Iniciar una nueva trabajo de control de energía');
define('_MD_GWLOTO_TITLE_PRINTJOB', 'Imprimir Energía de Control de Documentos de Trabajo');
define('_MD_GWLOTO_TITLE_VIEWJOB',  'Control de Energía de trabajo Detalle');
define('_MD_GWLOTO_TITLE_VIEWSTEP', 'Control de Energía de Trabajo Etapa Detalle');
define('_MD_GWLOTO_TITLE_LISTJOBS', 'Buscar trabajos de Control de Energía');

define('_MD_GWLOTO_TITLE_EDITPLACE', 'Lugares Editar');
define('_MD_GWLOTO_TITLE_NEWPLACE', 'Añadir un nuevo lugar');

define('_MD_GWLOTO_TITLE_VIEWPLAN', 'Ver Plan de Control de Energía');
define('_MD_GWLOTO_TITLE_EDITPLAN', 'Edición Plan de Control de Energía');
define('_MD_GWLOTO_TITLE_NEWPLAN',  'Inicio Nuevo Plan de Control de Energía');

define('_MD_GWLOTO_TITLE_VIEWPOINT', 'Ver la energía y control de puntos');
define('_MD_GWLOTO_TITLE_EDITPOINT', 'Punto de Edición Control de Energía');
define('_MD_GWLOTO_TITLE_NEWPOINT', 'Añadir nuevo punto de Control de Energía');
define('_MD_GWLOTO_TITLE_SORTPOINT', 'Reordenar Puntos de Control de Energía');

define('_MD_GWLOTO_TITLE_VIEWMEDIA', 'Archivo multimedia detalle');
define('_MD_GWLOTO_TITLE_NEWMEDIA', 'Añadir archivo multimedia');
define('_MD_GWLOTO_TITLE_LISTMEDIA', 'Lista de archivos multimedia');
define('_MD_GWLOTO_TITLE_ATTACHMEDIA', 'Adjuntar archivos multimedia');

define('_MD_GWLOTO_TITLE_SORTPLUGINS', 'Reordenar Plugins');
define('_MD_GWLOTO_TITLE_EDITPLUGIN', 'Editar Plugin');

define('_MD_GWLOTO_MSG_NO_ACCESS', 'Usted no tiene ningún acceso definido. Póngase en contacto con su supervisor.');
define('_MD_GWLOTO_MSG_ANON_ACCESS', 'Usted no está actualmente registrada, y sin acceso para los usuarios anónimos se define.');
define('_MD_GWLOTO_MSG_NO_AUTHORITY', 'Usted no tiene la autoridad necesaria para acceder a los recursos solicitados.');
define('_MD_GWLOTO_MSG_BAD_PARMS', 'Los parámetros no válidos para la operación.');
define('_MD_GWLOTO_MSG_BAD_TOKEN', 'Caducado o no válido token de seguridad en la solicitud.');
define('_MD_GWLOTO_MSG_NO_TRANSLATE_DEFAULT', 'Traducir la autoridad no puede ser usado para alterar versiones lengua materna.');

define('_MD_GWLOTO_ALL_AUTH_PLACES', 'Todos los lugares');

define('_MD_GWLOTO_LANG_TRAY', 'Idioma');
define('_MD_GWLOTO_LANG_CHANGE_BUTTON', 'Actualizar');
define('_MD_GWLOTO_LANG_TRANS_BUTTON', 'Traducir');

// user authority form
define('_MD_GWLOTO_USERAUTH_FORM', 'Establecer las autoridades del usuario');
define('_MD_GWLOTO_USERAUTH_USER', 'Seleccionar usuario');
define('_MD_GWLOTO_USERAUTH_DISPLAY', 'Mostrar las actuales autoridades');
define('_MD_GWLOTO_USERAUTH_AUTHS', 'Autoridades');
define('_MD_GWLOTO_USERAUTH_DISPLAY_BUTTON', 'Ver');
define('_MD_GWLOTO_USERAUTH_UPDATE', 'Guardar los cambios');
define('_MD_GWLOTO_USERAUTH_UPDATE_BUTTON', 'Guardar');
define('_MD_GWLOTO_USERAUTH_UPDATE_OK', 'Autoridades guardado. ');
define('_MD_GWLOTO_USERAUTH_DB_ERROR', 'No se pudo guardar las autoridades. ');

define('_MD_GWLOTO_USERAUTH_RPT_TITLE', 'Active las autoridades para el usuario por lugar');
define('_MD_GWLOTO_USERAUTH_RPT_PLACE', 'Lugar');
define('_MD_GWLOTO_USERAUTH_RPT_AUTHS', 'Autoridad');
define('_MD_GWLOTO_USERAUTH_RPT_NOAUTH', 'Ninguna autoridad');

define('_MD_GWLOTO_USERAUTH_EXIT', 'Salir del editor Autoridad');


define('_MD_GWLOTO_LASTCHG_BY', 'Modificado por');
define('_MD_GWLOTO_LASTCHG_ON', 'Tiempo de actualización');

// place form
define('_MD_GWLOTO_EDITPLACE_FORM', 'Editar lugar');

define('_MD_GWLOTO_EDITPLACE_NAME', 'Lugar Nombre');
define('_MD_GWLOTO_EDITPLACE_HAZARDS', 'Peligro de Inventario');
define('_MD_GWLOTO_EDITPLACE_PPE', 'Equipo de protección personal');

define('_MD_GWLOTO_EDITPLACE_UPDATE', 'Guardar los cambios');
define('_MD_GWLOTO_EDITPLACE_UPDATE_BUTTON', 'Guardar');
define('_MD_GWLOTO_EDITPLACE_UPDATE_OK', 'Lugar guardado. ');
define('_MD_GWLOTO_EDITPLACE_DB_ERROR', 'No se pudo guardar Lugar.');
define('_MD_GWLOTO_EDITPLACE_NOTFOUND', 'Lugar no se encuentra. ');

define('_MD_GWLOTO_PLACE_HAZARDS', 'Peligro de Inventario para %s');
define('_MD_GWLOTO_PLACE_PPE', 'Equipo de protección personal para %s');
define('_MD_GWLOTO_NO_PLACE_HAZARDS', '<i>(no se define)</i>');
define('_MD_GWLOTO_NO_PLACE_PPE', '<i>(no se define)</i>');
// new place form
define('_MD_GWLOTO_NEWPLACE_FORM', 'Añadir lugar con menos de %s');

define('_MD_GWLOTO_NEWPLACE_ADD_BUTTON_DSC', 'Añadir un nuevo lugar');
define('_MD_GWLOTO_NEWPLACE_ADD_BUTTON', 'Agregar');
define('_MD_GWLOTO_NEWPLACE_ADD_OK', 'Lugar agregó. ');
define('_MD_GWLOTO_NEWPLACE_DB_ERROR', 'No se pudo agregar Lugar. ');

// control plan form
define('_MD_GWLOTO_EDITPLAN_FORM', 'Edición Plan de Control');

define('_MD_GWLOTO_EDITPLAN_NAME', 'Nombre del Plan de Control');
define('_MD_GWLOTO_EDITPLAN_REVIEW', 'Comentarios / Aprobaciones');
define('_MD_GWLOTO_EDITPLAN_HAZARDS', 'Peligro de Inventario');
define('_MD_GWLOTO_EDITPLAN_PPE', 'Equipo de protección personal');
define('_MD_GWLOTO_EDITPLAN_AUTHPERSONNEL', 'Personal Requerido');
define('_MD_GWLOTO_EDITPLAN_ADDREQ', 'Requisitos adicionales');

define('_MD_GWLOTO_EDITPLAN_UPDATE', 'Guardar los cambios');
define('_MD_GWLOTO_EDITPLAN_UPDATE_BUTTON', 'Guardar');
define('_MD_GWLOTO_EDITPLAN_UPDATE_OK', 'Plan de guardado. ');
define('_MD_GWLOTO_EDITPLAN_DB_ERROR', 'No se pudo guardar el Plan. ');
define('_MD_GWLOTO_EDITPLAN_NOTFOUND', 'Plan no se encuentra. ');

define('_MD_GWLOTO_NEWPLAN_FORM', 'Añadir un nuevo Plan de Control de  %s');
define('_MD_GWLOTO_VIEWPLAN_FORM', 'Plan de Control');
define('_MD_GWLOTO_VIEWPLAN_COUNTS', 'Cuenta');
define('_MD_GWLOTO_VIEWPLAN_COUNTS_DETAIL', 'Puntos=%1$d Etiquetas=%2$d Bloqueos=%3$d ');
define('_MD_GWLOTO_VIEWPLAN_TRANSLATE_STATS', 'Estadísticas de Traducción ');
define('_MD_GWLOTO_VIEWPLAN_SEQ', 'Punto de secuencia');

define('_MD_GWLOTO_NEWPLAN_ADD_BUTTON_DSC', 'Añadir nuevo plan de control');
define('_MD_GWLOTO_NEWPLAN_ADD_BUTTON', 'Agregar');
define('_MD_GWLOTO_NEWPLAN_ADD_OK', 'Plan agregó. ');
define('_MD_GWLOTO_NEWPLAN_DB_ERROR', 'No se pudo agregar el Plan de Control. ');

define('_MD_GWLOTO_CPOINT_RPT_TITLE', 'Puntos de Control');
define('_MD_GWLOTO_CPOINT_RPT_NAME', 'Nombre del punto de');
define('_MD_GWLOTO_CPOINT_RPT_DISC_INST', 'Desconecte Instrucciones');
define('_MD_GWLOTO_CPOINT_RPT_DISC_STATE', 'Desconectado');
define('_MD_GWLOTO_CPOINT_RPT_LOCKS_REQ', 'Bloqueos');
define('_MD_GWLOTO_CPOINT_RPT_TAGS_REQ', 'Etiquetas');
define('_MD_GWLOTO_CPOINT_RPT_RECON_INST', 'Vuelva a conectar Instrucciones');
define('_MD_GWLOTO_CPOINT_RPT_RECON_STATE', 'Conectados');
define('_MD_GWLOTO_CPOINT_RPT_INSP_INST', 'Instrucciones de Inspección');

// Control Point Form
define('_MD_GWLOTO_NEWPOINT_FORM', 'Nuevo punto de control - %s');
define('_MD_GWLOTO_EDITPOINT_FORM', 'Editar puntos de control');
define('_MD_GWLOTO_EDITPOINT_NAME', 'Nombre del punto de');
define('_MD_GWLOTO_EDITPOINT_DISC_INST', 'Desconecte Instrucciones');
define('_MD_GWLOTO_EDITPOINT_DISC_STATE', 'Desconecte el estado');
define('_MD_GWLOTO_EDITPOINT_LOCKS_REQ', 'Bloqueos Requerido');
define('_MD_GWLOTO_EDITPOINT_TAGS_REQ', 'Número de copias Etiquetas');
define('_MD_GWLOTO_EDITPOINT_RECON_INST', 'Vuelva a conectar Instrucciones');
define('_MD_GWLOTO_EDITPOINT_RECON_STATE', 'Vuelva a conectar el estado');
define('_MD_GWLOTO_EDITPOINT_INSP_INST', 'Instrucciones de Inspección');
define('_MD_GWLOTO_EDITPOINT_INSP_STATE', 'Inspección de estado');

define('_MD_GWLOTO_NEWPOINT_ADD_BUTTON_DSC', 'Añadir nuevo punto de control');
define('_MD_GWLOTO_NEWPOINT_ADD_BUTTON', 'Agregar');
define('_MD_GWLOTO_NEWPOINT_ADD_OK', 'Punto de Control agregó.');
define('_MD_GWLOTO_NEWPOINT_DB_ERROR', 'No se pudo agregar puntos de control. ');

define('_MD_GWLOTO_VIEWPOINT_FORM', 'Punto de Control');

define('_MD_GWLOTO_EDITPOINT_UPDATE', 'Guardar los cambios');
define('_MD_GWLOTO_EDITPOINT_UPDATE_BUTTON', 'Guardar');
define('_MD_GWLOTO_EDITPOINT_UPDATE_OK', 'Punto de control guardado.');
define('_MD_GWLOTO_EDITPOINT_DB_ERROR', 'No se pudo guardar puntos de control.');
define('_MD_GWLOTO_EDITPOINT_NOTFOUND', 'Punto de Control no se encuentra. ');

// Job Forms
define('_MD_GWLOTO_JOB_NEW_FORM', 'Nuevo trabajo');
define('_MD_GWLOTO_JOB_EDIT_FORM', 'Edición de trabajo');
define('_MD_GWLOTO_JOB_VIEW_FORM', 'Detalles del trabajo');
define('_MD_GWLOTO_JOB_PRINT_FORM', 'Trabajo seleccionado para la impresión');

define('_MD_GWLOTO_JOB_ADD_BUTTON_DSC', 'Añadir nuevo trabajo');
define('_MD_GWLOTO_JOB_ADD_BUTTON', 'Agregar');
define('_MD_GWLOTO_JOB_ADD_OK', 'Trabajo agregó.');
define('_MD_GWLOTO_JOB_ADD_DB_ERROR', 'No se pudo agregar Trabajo. ');

define('_MD_GWLOTO_JOBSTEP_NEW_FORM', 'Añadir la etapa de trabajo');
define('_MD_GWLOTO_JOBSTEP_EDIT_FORM', 'Editar etapa de trabajo');
define('_MD_GWLOTO_JOBSTEP_VIEW_FORM', 'Detalles de Empleo Etapa');

define('_MD_GWLOTO_JOB_TOOL_TRAY_DSC', 'Herramientas de trabajo');
define('_MD_GWLOTO_JOB_EDIT_BUTTON', 'Guardar');
define('_MD_GWLOTO_JOB_EDIT_OK', 'Trabajo guardado.');
define('_MD_GWLOTO_JOB_EDIT_DB_ERROR', 'No se pudo guardar el trabajo.');

define('_MD_GWLOTO_JOBSTEP_TOOL_TRAY_DSC', 'Empleo herramientas etapa');
define('_MD_GWLOTO_JOBSTEP_EDIT_BUTTON', 'Guardar');
define('_MD_GWLOTO_JOBSTEP_ADD_BUTTON', 'Añadir Etapa');
define('_MD_GWLOTO_JOBSTEP_ADD_PICK_MSG', 'Busque el plan de control para añadir Trabajob');
define('_MD_GWLOTO_JOBSTEP_ADD_OK', 'Etapa de trabajo añadido.');
define('_MD_GWLOTO_JOBSTEP_ADD_DB_ERROR', 'No se pudo añadir la etapa de trabajo. ');
define('_MD_GWLOTO_JOBSTEP_EDIT_OK', 'Etapa de trabajo guardado.');
define('_MD_GWLOTO_JOBSTEP_EDIT_DB_ERROR', 'No se pudo guardar etapa de trabajo. ');
define('_MD_GWLOTO_JOBSTEP_DUPLICATE_PLAN', 'Plan de control seleccionado ya forma parte del trabajo.');

define('_MD_GWLOTO_JOB_NOTFOUND', 'Trabajo no se encuentra. ');
define('_MD_GWLOTO_JOB_NO_JOBS', 'Ningún trabajo que se encuentran.');
define('_MD_GWLOTO_JOB_STATUS_SELECT', 'Elija el estado del trabajo');
define('_MD_GWLOTO_JOB_STATUS_ALL', 'Todos los');
define('_MD_GWLOTO_JOB_SEARCH_BUTTON', 'Buscar');
define('_MD_GWLOTO_JOB_SEARCH_CRITERIA', 'Criterios de búsqueda:');

define('_MD_GWLOTO_JOB_RPT_TITLE', 'Disponible Trabajo');
define('_MD_GWLOTO_JOB_NAME', 'Trabajo');
define('_MD_GWLOTO_JOB_WORKORDER', 'Orden de trabajo');
define('_MD_GWLOTO_JOB_SUPERVISOR', 'Supervisor');
define('_MD_GWLOTO_JOB_PICKSUPER', 'Elija Supervisor');
define('_MD_GWLOTO_JOB_STARTDATE', 'Fecha prevista de inicio');
define('_MD_GWLOTO_JOB_ENDDATE', 'Prevista de terminación');
define('_MD_GWLOTO_JOB_DESCRIPTION', 'Descripción');

define('_MD_GWLOTO_JOB_STATUS', 'Condición del trabajo');
define('_MD_GWLOTO_JOB_STATUS_PLANNING', 'Planificador');
define('_MD_GWLOTO_JOB_STATUS_ACTIVE', 'Activo');
define('_MD_GWLOTO_JOB_STATUS_COMPLETE', 'Completo');
define('_MD_GWLOTO_JOB_STATUS_CANCELED', 'Cancelado');

define('_MD_GWLOTO_JOBSTEP_NAME', 'Etapa Nombre');
define('_MD_GWLOTO_JOBSTEP_ASSIGNED_UID', 'Asignado a');
define('_MD_GWLOTO_JOBSTEP_PLAN', 'Plan de Control');

define('_MD_GWLOTO_JOBSTEP_STATUS', 'Condición del trabajo etapa');
define('_MD_GWLOTO_JOBSTEP_STATUS_PLANNING', 'Planificador');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_DISC', 'Desconecte En Procesos');
define('_MD_GWLOTO_JOBSTEP_STATUS_DISC', 'Desconectado');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_RECON', 'Vuelva a conectar en los procesos');
define('_MD_GWLOTO_JOBSTEP_STATUS_RECON', 'Vuelve a conectar');
define('_MD_GWLOTO_JOBSTEP_STATUS_WIP_INSP', 'En los procesos de inspección');
define('_MD_GWLOTO_JOBSTEP_STATUS_INSP', 'Inspeccionados');
define('_MD_GWLOTO_JOBSTEP_STATUS_COMPLETE', 'Completo');
define('_MD_GWLOTO_JOBSTEP_STATUS_CANCELED', 'Cancelado');

define('_MD_GWLOTO_STEP_RPT_TITLE', 'Trabajo Etapa');
define('_MD_GWLOTO_STEP_NAME', 'Etapa');
define('_MD_GWLOTO_STEP_STATUS', 'Condición');
define('_MD_GWLOTO_STEP_CPLAN', 'Nombre del Plan');
define('_MD_GWLOTO_STEP_ASSIGNED', 'Asignado a');

define('_MD_GWLOTO_JOB_PHASE_SEQ', 'Imprimir para la fase');
define('_MD_GWLOTO_JOB_PRINT_BUTTON', 'Imprimir');
define('_MD_GWLOTO_JOB_PREDIT_BUTTON', 'Editar');
define('_MD_GWLOTO_JOB_PRVIEW_BUTTON', 'Ver');
define('_MD_GWLOTO_JOB_PRINT_PICK', 'Seleccione lo que desea imprimir');
define('_MD_GWLOTO_JOB_PRINT_REDIR_MSG', 'Elija las opciones de impresión.');
define('_MD_GWLOTO_JOB_PRINTING_REDIR_MSG', 'Impresión.');
define('_MD_GWLOTO_JOB_VIEW_REDIR_MSG', 'Los detalles del trabajo que aparecen.');
define('_MD_GWLOTO_NEED_TCPDF', 'No se puede encontrar la clase necesaria TCPDF');
define('_MD_GWLOTO_JOB_PRINT_NODEFS', 'No hay plugins de impresión están instalados. Notificar al administrador del sistema.');

// user authorities
define('_MD_GWLOTO_USERAUTH_PL_ADMIN_DSC', 'Editar Autoridades del usuario');
define('_MD_GWLOTO_USERAUTH_PL_AUDIT_DSC', 'Ver Autoridades del usuario');
define('_MD_GWLOTO_USERAUTH_PL_EDIT_DSC',  'Agregar o Editar Lugares');
define('_MD_GWLOTO_USERAUTH_PL_SUPER_DSC', 'Supervisor del Lugar');

define('_MD_GWLOTO_USERAUTH_CP_EDIT_DSC',  'Agregar o editar los planes de control');
define('_MD_GWLOTO_USERAUTH_CP_VIEW_DSC',  'Control de vistas de los planes');

define('_MD_GWLOTO_USERAUTH_JB_EDIT_DSC',  'Agregar o editar los trabajos');
define('_MD_GWLOTO_USERAUTH_JB_VIEW_DSC',  'Ver los trabajos');

define('_MD_GWLOTO_USERAUTH_MD_EDIT_DSC',  'Agregar o editar los archivos multimedia');
define('_MD_GWLOTO_USERAUTH_MD_VIEW_DSC',  'Navegar por los archivos multimedia');

define('_MD_GWLOTO_USERAUTH_PL_TRANS_DSC', 'Traducir lugares');
define('_MD_GWLOTO_USERAUTH_CP_TRANS_DSC', 'Traducir los planes de control');
define('_MD_GWLOTO_USERAUTH_MD_TRANS_DSC', 'Traducir archivos multimedia');

define('_MD_GWLOTO_SORTPOINT_FORM', 'Clasificar los puntos de control - %s');
define('_MD_GWLOTO_SORTPOINT_UP', 'Subir');
define('_MD_GWLOTO_SORTPOINT_DOWN', 'Bajar');
define('_MD_GWLOTO_SORTPOINT_REVERSE', 'Inversa');
define('_MD_GWLOTO_SORTPOINT_SAVE', 'Guardar');
define('_MD_GWLOTO_SORTPOINT_SELECT', 'Seleccione un punto de control para mover');
define('_MD_GWLOTO_SORTPOINT_ACTIONS', 'Acciones');
define('_MD_GWLOTO_SORTPOINT_CPOINTS', 'Puntos de Control');
define('_MD_GWLOTO_SORTPOINT_SEQ', 'Secuencia de Ajuste');
define('_MD_GWLOTO_SORTPOINT_SEQ_DISCON', 'Desconectar');
define('_MD_GWLOTO_SORTPOINT_SEQ_RECON', 'Vuelva a conectar');
define('_MD_GWLOTO_SORTPOINT_SEQ_INSPECT', 'Inspección');
define('_MD_GWLOTO_SORTPOINT_SEQ_SHOW', 'Volver a mostrar');
define('_MD_GWLOTO_SORTPOINT_EMPTY', 'No hay nada para ordenar');

define('_MD_GWLOTO_SETHOME_OK', 'Lugar de inicio está establecido.');
// program link descriptions
define('_MD_GWLOTO_PRG_DSC_EDITAUTHS', 'Autoridades del usuario');
define('_MD_GWLOTO_PRG_DSC_EDITPLACE', 'Editar este Lugar');
define('_MD_GWLOTO_PRG_DSC_ADDPLACE', 'Añadir un lugar');
define('_MD_GWLOTO_PRG_DSC_ADDPLAN', 'Añade un Plan de Control');
define('_MD_GWLOTO_PRG_DSC_VIEWPLAN', 'Ver Plan de Control');
define('_MD_GWLOTO_PRG_DSC_EDITPLAN', 'Editar este Plan de Control');
define('_MD_GWLOTO_PRG_DSC_ADDPOINT', 'Añadir un punto de control');
define('_MD_GWLOTO_PRG_DSC_EDITPOINT', 'Editar este punto de control');
define('_MD_GWLOTO_PRG_DSC_SRTPOINT', 'Clasificar los puntos de control');
define('_MD_GWLOTO_PRG_DSC_SETHOME', 'Establecer como Inicio');
define('_MD_GWLOTO_PRG_DSC_SELPLACE', 'Seleccionar lugar');
define('_MD_GWLOTO_PRG_DSC_SELPLAN', 'Seleccione el Plan de Control');
define('_MD_GWLOTO_PRG_DSC_SELPOINT', 'Seleccione el punto de control');
define('_MD_GWLOTO_PRG_DSC_SELMEDIA', 'Seleccione multimedia');
define('_MD_GWLOTO_PRG_DSC_NEWJOB', 'Nuevo trabajo con este Plan');
define('_MD_GWLOTO_PRG_DSC_LISTJOBS', 'Búsqueda de trabajo');
define('_MD_GWLOTO_PRG_DSC_MEDIA', 'Centro multimedia');

define('_MD_GWLOTO_CHOOSE_PLACE', 'Seleccione un lugar');
define('_MD_GWLOTO_CHOOSE_ACTION', 'Menú de acciones');
define('_MD_GWLOTO_CHOOSE_CTRLPLAN', 'Planes de Control de este lugar');

define('_MD_GWLOTO_CHOOSE_SELECTED', 'Elige Acción para el elemento seleccionado');
define('_MD_GWLOTO_CLIPBOARD_FORM', 'Elige la acción de la partida Portapapeles');
define('_MD_GWLOTO_DELETE_SELECTED', 'Eliminar este artículo');
define('_MD_GWLOTO_MOVE_SELECTED', 'Mueva este artículo');
define('_MD_GWLOTO_COPY_SELECTED', 'Copia este artículo');
define('_MD_GWLOTO_MOVECOPY_SELECTED', 'Mover o copiar este artículo');
define('_MD_GWLOTO_CANCEL_SELECTED', 'Cancelar Mover o copiar');
define('_MD_GWLOTO_DELETE_SEL_BUTTON', 'Eliminar');
define('_MD_GWLOTO_MOVE_SEL_BUTTON', 'Mover');
define('_MD_GWLOTO_COPY_SEL_BUTTON', 'Copiar');
define('_MD_GWLOTO_CANCEL_SEL_BUTTON', 'Cancelar');
define('_MD_GWLOTO_MOVECOPY_SEL_BUTTON', 'Mover o copiar');
define('_MD_GWLOTO_MOVECOPY_SEL_OK', 'Copiados al portapapeles. Seleccione el destino para mover o copiar.');
define('_MD_GWLOTO_DELETE_SEL_CONFIRM', '¿Estás seguro que quieres eliminar este?');

define('_MD_GWLOTO_CANCEL_SEL_OK', 'Portapapeles vacíos');
define('_MD_GWLOTO_COPY_SEL_OK', 'Copiado');
define('_MD_GWLOTO_COPY_SEL_ERR', 'No se pudo copiar');
define('_MD_GWLOTO_MOVE_SEL_OK', 'Movido');
define('_MD_GWLOTO_MOVE_SEL_ERR', 'No se puede mover');
define('_MD_GWLOTO_DELETE_SEL_OK', 'Eliminados');
define('_MD_GWLOTO_DELETE_SEL_ERR', 'No se pudo eliminar');
define('_MD_GWLOTO_DELETE_SEL_PLACE_IN_USE', 'No se pudo eliminar. %1$d lugares y %2$d planes de control se adjuntan');
define('_MD_GWLOTO_MOVE_SEL_ONLY_TOP_PLACE', 'No se puede mover el nivel superior sólo lugar');
define('_MD_GWLOTO_COPY_NAME_PREFIX', 'Copia del ');

define('_MD_GWLOTO_CLIPBOARD_JOB_FORM', 'Añadir Plan de Control de Trabajo');
define('_MD_GWLOTO_STEP_ADD_THIS_BUTTON', 'Añadir a Job');
define('_MD_GWLOTO_STEP_ADD_CANCEL_BUTTON', 'Cancelar');

// media
define('_MD_GWLOTO_MEDIA_RPT_TITLE', 'Archivos multimedia disponibles');

define('_MD_GWLOTO_MEDIA_FILE_TO_UPLOAD', 'Archivo para Agregar');
define('_MD_GWLOTO_MEDIA_LINK', 'Enlace a archivo multimedia');
define('_MD_GWLOTO_MEDIA_CLASS', 'Clasificación');
define('_MD_GWLOTO_MEDIA_CLASS_SELECT', 'Seleccione Clasificación');
define('_MD_GWLOTO_MEDIA_NAME', 'Nombre');
define('_MD_GWLOTO_MEDIA_DESCRIPTION', 'Descripción');
define('_MD_GWLOTO_MEDIA_REQUIRED', 'Requerido');
define('_MD_GWLOTO_MEDIA_ADD_BUTTON_DSC', 'Agregar archivos multimedia');
define('_MD_GWLOTO_MEDIA_ADD_BUTTON', 'Subir');
define('_MD_GWLOTO_MEDIA_ADD_FORM', 'Cargar nuevo archivo multimedia');
define('_MD_GWLOTO_MEDIA_VIEW_FORM', 'Ver detalles multimedia');
define('_MD_GWLOTO_MEDIA_EDIT_FORM', 'Editar información multimedia');
define('_MD_GWLOTO_MEDIA_ATTACH_FORM', 'Adjuntar archivo multimedia');

define('_MD_GWLOTO_ATTACHED_MEDIA_TITLE', 'Los archivos adjuntos');
define('_MD_GWLOTO_ATTACH_MEDIA', 'Adjuntar archivos');

define('_MD_GWLOTO_MEDIA_AUTHPLACE', 'Lugar de Autoridad');
define('_MD_GWLOTO_MEDIA_AUTHPLACE_CHOOSE', 'Elija Lugar');

define('_MD_GWLOTO_MEDIA_ADDNEW', 'Agregar');
define('_MD_GWLOTO_MEDIA_BROWSE', 'Examinar');
define('_MD_GWLOTO_MEDIA_EXIT', 'Salir del Centro Multimedia');
define('_MD_GWLOTO_MEDIA_SELECT', 'Seleccione');
define('_MD_GWLOTO_MEDIA_SEARCH_BUTTON', 'Búsqueda');
define('_MD_GWLOTO_MEDIA_SELECT_BUTTON', 'Seleccione');
define('_MD_GWLOTO_MEDIA_ATTACH_BUTTON', 'Adjunte');
define('_MD_GWLOTO_MEDIA_CANCEL_BUTTON', 'Cancelar');
define('_MD_GWLOTO_MEDIA_DETACH_BUTTON', 'Separe');
define('_MD_GWLOTO_MEDIA_VIEW_FILE', 'Ver');
define('_MD_GWLOTO_MEDIA_SAVE_BUTTON', 'Guardar');
define('_MD_GWLOTO_MEDIA_DELETE_BUTTON', 'Eliminar');
define('_MD_GWLOTO_MEDIA_DELETE_CONFIRM', '¿Está seguro que desea eliminar los elementos seleccionados?');
define('_MD_GWLOTO_MEDIA_TOOL_TRAY_DSC', 'Herramientas multimedia de archivos');

define('_MD_GWLOTO_MEDIA_SELECT_TO_ATTACH', 'Selecciona archivo a adjuntar.');
define('_MD_GWLOTO_MEDIA_SELECT_PROMPT', 'Selección de los medios de comunicación para unir %1$s %2$s');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_PLACE', 'a lugar');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_PLAN', 'a un plan de control');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_POINT', 'a los puntos de control');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_JOB', 'al trabajo');
define('_MD_GWLOTO_MEDIA_SELECT_TYPE_JOBSTEP', 'a la etapa de trabajo');
define('_MD_GWLOTO_MEDIA_SELECT_CANCELED', 'Selección de archivos cancelado.');

define('_MD_GWLOTO_MEDIA_ATTACH_TO', '%1$s %2$s');
define('_MD_GWLOTO_MEDIA_ATTACH_TO_PROMPT', 'Asociar');
define('_MD_GWLOTO_MEDIA_ATTACH_OPTIONS', 'Opciones');
define('_MD_GWLOTO_MEDIA_ATTACH_REQUIRED', 'Verifique marcados según se requiera.');
define('_MD_GWLOTO_MEDIA_ATTACH_CONTINUE', 'Compruebe para adjuntar archivos adicionales.');
define('_MD_GWLOTO_MEDIA_ATTACH_OK', 'Archivo adjunto.');
define('_MD_GWLOTO_MEDIA_ATTACH_DB_ERROR', 'No se puede adjuntar archivos.');
define('_MD_GWLOTO_MEDIA_DETACH_OK', 'Archivo individual.');
define('_MD_GWLOTO_MEDIA_DETACH_DB_ERROR', 'No es posible separar el archivo.');
define('_MD_GWLOTO_MEDIA_DETACH_CONFIRM', '¿Está seguro que desea separar esto?');

define('_MD_GWLOTO_MEDIA_FILE_NAME', 'Actual del archivo o el enlace');
define('_MD_GWLOTO_MEDIA_UPLOAD_BY', 'Subido por');
define('_MD_GWLOTO_MEDIA_UPLOAD_ON', 'Fecha de carga');

define('_MD_GWLOTO_MEDIA_NO_MEDIA', 'No se definen los elementos multimedia.');
define('_MD_GWLOTO_MEDIA_NO_MATCH', 'No se encontraron elementos multimedia que coinciden con los criterios de búsqueda.');
define('_MD_GWLOTO_MEDIA_NOTFOUND', 'Artículo no encontrado. ');

define('_MD_GWLOTO_MEDIA_FILE_NOT_GIVEN', 'Un archivo multimedia o un hipervínculo debe ser especificado.');
define('_MD_GWLOTO_MEDIA_FILE_UPLOAD_ERROR', 'Error al subir - código de error %1$d');
define('_MD_GWLOTO_MEDIA_FILE_MOVE_ERROR', 'Error al subir - Es subir ruta de acceso de escritura?');
define('_MD_GWLOTO_MEDIA_ADD_OK', 'Archivo agregado');
define('_MD_GWLOTO_MEDIA_ADD_DB_ERROR', 'Could not add multimedia item.');
define('_MD_GWLOTO_MEDIA_UPDATE_OK', 'Elemento multimedia actualizados.');
define('_MD_GWLOTO_MEDIA_UPDATE_DB_ERROR', 'No se pudo actualizar el artículo multimedia.');
define('_MD_GWLOTO_MEDIA_DELETE_OK', 'Elemento multimedia ha sido eliminado.');
define('_MD_GWLOTO_MEDIA_DELETE_DB_ERROR', 'No se pudo eliminar el tema multimedia.');

//  media_class ENUM('permit','form','diagram','instructions','other')
define('_MD_GWLOTO_MEDIACLASS_PERMIT', 'Permiso');
define('_MD_GWLOTO_MEDIACLASS_FORM', 'Formulario');
define('_MD_GWLOTO_MEDIACLASS_DIAGRAM', 'Diagrama');
define('_MD_GWLOTO_MEDIACLASS_INSTRUCTIONS', 'Instrucciones');
define('_MD_GWLOTO_MEDIACLASS_MANUAL', 'Manual');
define('_MD_GWLOTO_MEDIACLASS_MSDS', 'Hoja de Seguridad');
define('_MD_GWLOTO_MEDIACLASS_OTHER', 'Otros');

// plugins
define('_MD_GWLOTO_PLUGIN_ADMIN', 'Plugin de administración');
define('_MD_GWLOTO_PLUGIN_NAME', 'Plugin Nombre');
define('_MD_GWLOTO_PLUGIN_DESCRIPTION', 'Descripción');

//new since 1.0
define('_MD_GWLOTO_NOSCRIPT_GO', 'Ir'); // Navigation submit button
// group authority form
define('_MD_GWLOTO_TITLE_EDITGRPAUTHS', 'Establecer las autoridades del Grupo');
define('_MD_GWLOTO_GROUPAUTH_FORM', 'Establecer las autoridades del Grupo');
define('_MD_GWLOTO_GROUPAUTH_GROUP', 'Elegir un Grupo');
define('_MD_GWLOTO_GROUPAUTH_DISPLAY', 'Mostrar las actuales autoridades');
define('_MD_GWLOTO_USERAUTH_BY_USER', 'Edición de usuarios');
define('_MD_GWLOTO_USERAUTH_BY_GROUP', 'Edición de Grupos');
define('_MD_GWLOTO_USERAUTH_RPT_GROUP', 'Grupo');
