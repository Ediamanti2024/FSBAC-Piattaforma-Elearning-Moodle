<?php

function is_empty($var) {
	if ( empty($var) && ($var !== 0) && ($var !== '0') ) {
		return true;
	}
	return false;	
}