<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CI_Mybreadcrumb {

	private $breadcrumbs = array();

	function add($title, $href, $class=""){		
		if (!$title OR !$href) return;
		$this->breadcrumbs[] = array('title' => $title, 'href' => $href, 'class' => $class);
	}
	
	function render(){

		$output = '<ol class="breadcrumb float-sm-right">';
		$count = count($this->breadcrumbs)-1;
		foreach($this->breadcrumbs as $index => $breadcrumb){

			if($index == $count){
				$output .= '<li class="breadcrumb-item active">';
				if(!empty($breadcrumb['class'])) $output.= "<i class='fa ".$breadcrumb['class']."'></i> ";
				$output .= $breadcrumb['title'];
				$output .= '</li>';
			}else{
				$output .= '<li class="breadcrumb-item">';
				$output .= '<a href="'.$breadcrumb['href'].'">';
				if(!empty($breadcrumb['class'])) $output.= "<i class='fa ".$breadcrumb['class']."'></i> ";
				$output .= $breadcrumb['title'];
				$output .= '</a>';
				$output .= '</li>';
			}
			
		}
		$output .= "</ol>";

		return $output;
	}

}