<?php
if(!class_exists('Walker_Category_Checklist')) {
	include_once ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php';
}
class Walker_Project_Checklist extends Walker_Category_Checklist {

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.5.1
	 * @since 5.9.0 Renamed `$category` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 *
	 * @param string  $output            Used to append additional content (passed by reference).
	 * @param WP_Term $data_object       The current term object.
	 * @param int     $depth             Depth of the term in reference to parents. Default 0.
	 * @param array   $args              An array of arguments. See {@see wp_terms_checklist()}.
	 * @param int     $current_object_id Optional. ID of the current term. Default 0.
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		// Restores the more descriptive, specific name for use within this method.
		$category = $data_object;

		if ( empty( $args['taxonomy'] ) ) {
			$taxonomy = 'category';
		} else {
			$taxonomy = $args['taxonomy'];
		}

		if ( 'category' === $taxonomy ) {
			$name = 'post_category';
		} else {
			$name = 'tax_input[' . $taxonomy . ']';
		}

		$args['popular_cats'] = ! empty( $args['popular_cats'] ) ? array_map( 'intval', $args['popular_cats'] ) : array();

		$class = in_array( $category->term_id, $args['popular_cats'], true ) ? ' class="popular-category"' : '';

		$args['selected_cats'] = ! empty( $args['selected_cats'] ) ? array_map( 'intval', $args['selected_cats'] ) : array();

		if ( ! empty( $args['list_only'] ) ) {
			$aria_checked = 'false';
			$inner_class  = 'category';

			if ( in_array( $category->term_id, $args['selected_cats'], true ) ) {
				$inner_class .= ' selected';
				$aria_checked = 'true';
			}

			$output .= "\n" . '<li' . $class . '>' .
				'<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
				' tabindex="0" role="checkbox" aria-checked="' . $aria_checked . '">' .
				/** This filter is documented in wp-includes/category-template.php */
				esc_html( $category->description.' ('.$category->name.')' ) . '</div>';
		} else {
			$is_selected         = in_array( $category->term_id, $args['selected_cats'], true );
			$is_disabled         = ! empty( $args['disabled'] );
			$li_element_id       = wp_unique_prefixed_id( "in-{$taxonomy}-{$category->term_id}-" );
			$checkbox_element_id = wp_unique_prefixed_id( "in-{$taxonomy}-{$category->term_id}-" );

			$output .= "\n<li id='" . esc_attr( $li_element_id ) . "'$class>" .
				'<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" id="' . esc_attr( $checkbox_element_id ) . '"' .
				checked( $is_selected, true, false ) .
				disabled( $is_disabled, true, false ) . ' /> ' .
				/** This filter is documented in wp-includes/category-template.php */
				esc_html( $category->description.' ( '.$category->name.' )' ) . '</label>';
		}
	}

}