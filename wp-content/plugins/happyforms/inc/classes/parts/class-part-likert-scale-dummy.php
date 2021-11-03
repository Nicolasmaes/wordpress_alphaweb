<?php

class HappyForms_Part_LikertScale_Dummy extends HappyForms_Form_Part {

	public $type = 'likert_scale_dummy';

	public function __construct() {
		$this->label = __( 'Likert Scale', 'happyforms' );
		$this->description = __( 'For collecting opinions using a numeric likert scale.', 'happyforms' );
	}

}
