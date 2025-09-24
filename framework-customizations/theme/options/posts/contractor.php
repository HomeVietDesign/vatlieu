<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	'info'=>array(
		'context' => 'advanced',
		'title'   => 'Thông tin',
		'type'    => 'box',
        'options' => array(
			'_phone_number' => array(
				'label' => 'Số điện thoại',
				'type' => 'text'
			),
		),
    ),

);
