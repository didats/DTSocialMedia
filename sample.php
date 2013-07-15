<?php
	require_once("DTSocialMedia.php");
	$social = new DTSocialMedia();
	
	$social->consumerkey = "";
	$social->consumersecret = "";
	$social->accesstoken = "";
	$social->accesstokensecret = "";
	
	$data = $social->get_data("twitter", "didats", 20);
	
	print_r($data);