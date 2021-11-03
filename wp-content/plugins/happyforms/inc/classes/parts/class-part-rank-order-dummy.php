<?php

class HappyForms_Part_RankOrder_Dummy extends HappyForms_Form_Part {

	public $type = 'rank_order_dummy';

	public function __construct() {
		$this->label = __( 'Rank Order', 'happyforms' );
		$this->description = __( 'For collecting preferences between choices in numeric order.', 'happyforms' );
	}

}
