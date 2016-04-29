<?php

// The file must have the type-[data-type].php filename format


class TPL_Static extends TPL_Data_Type {

	public $less_string	= true;


	// Container end of the form field
	public function form_field_after () {

		echo '</div>';

	}

}
