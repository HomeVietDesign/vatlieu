<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'general' => array(
		'type' => 'tab',
		'title' => 'Cài đặt chung',
		'options' => array(
			// 'hotline' => array(
			// 	'label' => 'Số hotline',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => ''
			// ),
			// 'hotline_label' => array(
			// 	'label' => 'Nhãn hotline',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => ''
			// ),
			// 'zalo' => array(
			// 	'label' => 'Số zalo',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => ''
			// ),
			// 'zalo_label' => array(
			// 	'label' => 'Nhãn zalo',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => ''
			// ),
			// 'admin_email_address' => array(
			// 	'label' => 'Admin email address',
			// 	'desc'  => '',
			// 	'type'  => 'text',
			// 	'value' => ''
			// ),
			'recommend_label' => array(
				'label' => 'Nhãn nhóm nhà thầu đề cử cho dự án',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'Được đề cử'
			),
			'recommend_label_local' => array(
				'label' => 'Nhãn nhóm nhà thầu tại địa phương',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'Tại địa phương'
			),
			'cf_turnstile_key' => array(
				'label' => __( 'Turnstile key' ),
				'type'  => 'text',
			),
			'cf_turnstile_secret' => array(
				'label' => __( 'Turnstile secret' ),
				'type'  => 'text',
			),
		),
	),
);