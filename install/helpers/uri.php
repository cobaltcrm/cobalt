<?php
class CURI{
	
	function base(){
		return substr(JURI::base(),0, strrpos(JURI::base(),"install/"));
	}

}