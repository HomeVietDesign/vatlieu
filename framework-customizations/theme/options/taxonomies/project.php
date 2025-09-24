<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = [
	// 'location' => [
	// 	'label' => 'Địa điểm',
	// 	'desc'  => '',
	// 	'type'  => 'multi-select',
	// 	'population' => 'taxonomy',
	// 	'source' => 'location',
	// 	'limit' => 1
	// ],
	'segment' => [
		'label' => 'Phân khúc',
		'desc'  => '',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'segment',
		'limit' => 1
	],
	'design_interior' => [
		'label' => 'Không gian nội thất',
		'desc'  => '',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'design_interior',
		'limit' => 0
	],
	'design_exterior' => [
		'label' => 'Không gian ngoại thất',
		'desc'  => '',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'design_exterior',
		'limit' => 0
	],
	// 'contractors' => [
	// 	'label' => 'Được đề cử',
	// 	'desc'  => '',
	// 	'type'  => 'multi-select',
	// 	'population' => 'posts',
	// 	'source' => 'contractor',
	// 	'limit' => 0,
	// 	'fw-storage' => array(
	// 		'type' => 'term-meta',
	// 		'term-meta' => 'contractors',
	// 	),
	// ],
	/*
	'files' => [
		'type' => 'multi-upload',
		'label' => 'File hồ sơ (pdf)',
		'images_only' => false,
		'files_ext' => ['pdf'],
	],
	'images' => [
		'type' => 'multi-upload',
		'label' => 'Ảnh thiết kế',
		'images_only' => true,
		'files_ext' => ['png', 'jpg', 'jpeg'],
	],
	'content' => array(
		'label' => 'Thông tin thêm',
		'desc'  => '',
		'type'  => 'wp-editor',
		'value' => '',
		'size' => 'large',
		'editor_height' => '400'
	),

	'divider' => [
		'type' => 'html',
		'label' => '',
		'html' => '<p></p>'
	]
	*/
];