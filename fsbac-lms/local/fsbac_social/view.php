<?php

require_once('../../config.php');
require_once('locallib.php');

global $CFG;
global $SESSION;
global $DB;
global $USER;

$courseIdSource = optional_param('id', '', PARAM_INT);
$utmSource = optional_param('utm_source', '', PARAM_TEXT);
$utmMedium = optional_param('utm_medium', '', PARAM_TEXT);
$utmCampaign = optional_param('utm_campaign', '', PARAM_TEXT);

if(is_empty($courseIdSource) || is_empty($utmSource) || is_empty($utmMedium) || is_empty($utmCampaign)) {
    redirect($CFG->wwwroot);
}

$sessionId = session_id();

// Per prima cosa, si controlla che l'id corso sia corretto (corso esistente)
if(!$DB->record_exists('course', ['id' => $courseIdSource])) {
    //Se il corso non è esistente, si reindirizza in homepage
    redirect($CFG->wwwroot);
}

// Il record viene creato solo la prima volta che si accede alla pagina, per evitare che eventuali refresh aggiungano record fittizi

$sessionKeyDescription = $DB->sql_compare_text('session_key');
$sessionKeyPlaceholder = $DB->sql_compare_text(':sessionkey');

$recordExists = $DB->record_exists_sql(
    "SELECT id FROM {fsbac_data} WHERE {$sessionKeyDescription} = {$sessionKeyPlaceholder}",
    [
        'sessionkey' => $sessionId,
    ]
);

if(!$recordExists) {

    // Inserimento record accesso
    $insertObj = new stdClass();
    $insertObj->session_key         = $sessionId;
    $insertObj->utm_source          = $utmSource;
    $insertObj->utm_medium          = $utmMedium;
    $insertObj->utm_campaign        = $utmCampaign;
    $insertObj->user_id             = isloggedin() ? $USER->id : null;
    $insertObj->course_id_source    = $courseIdSource;
    $insertObj->action              = 'access';
    $insertObj->created_at          = time();
    
    // Inserimento nuova sessione
    $DB->insert_record('fsbac_data', $insertObj);

}
// http://moodle-unpli.local/local/fsbac_social/view.php?id=2&utm_source=google&utm_medium=cpc&utm_campaign=spring

redirect($CFG->wwwroot . '/course/view.php?id=' . $courseIdSource);
