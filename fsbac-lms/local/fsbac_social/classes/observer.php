<?php

defined('MOODLE_INTERNAL') || die();

class local_fsbac_social_observer {

    public static function user_created(\core\event\user_created $event) {

        if (isset($_COOKIE['MoodleSession'])) {

            $oldSessionValue = clean_param($_COOKIE['MoodleSession'], PARAM_TEXT);

            global $DB;
            $sessionKey = session_id();
            
            $sessionKeyDescription = $DB->sql_compare_text('session_key');
            $sessionKeyPlaceholder = $DB->sql_compare_text(':sessionkey');
    
            $accessRecord = $DB->get_record_sql(
                "SELECT id, session_key FROM {fsbac_data} WHERE action='access' AND {$sessionKeyDescription} = {$sessionKeyPlaceholder}",
                [
                    'sessionkey' => $oldSessionValue,
                ]
            );

            $data = $event->get_data();
            
            if($accessRecord) {
                // Inserimento record registration
                $insertObj = new stdClass();
                $insertObj->session_key         = $sessionKey;
                $insertObj->user_id             = $data['objectid'];
                $insertObj->action              = 'registration';
                $insertObj->created_at          = time();
                
                $DB->insert_record('fsbac_data', $insertObj);

                // Aggiornamento record accesso
                $accessRecord->session_key = $sessionKey;
                $DB->update_record('fsbac_data', $accessRecord);
            }
        }
    }
    
    public static function user_enrolled(\core\event\user_enrolment_created $event) {

        if (isset($_COOKIE['MoodleSession'])) {

            global $DB;
            global $USER;
            global $COURSE;
            
            $sessionKey = session_id();
            
            $sessionKeyDescription = $DB->sql_compare_text('session_key');
            $sessionKeyPlaceholder = $DB->sql_compare_text(':sessionkey');
    
            $accessRecord = $DB->get_record_sql(
                "SELECT id, session_key FROM {fsbac_data} WHERE action='access' AND {$sessionKeyDescription} = {$sessionKeyPlaceholder}",
                [
                    'sessionkey' => $sessionKey,
                ]
            );
            
            if($accessRecord) {
                // Inserimento record enrol
                $insertObj = new stdClass();
                $insertObj->session_key         = $sessionKey;
                $insertObj->user_id             = isloggedin() ? $USER->id : null;
                $insertObj->action              = 'enrol';
                $insertObj->course_id_enrol     = $COURSE->id;
                $insertObj->created_at          = time();
                
                $DB->insert_record('fsbac_data', $insertObj);
            }
        }
    }

    public static function user_loggedin(\core\event\user_loggedin $event) {
        
        if (isset($_COOKIE['MoodleSession'])) {

            $oldSessionValue = clean_param($_COOKIE['MoodleSession'], PARAM_TEXT);

            global $DB;
            global $USER;

            $sessionKey = session_id();
            
            $sessionKeyDescription = $DB->sql_compare_text('session_key');
            $sessionKeyPlaceholder = $DB->sql_compare_text(':sessionkey');
    
            $accessRecord = $DB->get_record_sql(
                "SELECT id, session_key  FROM {fsbac_data} WHERE action='access' AND {$sessionKeyDescription} = {$sessionKeyPlaceholder}",
                [
                    'sessionkey' => $oldSessionValue,
                ]
            );
            
            if($accessRecord) {
                // Inserimento record login
                $insertObj = new stdClass();
                $insertObj->session_key     = $sessionKey;
                $insertObj->user_id         = isloggedin() ? $USER->id : null;
                $insertObj->action          = 'login';
                $insertObj->created_at      = time();
                
                $DB->insert_record('fsbac_data', $insertObj);

                // Aggiornamento record accesso
                //$accessRecord->session_key = $sessionKey;
                //$DB->update_record('fsbac_data', $accessRecord);

                $sql = 'UPDATE {fsbac_data} SET session_key = :newsessionkey WHERE session_key = :oldsessionkey';
                $DB->execute($sql, ['newsessionkey' => $sessionKey, 'oldsessionkey' => $oldSessionValue]);
            }
        }
    }
}
