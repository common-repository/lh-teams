<?php 
class LH_Posttype_Retrans {
    // store the options
    protected $params;

    /**
     * Set up basic information
     * 
     * @param  array $options
     * @return void
     */
    public function __construct( array $options )
    {
        $defaults = array (
            'domain'       => 'default'
        ,   'context'      => 'backend'
        ,   'replacements' => array ()
        ,   'post_type'    => array ( 'post' )
        );

        $this->params = array_merge( $defaults, $options );

        // When to add the filter
        $hook = 'backend' == $this->params['context'] 
            ? 'admin_head' : 'template_redirect';

        add_action( $hook, array ( $this, 'register_filter' ) );
    }

    /**
     * Conatiner for add_filter()
     * @return void
     */
    public function register_filter()
    {
        add_filter( 'gettext', array ( $this, 'translate' ), 10, 3 );
    }

    /**
     * The real working code.
     * 
     * @param  string $translated
     * @param  string $original
     * @param  string $domain
     * @return string
     */
    public function translate( $translated, $original, $domain ){
        // exit early
        if ( 'backend' == $this->params['context'] )
        {
            global $post_type;

            if ( ! empty ( $post_type ) 
                && ! in_array( $post_type, $this->params['post_type'] ) )
            {
                return $translated;
            }
        }

        if ( $this->params['domain'] !== $domain )
        {
            return $translated;
        }

        // Finally replace
        return strtr( $original, $this->params['replacements'] );
    }
}

?>