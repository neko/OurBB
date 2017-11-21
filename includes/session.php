<?php

session_start();

if ( !isset( $_SESSION['sid'] ) || !( $session = Session::where( 'sid', $_SESSION['sid'] )->first() ) ) {
	$session = new Session();
}

$session->save();

$_SESSION['sid'] = $session->sid;
