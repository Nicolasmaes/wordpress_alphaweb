<?php

class HappyForms_Part_Rating_Dummy extends HappyForms_Form_Part {

	public $type = 'rating_dummy';

	public function __construct() {
		$this->label = __( 'Star Rating', 'happyforms' );
		$this->description = __( 'For collecting opinions using stars.', 'happyforms' );
	}

}
