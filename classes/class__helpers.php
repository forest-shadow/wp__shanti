<?
class Helpers {
    /**
     * 1.0. Pre-formatted var_dump
     *
     * @uses WP_Query
     * @uses get_queried_object()
     * @extends get_the_ID()
     * @see get_the_ID()
     *
     * @return int
     */
    public static function gt_var_dump( $param ) {
        echo "<pre>";
        var_dump( $param );
        echo "</pre>";
    }


    /**
     * 2.0. Gets the ID of the post, even if it's not inside the loop.
     *
     * @uses WP_Query
     * @uses get_queried_object()
     * @extends get_the_ID()
     * @see get_the_ID()
     *
     * @return int
     */
    function gt_get_the_ID() {
        if ( in_the_loop() ) {
            $post_id = get_the_ID();
        } else {
            /** @var $wp_query wp_query */
            global $wp_query;
            $post_id = $wp_query->get_queried_object_id();
        }
        return $post_id;
    }
}
?>