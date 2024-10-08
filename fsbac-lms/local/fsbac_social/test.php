<?php

require_once('../../config.php');

global $CFG;
global $SESSION;


echo sesskey();
echo '<br>';

$SESSION->myvar = 'simonefighissimo';
echo '<br>';

var_dump($SESSION);
echo get_moodle_cookie();
echo '<br>';
echo '<hr>';

echo ($CFG->wwwroot . '/course/view.php?id=');