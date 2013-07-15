<?php
	require_once("DTSocialMedia.php");
	$social = new DTSocialMedia();
	
	$social->consumerkey = "DEYdPPGUpYqJTDNgw6sUw";
	$social->consumersecret = "6HzaqpQy8T4j90CY7o4LQmK76rY0TDNkRxLX7pEwlI";
	$social->accesstoken = "51386840-gm7rjpq6LHLChB2aerZdwzdTk3agqG5Gh2Z5mknKQ";
	$social->accesstokensecret = "jjRL8fjoCZ03blMCrKprm2dn3uW16ZZRiU0NrK2pw9I";
	
	$data = $social->get_data("twitter", "VivaTelecom", 20);
	
	print_r($data);