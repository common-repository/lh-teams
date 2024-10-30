<?php

class LH_clone_post_object_class {


	
	/**
	 * Copy an existing post to a new one
	 *
	 * @param int $old_post_id
	 * @param array $args Optional. Options for the new post.
	 * @return int the ID of the new post
	 */
private function clone_post( $old_post_id, $args = array() ) {





		# Ensure that the user can create this post type
		$post_type_object = get_post_type_object( get_post_type( $old_post_id ) );
		if ( ! current_user_can( $post_type_object->cap->create_posts ) ) {
			return;
		}



		if ( is_numeric( $old_post_id ) ) {
			$old_post = get_post( $old_post_id );

		}
		if ( ! is_object( $old_post ) ) {
			return false;
		}
		$args = wp_parse_args( $args, array(
			'post_status' => 'draft',
			'post_date' => false,
		) );
		$post_args = array(
			'menu_order'     => $old_post->menu_order,
			'comment_status' => $old_post->comment_status,
			'ping_status'    => $old_post->ping_status,
			'post_author'    => get_current_user_id(),
			'post_content'   => $old_post->post_content,
			'post_excerpt'   => $old_post->post_excerpt,
			'post_mime_type' => $old_post->post_mime_type,
			'post_parent'    => $old_post->post_parent,
			'post_password'  => $old_post->post_password,
			'post_status'    => $args['status'],
			'post_title'     => $old_post->post_title,
			'post_type'      => $old_post->post_type,
		);
		if ( $args['post_date'] ) {
			$post_args['post_date'] = $args['post_date'];
			$post_args['post_date_gmt'] = get_gmt_from_date( $args['post_date'] );
		}

		if ( $args['post_author'] ) { 	$post_args['post_author'] = $args['post_author']; }

		if ( $args['post_title'] ) { $post_args['post_title'] = $args['post_title']; }

		if ( $args['post_status'] ) { $post_args['post_status'] = $args['post_status']; }



		$post_id = wp_insert_post( $post_args );


		do_action( 'LH_post_object_inserted_post', $post_id, $old_post_id );
		return $post_id;
	}
	/**
	 * Copy terms from one post to another
	 *
	 * @param int $to_post_id The ID of the post to copy to
	 * @param int $from_post_id The ID of the post to copy from
	 * @return void
	 */
private function clone_terms( $to_post_id, $from_post_id ) {
		$post = get_post( $to_post_id );
		$taxonomies = apply_filters( 'LH_clone_post_object_clone_taxonomies', get_object_taxonomies( $post->post_type ), $post );

// a shitty bit of code to get this working with co authors plus removes author taxonomy from clone

if(($key = array_search('author', $taxonomies)) !== false) {
    unset($taxonomies[$key]);
}


		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $from_post_id, $taxonomy, array( 'orderby' => 'term_order', 'fields' => 'ids' ) );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$terms = array_map( 'intval', $terms );
				$terms = apply_filters( 'LH_clone_post_object_clone_terms', $terms, $to_post_id, $taxonomy );
				wp_set_object_terms( $to_post_id, $terms, $taxonomy );
			}
		}
	}
	/**
	 * Copy post meta from one post to another
	 *
	 * @param int $to_post_id The ID of the post to copy to
	 * @param int $from_post_id The ID of the post to copy from
	 * @return void
	 */
private function clone_post_meta( $to_post_id, $from_post_id ) {
		$post_meta = apply_filters( 'LH_clone_post_object_clone_post_meta', get_post_meta( $from_post_id ), $to_post_id, $from_post_id );
		$ignored_meta = apply_filters( 'LH_clone_post_object_ignored_meta', array(
			'_edit_lock',
			'_edit_last',
			'_wp_old_slug',
			'_wp_trash_meta_time',
			'_wp_trash_meta_status',
			'_previous_revision',
			'_wpas_done_all',
			'_encloseme',
			'_cr_original_post',
			'_cr_replace_post_id',
			'_cr_replacing_post_id',
		) );
		if ( empty( $post_meta ) ) {
			return;
		}
		foreach ( $post_meta as $key => $value_array ) {
			if ( in_array( $key, $ignored_meta ) ) {
				continue;
			}
			foreach ( (array) $value_array as $value ) {
				add_post_meta( $to_post_id, $key, maybe_unserialize( $value ) );
			}
		}
	}
	/**
	 * Perform any cleanup operations following a post cloning
	 *
	 * @param int $post_id The ID of the post to copy to
	 * @param int $old_post_id The ID of the post to copy from
	 * @return void
	 */
private function cleanup( $post_id, $old_post_id ) {
		# Record the original post ID so the clone can later replace the cloned
		add_post_meta( $post_id, '_lh_clone_post_object-original_post', $old_post_id );
	}

public function run_clone( $old_post_id, $args = array() ) {


if ($to_post_id = $this->clone_post( $old_post_id, $args )){

$this->clone_terms( $to_post_id, $old_post_id );

$this->clone_post_meta( $to_post_id, $old_post_id );

$this->cleanup( $to_post_id, $old_post_id );

return $to_post_id;

} else {

return false;

}



}


public function __construct() {



/* Don't do anything yet */

}


}



?>