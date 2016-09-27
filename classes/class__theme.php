<?
class Theme {
    public $theme_name;
    public $theme_version;

    public function __construct( $theme_name, $theme_version ) {

        $this->theme_name       = $theme_name;
        $this->theme_version    = $theme_version;

        // Theme setup
        $this->theme_setup();

        // Adding actions
        $this->add_actions();
    }

    /**
     * Initial theme setup
     */
    public function theme_setup() {

        /**
         * Set the content width based on the theme's design and stylesheet.
         *
         * @since Diamond 1.0
         */
        if ( ! isset( $content_width ) ) {
            $content_width = 660;
        }

        /**
         * Diamond only works in WordPress 4.1 or later.
         */
        if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {
            require get_template_directory() . '/inc/back-compat.php';
        }

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on diamond, use a find and replace
         * to change 'diamond' to the name of your theme in all the template files
         */
        load_theme_textdomain( 'diamond', get_template_directory() . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support( 'title-tag' );

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support( 'post-thumbnails' );
        //set_post_thumbnail_size( 234.797, 510, true );

        // This theme uses wp_nav_menu() in two locations.
        register_nav_menus( array(
            'primary' => __( 'Sidebar Menu',      'diamond' ),
            'second'  => __( 'Header Menu', 'diamond' ),
        ) );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support( 'html5', array(
            'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
        ) );

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support( 'post-formats', array(
            'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
        ) );

        /*
         * This theme styles the visual editor to resemble the theme style,
         */
        add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css' ) );
    }


    /**
     * Adding different actions
     */
    public function add_actions() {

        // Enqueue and add custom styles and scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'diamond_scripts' ) );

        // Register Menu
        add_action( 'init', array( $this, 'register_menus' ) );


        // Register sidebar widgets
        add_action( 'widgets_init', array( $this, 'diamond_widgets_init' ) );

        // Load fonts from Google
        add_action( 'wp_enqueue_scripts', array( $this, 'diamond_load_fonts') );


        add_filter( 'get_search_form', array( $this, 'diamond_search_form_modify') );



        // -----------------------------------------------------------

        // The page title <title></title>
        add_filter( 'wp_title', array( $this, 'page_title' ) );

        // Custom excerpt length
        add_filter( 'excerpt_legth', array( $this, 'excerpt_length') );

    }

    /**
     * Enqueue scripts and styles.
     *
     * @since Diamond 1.0
     */
    function diamond_scripts() {

        wp_enqueue_style( 'diamond-genericons', get_template_directory_uri() . '/all.css', array(), '3.2' );

        // Add Genericons, used in the main stylesheet.
        wp_enqueue_style( 'diamond-genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.2' );

        // Add font-awesome, used in the main stylesheet.
        wp_enqueue_style( 'diamond-font-awesome', get_template_directory_uri() . '/font-awesome/font-awesome.css', array(), '3.2' );

        wp_enqueue_script( 'diamond-menu', get_template_directory_uri() . '/js/menu.js', array('jquery'), '20151014', false );
        wp_enqueue_script( 'diamond-js', get_template_directory_uri() . '/js/diamond-js.js', array(), '20150418', true );

        // Load our main stylesheet.
        wp_enqueue_style( 'diamond-style', get_stylesheet_uri() );

        // Load the Internet Explorer specific stylesheet.
        wp_enqueue_style( 'diamond-ie', get_template_directory_uri() . '/css/ie.css', array( 'diamond-style' ), '20141010' );
        wp_style_add_data( 'diamond-ie', 'conditional', 'lt IE 9' );

        // Load the Internet Explorer 7 specific stylesheet.
        wp_enqueue_style( 'diamond-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'diamond-style' ), '20141010' );
        wp_style_add_data( 'diamond-ie7', 'conditional', 'lt IE 8' );

        wp_enqueue_script( 'diamond-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20141010', true );

        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }

        if ( is_singular() && wp_attachment_is_image() ) {
            wp_enqueue_script( 'diamond-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20141010' );
        }

        wp_enqueue_script( 'diamond-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
        wp_localize_script( 'diamond-script', 'screenReaderText', array(
            'expand'   => '<span class="screen-reader-text">' . __( 'expand child menu', 'diamond' ) . '</span>',
            'collapse' => '<span class="screen-reader-text">' . __( 'collapse child menu', 'diamond' ) . '</span>',
        ) );

        // Load grid home posts javascript
        wp_enqueue_script( 'diamond-mansory-grid', get_template_directory_uri() . '/js/Packery-grid.js', array( 'jquery' ), '20162201', true );
    }

    public function register_menus() {

        // Register main menu
        register_nav_menus(
            array(
                'primary' => __( 'Main Menu', 'theme-name' )
            )
        );

        // Register footer menu
        register_nav_menus(
            array(
                'footer' => __( 'Footer Menu', 'theme-name' )
            )
        );
    }

    /**
     * Adding fonts from google API.
     *
     * @since Diamond 1.0
     */
    function diamond_load_fonts() {
        wp_register_style('diamond-googleFonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700');
        wp_enqueue_style( 'diamond-googleFonts');
    }


    /**
     * Register widget area.
     *
     * @since Diamond 1.0
     *
     * @link https://codex.wordpress.org/Function_Reference/register_sidebar
     */
    function diamond_widgets_init() {

        register_sidebar( array(
            'name'          => __( 'Widget Area', 'diamond' ),
            'id'            => 'sidebar-1',
            'description'   => __( 'Add widgets here to appear in your sidebar.', 'diamond' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );

        register_sidebar( array(
            'name'          => __( 'Widget right', 'diamond' ),
            'id'            => 'sidebar-2',
            'description'   => __( 'Add widgets here to appear in your sidebar.', 'diamond' ),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ) );

    }

    public static function page_title( $title, $sep = ' - ' ) {
        global $page, $paged;

        if ( is_feed() )
            return $title;

        $site_description = get_bloginfo( 'description' );

        $filtered_title = $title . get_bloginfo( 'name' );
        $filtered_title .= ( ! empty( $site_description ) && ( is_home() || is_front_page() ) ) ? $sep .
            $site_description : '';
        $filtered_title .= ( 2 <= $paged || 2 <= $page ) ? $sep . sprintf( __( 'Page %s', 'theme-name' ), max(
                $paged, $page ) ) : '';

        return $filtered_title;


    }

    public static function the_author() {
        $link = get_author_posts_url( get_the_author_meta ( 'ID') );
        echo '<a href="' . $link . '">' . get_the_author() . '</a>';
    }

    public static function diamond_excerpt($limit) {
        $excerpt = explode(' ', get_the_excerpt(), $limit);
        if (count($excerpt)>=$limit) {
            array_pop($excerpt);
            $excerpt = implode(" ",$excerpt)/*.'<div id="teaser-more"><a href="'. get_permalink( get_the_ID() ) .'">Continue Reading</a></div>'*/;
        } else {
            $excerpt = implode(" ",$excerpt);
        }
        $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
        return $excerpt;
    }

    /**
     * Add a `screen-reader-text` class to the search form's submit button.
     *
     * @since Diamond 1.0
     *
     * @param string $html Search form HTML.
     * @return string Modified search form HTML.
     */
    function diamond_search_form_modify( $html ) {
        return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
    }
}
?>