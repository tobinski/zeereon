<?php
/*****************************************************************
19.11.2008 - CHANGES BY ENI:
CAN PARSE STRINGS:
parse($string,true)
/***************************************************************************
*
*	Author   : Eric Sizemore ( www.secondversion.com & www.phpsociety.com)
*	Package  : Simple Template Engine
*	Version  : 1.0.2
*	Copyright: (C) 2006 - 2007 Eric Sizemore
*	Site     : www.secondversion.com
*	Email    : esizemore05@gmail.com
*	File     : tpl.class.php
*
*	This program is free software; you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation; either version 2 of the License, or
*	at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*	GNU General Public License for more details.
*
***************************************************************************/

// Template engine
class template
{
	/**
	* Template variables and their replacements
	*
	* @var array
	*/
	var $tpl_vars;

	/**
	* Constructor
	*/
	function __construct($dir="html/")
	{
		$this->dir=$dir;
		$this->tp_file = false;
		$this->tpl_vars = array();
	}
	
	//edit eni
	function set_file($file){
			$this->tp_file=$file;
	}

	/**
	* Assign our variables and replacements
	*
	* @param  array  Template variables and replacements
	* @return none
	*/

	
		
	//edit eni
	function auto_assign($file=false,$string=false){
			if (!$file){ $file=$this->tp_file; }
			$file=$this->dir.$file;
			if($string){
				$content=$file;
			} else {
				if (!is_file($file))
					return false;
				$content=@file_get_contents($file);
				$file=$this->tp_file;
			}
			//ich weiss nicht: {[^\}]+\}  vs {([a-zA-Z0-9_-]*)\}
			preg_match_all("/\{([a-zA-Z0-9_-]*)\}/U",$content,$res_arr,PREG_PATTERN_ORDER);

			$assign = array();
			foreach ($res_arr[1] as $item){
					$text = __($item);
					if (!empty($text))
						$assign[$item] = $text;
			}
			$this->assign($assign);

	}
	//edit eni
	function assign_id(){
			$this->assign( array( "id"=>mysql_escape_string( $_REQUEST["id"] ) ) );
	}


	function assign($var_array)
	{
		// Must be an array...
		if (!is_array($var_array))
		{
			return false;
			//die('template::assign() - $var_array must be an array.');
		}
		$this->tpl_vars = array_merge($this->tpl_vars, $var_array);
	}

	/**
	* Parse the template file
	*
	* @param  string  Template file
	* @return string  Parsed template data
	*/
	function parse($tpl_file=false, $no_file=false)
	{
		if (!$tpl_file){ $tpl_file=$this->tp_file; }
		//var_dump($tpl_file);
		if (!$no_file)
		{// Make sure it's a valid file, and it exists
				$tpl_file=$this->dir.$tpl_file;
				if (!is_file($tpl_file))
				{
					//die('template::parse() - "' . $tpl_file . '" does not exist or is not a file.');
				}
				$tpl_content = @file_get_contents($tpl_file);
		}
		else
		{ //load string
				$tpl_content=$tpl_file;
		}

		foreach ($this->tpl_vars AS $var => $content)
		{
			$tpl_content = str_replace('{' . $var . '}', $content, $tpl_content);
		}
		return $tpl_content;
	}

	/**
	* Output the template
	*
	* @param string Template file
	*/
	function display($tpl_file)
	{
		echo $this->parse($tpl_file);
	}
	
	
	//edit eni: less code more cool (returns html)
	function go($file,$array=false,$nofile=false){
			if (is_array($array)){
					$this->assign($array);
			}
			if ($nofile){
					$this->assign_id();
					$this->auto_assign($file,true);
					return $this->parse($file,true);
			} else {
					$this->set_file($file);
					$this->auto_assign();
					$this->assign_id();
					return $this->parse();
			}
	}
	
}

?>
