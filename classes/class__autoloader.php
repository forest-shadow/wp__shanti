<?
/**
 * Autoloads Theme WP classes using WordPress convention.
 *
 * @author Somebuddy
 */
class Autoloader {
    /**
     * Registers Autoloader as an SPL autoloader.
     *
     * @param boolean $prepend
     */
    public static function register( $prepend = false ) {
        if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
            spl_autoload_register( array( new self, 'autoload' ), true, $prepend);
        } else {
            spl_autoload_register( array( new self, 'autoload' ) );
        }
    }

    /**
     * Handles autoloading of MyPlugin classes.
     *
     * @param string $class
     */
    public static function autoload( $class ) {

        $file = dirname(__FILE__) . '/class__' . strtolower( str_replace( array('_', "\0"), array( '-', '' ), $class )
                . '.php' );

        if ( is_file( $file ) ) {
            require_once $file;
        }
    }
}
?>