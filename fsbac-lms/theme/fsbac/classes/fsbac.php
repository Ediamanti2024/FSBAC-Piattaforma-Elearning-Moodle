<?php

namespace theme_fsbac;


use stdClass;

class fsbac
{
    public static function get_corsi_invetrina()
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from mdl_course c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='public' and cd.value=1 ) pub on c.id=pub.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {


            $courses[] = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
        }


        return $courses;
    }



    public static function get_percorsi_home()
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore, liv.value as livello, liv.configdata as jsonlivello, dur.value durata
                    from mdl_course c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='livello' ) liv on c.id=liv.instanceid
                    where c.visible=1 and c.format='percorsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $paths = [];
        foreach ($records as  $path) {


            $paths[] = fsbac::get_format_path_for_card($path->courseid, $path->sottotitolo, $path->livello, $path->jsonlivello, $path->durata);
        }

        return $paths;
    }

    public static function get_percorsi_pubblici()
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore, liv.value as livello, liv.configdata as jsonlivello, dur.value as durata
                    from mdl_course c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='livello' ) liv on c.id=liv.instanceid
                    where c.visible=1 and c.format='percorsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $paths = [];
        foreach ($records as  $path) {
            $p = fsbac::get_format_path_for_card($path->courseid, $path->sottotitolo, $path->livello, $path->jsonlivello, $path->durata);
            $p->startdate = intval($path->startdate);
            $p->duration =  $p->duration  ? intval($p->duration) : 1;
            $paths[] = $p;
        }

        return $paths;
    }


    public static function get_corsi_pubblici()
    {
        global $DB;
        $sql = <<<SQL
                        select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia,
                        mode.configdata as jsontipologia, c.startdate,
                        c.enddate,
                        case when (c.enddate) = 0 then  'senza_scadenza'
                        when (unix_timestamp(now()) - c.enddate) between 1 and 1296000 then  'entro_7_giorni'
                        when (unix_timestamp(now()) - c.enddate) between 604800 and 1296000 then  'entro_15_giorni'
                        when (unix_timestamp(now()) - c.enddate) between 1296000 and 2592000 then  'entro_1_mese'
                        when (unix_timestamp(now()) - c.enddate) between 2592000 and 5184000 then  'entro_2_mesi'
                        when (unix_timestamp(now()) - c.enddate) between 5184000 and 15552000 then  'entro_6_mesi'
                        when (unix_timestamp(now()) - c.enddate) > 15552000 then  'dopo_6_mesi'
                        end
                        as enddate_fascia,
                        sot.value as sottotitolo, tea.value as autore, pro.value as programma, dur.value as durata
                        from mdl_course c
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='programma' ) pro on c.id=pro.instanceid
                        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                        where c.visible=1 and c.format='corsofsbac'
                        and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        and c.id in (select e.courseid from mdl_enrol e where c.id=e.courseid and e.enrol='fsbac' and e.status=0 and e.customint6=1)
                        order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;

            $courses[] =   $courseNew;
        }


        return $courses;
    }

    public static function is_certicate_courseid($courseId)
    {
        global $DB;
        $sql = <<<SQL
                        select count(*) as iscertificate
                        from mdl_course_modules cm
                        inner join mdl_modules m on m.id=cm.module and m.name='certificate' and cm.visible=1
                        where cm.course=$courseId
                 SQL;

        $record = $DB->get_record_sql($sql);
        return $record->iscertificate === '0' ? false : true;
    }


    public static function get_format_course_for_card($id, $subtitle, $authors, $typeId, $jsontipologia, $startdate)
    {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/course/format/corsofsbac/locallib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $isloggedin = isloggedin() && !isguestuser();
        $favouritecourseids = [];
        if ($isloggedin) {
            $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));
            $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');

            if ($favourites) {
                $favouritecourseids = array_map(
                    function ($favourite) {
                        return $favourite->itemid;
                    },
                    $favourites
                );
            }
        }

        $uselabels = false;
        $coursecustomfields = get_course_customfields($id);
        if (isset($coursecustomfields["barracompletamentolabels"]) && !empty($coursecustomfields["barracompletamentolabels"]->value)) {
            $uselabels = true;
        }

        $c =  get_course($id);
        $c->fullname = format_string($c->fullname);
        $c->sottotitolo = format_string($subtitle);
        $c->autore = $authors;
        $c->tipologia = fsbac::get_type_from_json($typeId, $jsontipologia);
        $c->tipologiaLabel = $c->tipologia ?  get_string('typecourse.' .  $c->tipologia, 'theme_fsbac') : '';
        $c->courseimage = \core_course\external\course_summary_exporter::get_course_image($c);
        $c->viewurl = $CFG->wwwroot . '/course/view.php?id=' . $id;
        $progressbar = new \format_corsofsbac\progressbar($uselabels);
        $completion = new \completion_info($c);
        if ($completion->is_course_complete($USER->id)) {
            $c->progress = 100;
        } else {
            $c->progress = floor($progressbar::get_course_completion_info($c, $USER->id)->courseprogressperc);
        }
        $c->progressComplete = $c->progress == 100;
        $isfavourite = false;
        if (in_array($c->id, $favouritecourseids)) {
            $isfavourite = true;
        }
        $c->isfavourite = $isfavourite;
        $c->showFavourite = $isloggedin;

		/* La prima start date la trasformo al TZ dell'utente */
		$date = new \DateTime();
		$date->setTimestamp($startdate);
		$date->setTimezone(new \DateTimeZone(\core_date::get_user_timezone()));
		$startdate = $date->getTimestamp();


        if ($startdate && $c->tipologia === 'live') {

			$startlive = fsbac::get_live_startdate($id);
			if($startlive) $startdate = $startlive;

			if ($startdate > time()) {

				$date = new \DateTime();
				$date->setTimestamp($startdate);
				$date->setTimezone(new \DateTimeZone(\core_date::get_user_timezone()));
				$startdate = $date->getTimestamp();
				$hours = $date->format('H:i');
				$days = $date->format('d/m');

				$c->dateLive =  new stdClass();
				$c->dateLive->day =  $days;
				$c->dateLive->hour =  $hours;
			} else {
				$c->dateLive =  null;
			}
        }
        $c->isCertificate = fsbac::is_certicate_courseid($id);



        return $c;
    }

		private static function get_live_startdate($id) {

				global $DB;
				$sql = <<<SQL
							select cm.course, min(start_time) as startdate from mdl_modules m
							inner join mdl_course_modules cm on m.id=cm.module and cm.visible=1 and cm.course=$id
							inner join mdl_zoom z on cm.instance=z.id
							where m.name='zoom' and z.start_time>unix_timestamp(now())
							group by cm.course
						SQL;

				$record = $DB->get_record_sql($sql);

				$startdate = $record->startdate;

				return $startdate;

		}


    private static function get_format_path_for_card($id, $subtitle, $livello, $livelloJson, $durata)
    {
        global $CFG;

        $p =  get_course($id);
        $p->fullname = format_string($p->fullname);
        $p->sottotitolo = format_string($subtitle);
        $p->livello = fsbac::get_type_from_json($livello, $livelloJson);
        $p->livelloLabel = $p->livello ?  get_string('level.' .  $p->livello, 'theme_fsbac') : '';
        $p->duration = $durata ?  $durata : 1;
        $p->durata = fsbac::get_durata_from_minute($durata);
        $p->image = \core_course\external\course_summary_exporter::get_course_image($p);
        $p->viewurl = $CFG->wwwroot . '/course/view.php?id=' . $id;
        $p->progress = floor(\core_completion\progress::get_course_progress_percentage($p));
        $p->progressComplete = $p->progress == 100;
        return  $p;
    }


    private static function get_type_from_json($id, $jsonString)
    {


        if ($id) {
            $json = json_decode($jsonString);
            $types = explode("\n", $json->options);

            return trim($types[intval($id) - 1]);
        }
        return null;
    }


    private static function get_durata_from_minute($time)
    {
        try {
            $h =    date('H', intval(mktime(0, (int) $time)));
            $m =    date('i', intval(mktime(0, (int) $time)));

            $h = substr($h, 0, 1) == "0" && strlen($h) > 1 ? substr($h, 1, 1) : $h;
            $m = substr($m, 0, 1) == "0" && strlen($m) > 1 ? substr($m, 1, 1) : $m;

            $strH = $h != "0"  ?  $h . "<span>h</span>" : "";
            $strM = $m != "0"  ?  " " . $m . "<span>min</span>" : "";

            return  $strH . "" . $strM;
        } catch (\Exception $e) {
            return "";
        }
    }

    public static function get_interests()
    {
        $theme = \theme_config::load('fsbac');

        $interestsString = $theme->settings->{"interests"};

        if ($interestsString) {
            $tags = explode(",",  $interestsString);

            $interests = [];
            foreach ($tags as  $interest) {
                $i  = new stdClass();

                $i->tag = trim($interest);
                $i->label = fsbac::get_label_tag($i->tag);
                $interests[] =  $i;
            }
            return $interests;
        }
        return [];
    }

    public static function get_footer_images()
    {
        global $OUTPUT;

        $images = new stdClass();

        $images->mic = $OUTPUT->image_url('footer/mic', 'theme_fsbac');
        $images->repubblica = $OUTPUT->image_url('footer/repubblica', 'theme_fsbac');
        $images->euro = $OUTPUT->image_url('footer/euro', 'theme_fsbac');
        $images->loghi_istituzionali = $OUTPUT->image_url('footer/loghi_istituzionali', 'theme_fsbac');

        return $images;
    }

    public static function get_footer_links_social()
    {

        $theme = \theme_config::load('fsbac');


        $links = new stdClass();

        $links->facebooklink =  $theme->settings->{"facebooklink"};
        $links->linkedinlink =  $theme->settings->{"linkedinlink"};
        $links->twitterlink =  $theme->settings->{"twitterlink"};
        $links->instagramlink =  $theme->settings->{"instagramlink"};


        return $links;
    }


    public static function get_header_notify()
    {

        $theme = \theme_config::load('fsbac');


        $notify = new stdClass();

        $notify->text =  $theme->settings->{"notify_text"};
        $notify->link =  $theme->settings->{"notify_link"};

        if ($notify->text) {
            return  $notify;
        }


        return null;
    }



    public static function get_label_tag($tag)
    {
        try {
            return  get_string('tag.' .  trim($tag), 'theme_fsbac');
        } catch (\Exception $e) {
            return $tag;
        }
    }

    public static function get_options_filter_order()
    {
        $options = [];
        $option =  fsbac::get_option(get_string('filter.order.date_start', 'theme_fsbac'), 'date_start');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.order.a_z', 'theme_fsbac'), 'a_z');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.order.z_a', 'theme_fsbac'), 'z_a');
        $options[] = $option;

        return  $options;
    }

    public static  function get_option($label, $value)
    {
        $option = new stdClass();
        $option->label = $label;
        $option->value = $value;
        return $option;
    }

    public static function get_options_tag()
    {
        global $DB;
        $sql = <<<SQL
                        select distinct t.name as tagname
                        from mdl_tag t inner join mdl_tag_instance ti on t.id=t.id and itemtype='course'

               SQL;

        $records = $DB->get_records_sql($sql);

        $options = [];
        foreach ($records as  $tag) {
            $options[] = fsbac::get_option(fsbac::get_label_tag($tag->tagname), $tag->tagname);
        }
        usort($options, fn ($a, $b) => strcmp($a->label, $b->label));
        return $options;
    }

    public static function get_options_percorsi()
    {
        global $DB;
        $sql = <<<SQL
                            select id,fullname from mdl_course c
                            where format='percorsofsbac' and visible=1
                            and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                            and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                 SQL;

        $records = $DB->get_records_sql($sql);

        $options = [];
        foreach ($records as  $path) {
            $options[] = fsbac::get_option(format_string($path->fullname), $path->id);
        }
        return $options;
    }

    public static function get_options_type_courses()
    {
        $options = [];
        $option =  fsbac::get_option(get_string('typecourse.live', 'theme_fsbac'), 'live');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('typecourse.video_ondemand', 'theme_fsbac'), 'video_ondemand');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('typecourse.corsi_multimediali', 'theme_fsbac'), 'corsi_multimediali');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('typecourse.podcast', 'theme_fsbac'), 'podcast');
        $options[] = $option;

        return  $options;
    }

    public static function get_options_teacher()
    {
        global $DB;
        $sql = <<<SQL
                              select distinct value from mdl_customfield_data cd
                              inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' and value <>''
                 SQL;

        $records = $DB->get_records_sql($sql);

        $options = [];
        $values = [];

        foreach ($records as  $v) {
            $list = explode(",", trim($v->value));
            foreach ($list as  $name) {
                $n = trim($name);
                $values[] = $n;
            }
        }

        foreach (array_unique($values) as  $name) {
            $options[] = fsbac::get_option($name, $name);
        }

        usort($options, fn ($a, $b) => strcmp($a->label, $b->label));
        return $options;
    }

    public static function get_options_programma()
    {
        global $DB;
        $sql = <<<SQL
                        select distinct value from mdl_customfield_data cd
                        inner join mdl_course c on c.id=cd.instanceid and c.visible=1
                        inner join mdl_customfield_field cf on cf.id=cd.fieldid where cf.shortname='programma' and value <>''
                        and instanceid not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                 SQL;

        $records = $DB->get_records_sql($sql);

        $options = [];
        $values = [];
        foreach ($records as  $v) {
            $list = explode(",", trim($v->value));
            foreach ($list as  $name) {
                $n = trim($name);
                $values[] = $n;
            }
        }

        foreach (array_unique($values) as  $name) {
            $options[] = fsbac::get_option($name, $name);
        }

        usort($options, fn ($a, $b) => strcmp($a->label, $b->label));
        return $options;
    }

    public static function get_tags_course($courseId)
    {
        global $DB;

        $sql = "SELECT ti.id, t.name
        FROM {tag_instance} ti
        JOIN {tag} t
        ON t.id = ti.tagid
        WHERE ti.itemtype = 'course'
        AND ti.contextid = ?";
        $coursecontextinstance = \context_course::instance($courseId);
        $coursetags = $DB->get_records_sql($sql, array($coursecontextinstance->id));
        $coursetagsarray = [];
        if (!empty($coursetags)) {
            foreach ($coursetags as $tag) {
                $coursetagsarray[] = $tag->name;
            }
        }

        return $coursetagsarray;
    }

    public static function get_paths_course($courseId)
    {
        global $DB;

        $sql = <<<SQL
                    select distinct s.course as idpercorso from mdl_subcourse s
                    inner join mdl_course_modules cm on s.id=cm.instance and s.course=cm.course
                    inner join mdl_modules m on cm.module=m.id and m.name='subcourse'
                    where refcourse=$courseId and cm.deletioninprogress=0 and cm.visible=1
            SQL;


        $records = $DB->get_records_sql($sql);
        $paths = [];

        foreach ($records as $p) {
            $paths[] = $p->idpercorso;
        }

        return $paths;
    }


    public static function get_options_scadenza()
    {
        $options = [];
        $option =  fsbac::get_option(get_string('filter.time.senza_scadenza', 'theme_fsbac'), 'senza_scadenza');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.entro_7_giorni', 'theme_fsbac'), 'entro_7_giorni');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.entro_15_giorni', 'theme_fsbac'), 'entro_15_giorni');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.entro_1_mese', 'theme_fsbac'), 'entro_1_mese');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.entro_2_mesi', 'theme_fsbac'), 'entro_2_mesi');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.entro_6_mesi', 'theme_fsbac'), 'entro_6_mesi');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('filter.time.dopo_6_mesi', 'theme_fsbac'), 'dopo_6_mesi');
        $options[] = $option;

        return  $options;
    }


    public static function get_options_level()
    {
        $options = [];
        $option =  fsbac::get_option(get_string('level.base', 'theme_fsbac'), 'base');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('level.intermedio', 'theme_fsbac'), 'intermedio');
        $options[] = $option;
        $option =  fsbac::get_option(get_string('level.avanzato', 'theme_fsbac'), 'avanzato');
        $options[] = $option;
        return  $options;
    }

    public static function get_corsi_perte()
    {
        global $DB, $USER;
        $sql = <<<SQL
                        select c.id as courseid, c.fullname as coursename, c.summary,c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                        from mdl_user_enrolments ue
                        inner join mdl_enrol e on ue.enrolid=e.id and e.status=0 and ue.userid=$USER->id
                        inner join mdl_course c on e.courseid=c.id and c.visible=1 and format='corsofsbac'
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        where c.visible=1 and c.format='corsofsbac' and ue.status=0
                        -- and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        -- NF per il momento prendo tutte le iscrizioni.  and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0)
                        and c.id not in (select comp.course from mdl_course_completions comp where comp.userid=$USER->id and comp.timecompleted is not null)
                        order by c.sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->progressShow = true;

            $courses[] =   $courseNew;
        }


        return $courses;
    }

    public static function get_corsi_like()
    {
        global $DB, $USER;
        $sql = <<<SQL
                        select distinct c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                        from mdl_tag t
                        inner join mdl_tag_instance ti on t.id=ti.tagid and ti.itemtype ='course'
                        inner join mdl_course c on  c.id=ti.itemid
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        where c.visible=1 and c.format='corsofsbac'
                        and t.name in (select name from mdl_tag t inner join mdl_tag_instance ti on t.id=ti.tagid and itemtype ='user' and  itemid=$USER->id)
                        and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                        and c.id not in (select courseid from mdl_enrol e inner join mdl_user_enrolments ue on e.id=ue.enrolid and userid=$USER->id)
                 SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;



            $courses[] =   $courseNew;
        }


        return $courses;
    }

    static function get_sql_course_home($GIORNIDAINSERIRE)
    {

        global $DB;
        $sql = <<<SQL
        select c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore,
        if( (unix_timestamp() + ($GIORNIDAINSERIRE*86400) > c.enddate and unix_timestamp() < c.enddate) , 1, 0) inscadenza,
        coalesce(pub.value,0) suggerito,
        coalesce(news.value,0) nuovo,
        coalesce(inarr.value,0) inarrivo
        from mdl_course c
        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='public') pub on c.id=pub.instanceid
        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='new') news on c.id=news.instanceid
        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='inarrivo') inarr on c.id=inarr.instanceid
        where c.visible=1 and c.format='corsofsbac'
        and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
        and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
        order by sortorder
        SQL;
        $records = $DB->get_records_sql($sql);

        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->suggerito =  $course->suggerito;
            $courseNew->inscadenza =  $course->inscadenza;
            $courseNew->nuovo =  $course->nuovo;
            $courseNew->inarrivo =  $course->inarrivo;
            $courses[] = $courseNew;
        }


        return $courses;
    }

    public static function get_corsi_suggeriti($records)
    {


        $courses = [];
        foreach ($records as  $course) {
            if ($course->suggerito == 1)
                $courses[] =   $course;
        }


        return $courses;
    }


    public static function get_corsi_last_minute($records)
    {

        $courses = [];
        foreach ($records as  $course) {

            if ($course->inscadenza == 1)
                $courses[] =   $course;
        }


        return $courses;
    }

    public static function get_corsi_tedenza()
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from mdl_course c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join
                    (
                    select count(*) as totaccessi, e.courseid from mdl_enrol e
                    inner join mdl_user_enrolments ue on e.id=ue.enrolid
                    where e.status=0 and e.enrol='fsbac'
                    group by e.courseid
                    ) accessi on accessi.courseid=c.id
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by totaccessi desc limit 6
            SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;

            $courses[] =   $courseNew;
        }


        return $courses;
    }


    public static function get_corsi_nuovi($records)
    {

        $courses = new stdClass();
        $courses->nuovi = [];
        $courses->inarrivo = [];

        foreach ($records as  $course) {


            if ($course->nuovo == 1) {
                $courses->nuovi[] = $course;
            }
            if ($course->inarrivo == 1) {
                $courses->inarrivo[] = $course;
            }
        }
        $courses->nuoviC = count($courses->nuovi);
        $courses->inarrivoC = count($courses->inarrivo);

        return $courses;
    }

    public static function get_percorsi_simili($courseId)
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore, liv.value as livello, liv.configdata as jsonlivello, dur.value durata
                    from (
                    select cc.* from mdl_course cc inner join mdl_tag_instance tii on cc.id=tii.itemid and tii.itemtype='course' and cc.format='percorsofsbac' where tii.tagid in ( select ti.tagid from mdl_course ccc inner join mdl_tag_instance ti on ccc.id=ti.itemid and itemtype='course' where ccc.id=$courseId)
                    ) c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='livello' ) liv on c.id=liv.instanceid
                    where c.visible=1 and c.format='percorsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $paths = [];
        foreach ($records as  $path) {


            $paths[] = fsbac::get_format_path_for_card($path->courseid, $path->sottotitolo, $path->livello, $path->jsonlivello, $path->durata);
        }

        return $paths;
    }

    public static function get_corsi_simili($courseId)
    {
        global $DB;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from (
                    select cc.* from mdl_course cc
                    inner join mdl_tag_instance tii on cc.id=tii.itemid and tii.itemtype='course' and cc.format='corsofsbac'
                    where tii.tagid in (select ti.tagid from mdl_course ccc inner join mdl_tag_instance ti on ccc.id=ti.itemid and itemtype='course' where ccc.id=$courseId)
                    ) c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {


            $courses[] = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
        }


        return $courses;
    }


    public static function get_corsi_dashboard()
    {
        global $DB, $USER;
        $sql = <<<SQL
                       select c.id as courseid, c.fullname as coursename, c.summary,c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                        from mdl_user_enrolments ue
                        inner join mdl_enrol e on ue.enrolid=e.id and e.status=0 and ue.userid=$USER->id
                        inner join mdl_course c on e.courseid=c.id and c.visible=1 and format='corsofsbac'
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        where c.visible=1 and c.format='corsofsbac' and ue.status=0
                        -- and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        -- NF per il momento prendo tutte le iscrizioni.  and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0)
                        order by c.sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $following = [];
        $completed = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->progressShow = true;

            if ($courseNew->progressComplete) {
                $completed[] =   $courseNew;
            } else {
                $following[] =   $courseNew;
            }
        }

        $res = new stdClass();
        $res->completed = $completed;
        $res->following = $following;

        return $res;
    }


    public static function get_percorsi_dashboard()
    {
        global $DB, $USER;
        $sql = <<<SQL
                    select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore, liv.value as livello, liv.configdata as jsonlivello, dur.value durata
                    from mdl_user_enrolments ue
                    inner join mdl_enrol e on ue.enrolid=e.id and e.status=0 and ue.userid=$USER->id
                    inner join mdl_course c on e.courseid=c.id and c.visible=1 and format='percorsofsbac'
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='livello' ) liv on c.id=liv.instanceid
                    where c.visible=1 and c.format='percorsofsbac'
                    -- and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    -- NF per il momento prendo tutte le iscrizioni.   and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0)
                    order by c.sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $following = [];
        $completed = [];
        foreach ($records as  $path) {


            $pathNew = fsbac::get_format_path_for_card($path->courseid, $path->sottotitolo, $path->livello, $path->jsonlivello, $path->durata);
            $pathNew->progressShow = true;

            if ($pathNew->progressComplete) {
                $completed[] =   $pathNew;
            } else {
                $following[] =   $pathNew;
            }
        }

        $res = new stdClass();
        $res->completed = $completed;
        $res->following = $following;

        return $res;
    }

    public static function get_corsi_favorite()
    {
        global $DB, $USER;
        $sql = <<<SQL
                       select c.id as courseid, c.fullname as coursename, c.summary,c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                        from  mdl_course c
                        inner join mdl_favourite f on c.id=f.itemid and userid=$USER->id
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        where c.visible=1 and c.format='corsofsbac'
                        and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and customint6=1)
                        order by c.sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $incompleted =  [];
        $started = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->progressShow = true;

            if ($courseNew->progress > 0 &&  $courseNew->progress < 100) {
                $incompleted[] = $courseNew;
            }

            if ($courseNew->progress == 0) {
                $started[] = $courseNew;
            }
        }


        $res = new stdClass();
        $res->incompleted = $incompleted;
        $res->started = $started;


        return $res;
    }

    public static function get_certificate()
    {
        global $DB, $USER, $OUTPUT;
        $sql = <<<SQL
                        select cm.id as certificate,
                        c.id as courseid, c.fullname as coursename, c.summary,c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                        from mdl_course_completions cc
                        inner join mdl_course_modules cm on cc.course=cm.course
                        inner join mdl_modules m on m.id=cm.module and m.name='certificate'
                        inner join mdl_certificate cert on cm.instance=cert.id
                        inner join mdl_course c on cc.course=c.id and format='corsofsbac'
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        where cc.userid=$USER->id and cc.timecompleted is not null
                        order by cc.timecompleted desc
               SQL;

        $records = $DB->get_records_sql($sql);
        $completed =  [];
        $incompleted = [];

        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->certificateId = $course->certificate;

            $link = new \moodle_url('/mod/certificate/view.php?id=' . $courseNew->certificateId . '&action=get');
            $button = new \single_button($link, get_string('common.download', 'theme_fsbac'), 'post', 'primary');
            $button->add_action(new \popup_action('click', $link, 'view' . $courseNew->certificateId, array('height' => 600, 'width' => 800)));

            $courseNew->buttonDownloadCerticate =  \html_writer::tag('span', $OUTPUT->render($button), array('style' => 'text-align:center'));

            if ($courseNew->progress > 0 &&  $courseNew->progress < 100) {
                $courseNew->progressShow = true;
                $incompleted[] = $courseNew;
            }

            if ($courseNew->progress == 100) {
                $completed[] = $courseNew;
            }
        }


        $res = new stdClass();
        $res->incompleted = $incompleted;
        $res->completed = $completed;


        return $res;
    }


    public static function get_certificate_inprogress()
    {
        global $DB, $USER;
        $sql = <<<SQL
                         select cm.instance, c.id as courseid, c.fullname as coursename, c.summary,c.startdate,c.enddate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                            from mdl_user_enrolments ue
                            inner join mdl_enrol e on ue.enrolid=e.id and e.status=0 and ue.userid=$USER->id
                            inner join mdl_course c on e.courseid=c.id and c.visible=1 and c.format='corsofsbac'
                            inner join mdl_course_modules cm on cm.course=c.id
                            inner join mdl_modules m on m.id=cm.module and m.name='certificate'
                            inner join mdl_certificate cert on cm.instance=cert.id
                            inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                            left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                            left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                            where c.visible=1 and c.format='corsofsbac' and ue.status=0
                            and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                            and c.id not in (select course from  mdl_course_completions where userid=$USER->id and timecompleted is not null)
               SQL;

        $records = $DB->get_records_sql($sql);

        $incompleted = [];

        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;
            $courseNew->certificateId = $course->certificate;
            $courseNew->progressShow = true;

            $incompleted[] = $courseNew;
        }


        $res = new stdClass();
        $res->incompleted = $incompleted;


        return $res;
    }

    public static function get_badges()
    {
        global $DB, $USER, $PAGE;


        $output = $PAGE->get_renderer('core', 'badges');
        $badges = badges_get_user_badges($USER->id);
        $records = badges_get_user_badges($USER->id, null, 0, 120000);
        $userbadges             = new \core_badges\output\badge_user_collection($records, $USER->id);
        $userbadges->sort       = 'dateissued';
        $userbadges->dir        = 'DESC';
        $userbadges->page       = 0;
        $userbadges->perpage    =  120000;
        $userbadges->totalcount = count($badges);

        $res = new stdClass();
        $res->num = count($badges);
        $res->view =   $output->render($userbadges);


        return $res;
    }

    public static function get_corsi_dicolabpage()
    {
        global $DB;
        $sql = <<<SQL
                    select distinct c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from  mdl_course c
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='pnrr' and value=1) pnrr on c.id=pnrr.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    order by c.startdate desc
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {


            $courses[] = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
        }


        return $courses;
    }

    public static function get_corsi_dicolabCard($courseId)
    {
        global $DB, $USER;
        $sql = <<<SQL
                    select distinct c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from (
                    select ti.itemid
                    from mdl_tag t
                    inner join mdl_tag_instance ti on t.id=ti.tagid and ti.itemtype ='course'
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='pnrr' and cd.value=1 ) dicolab on ti.itemid=dicolab.instanceid
                    where t.name in (select name from mdl_tag t inner join mdl_tag_instance ti on t.id=ti.tagid and itemtype ='user' and  itemid=$USER->id)
                    union
                    select cd.instanceid from  mdl_customfield_field cf
                    inner join mdl_customfield_data cd on cf.id=cd.fieldid and cf.shortname='pnrr' and cd.value=1
                    ) cc
                    inner join mdl_course c on c.id=cc.itemid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    and c.id <> $courseId
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {


            $courses[] = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
        }


        return $courses;
    }



	    public static function get_corsi_fsbacCard($courseId)
    {
        global $DB, $USER;
        $sql = <<<SQL
                    select distinct c.id as courseid, c.fullname as coursename, c.summary, c.startdate, mode.value as tipologia, mode.configdata as jsontipologia, sot.value as sottotitolo, tea.value as autore
                    from (
                    select ti.itemid
                    from mdl_tag t
                    inner join mdl_tag_instance ti on t.id=ti.tagid and ti.itemtype ='course'
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='pnrr' and cd.value=0 ) dicolab on ti.itemid=dicolab.instanceid
                    where t.name in (select name from mdl_tag t inner join mdl_tag_instance ti on t.id=ti.tagid and itemtype ='user' and  itemid=$USER->id)
                    union
                    select cd.instanceid from  mdl_customfield_field cf
                    inner join mdl_customfield_data cd on cf.id=cd.fieldid and cf.shortname='pnrr' and cd.value=0
                    ) cc
                    inner join mdl_course c on c.id=cc.itemid
                    inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                    left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                    where c.visible=1 and c.format='corsofsbac'
                    and c.id not in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                    and c.id in (select e.courseid from mdl_enrol e where e.enrol='fsbac' and status=0 and e.customint6=1)
                    and c.id <> $courseId
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {


            $courses[] = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
        }


        return $courses;
    }






    public static function get_corsi_riservati()
    {
        global $DB;
        $sql = <<<SQL
                        select c.id as courseid, c.fullname as coursename, c.summary, mode.value as tipologia,
                        mode.configdata as jsontipologia, c.startdate,
                        c.enddate,
                        case when (c.enddate) = 0 then  'senza_scadenza'
                        when (unix_timestamp(now()) - c.enddate) between 1 and 1296000 then  'entro_7_giorni'
                        when (unix_timestamp(now()) - c.enddate) between 604800 and 1296000 then  'entro_15_giorni'
                        when (unix_timestamp(now()) - c.enddate) between 1296000 and 2592000 then  'entro_1_mese'
                        when (unix_timestamp(now()) - c.enddate) between 2592000 and 5184000 then  'entro_2_mesi'
                        when (unix_timestamp(now()) - c.enddate) between 5184000 and 15552000 then  'entro_6_mesi'
                        when (unix_timestamp(now()) - c.enddate) > 15552000 then  'dopo_6_mesi'
                        end
                        as enddate_fascia,
                        sot.value as sottotitolo, tea.value as autore, pro.value as programma, dur.value as durata
                        from mdl_course c
                        inner join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='mode' ) mode on c.id=mode.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='teacherslist' ) tea on c.id=tea.instanceid
                        left join  ( select instanceid, value from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='sottotitolo' ) sot on c.id=sot.instanceid
                        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='programma' ) pro on c.id=pro.instanceid
                        left join ( select instanceid, value, configdata from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='duration' ) dur on c.id=dur.instanceid
                        where c.visible=1 and c.format='corsofsbac'
                        and c.id in (select instanceid from mdl_customfield_data cd inner join mdl_customfield_field cf on cf.id=cd.fieldid where shortname='riservato' and cd.value=1 )
                        and c.id in (select e.courseid from mdl_enrol e where c.id=e.courseid and e.enrol='self' and e.status=0)
                        order by sortorder
               SQL;

        $records = $DB->get_records_sql($sql);
        $courses = [];
        foreach ($records as  $course) {

            $courseNew = fsbac::get_format_course_for_card($course->courseid, $course->sottotitolo, $course->autore, $course->tipologia, $course->jsontipologia, $course->startdate);
            $courseNew->enddate_fascia = $course->enddate_fascia;
            $courseNew->startdate = intval($course->startdate);
            $courseNew->enddate = $course->enddate;
            $courseNew->programma = $course->programma;
            $courseNew->tags = fsbac::get_tags_course($course->courseid);
            $courseNew->paths = fsbac::get_paths_course($course->courseid);
            $courseNew->duration = $course->durata ? intval($course->durata) : 1;

            $courses[] =   $courseNew;
        }


        return $courses;
    }
}