<?php
function pr($data = null){
	echo "<pre>";
	print_r($data);
	echo "<pre>";
}

function prd($data = null)
{
	echo "<pre>";
	pr($data);
	exit;
}