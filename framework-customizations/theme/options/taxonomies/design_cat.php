<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */
$taxonomies = get_taxonomies( array( 'public' => true ), 'names' );
$taxes = [];
foreach ( $taxonomies as $taxonomy ) {
	if ( !in_array($taxonomy, ['post_format','category','post_tag','project','design_cat']) ) {
		$tax = get_taxonomy( $taxonomy );
		$taxes[ $taxonomy ] = $tax->label;
	}
}
//debug_log($taxes);

$options = [
	'tax_active' => [
		'label' => 'Mục con',
		'desc'  => '',
		'type'  => 'multi-select',
		'population' => 'array',
		'source' => '',
		'choices' => $taxes,
		'limit' => 100
	]
];