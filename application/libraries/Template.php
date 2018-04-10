<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter Template Layout library
 *
 * @package     CodeIgniter
 * @author      Bo-Yi Wu <appleboy.tw@gmail.com>
 * @link        https://github.com/appleboy/CodeIgniter-Template
 */
class Template {

    /**
     * ci
     *
     * @param instance object
     */
    private static $_ci;

    /**
     * data
     *
     * @param array
     */
    private static $_data = array();

    /**
     * layout
     *
     * @param string
     */
    private static $_layout;

    /**
     * Scripts
     *
     * @param array
     */
    private static $_scripts = array();
    private static $_scripts_header = array();
    private static $_scripts_footer = array();

    /**
     * Styles
     *
     * @param array
     */
    private static $_styles = array();

    /**
     * Site title
     *
     * @param string
     */
    private static $_base_title;

    /**
     * Site title segmen
     *
     * @param array
     */
    private static $_title_segments = array();

    /**
     * Site title separator
     *
     * @param string
     */
    private static $_title_separator;

    /**
     * Site meta tag
     *
     * @param array
     */
    private static $_meta_tags = array();

    /**
     * constructor
     *
     * @param string $config
     */
    public function __construct($config = array()) {
        self::$_ci = get_instance();
        self::$_ci->load->helper('html');

        log_message('debug', 'Tempalte Class Initialized');

        empty($config) OR self::initialize($config);
    }

    /**
     * initialize
     *
     * @param array $config
     */
    public static function initialize($config) {
        self::$_layout = $config['template_layout'];

        foreach ($config as $key => $val) {
            if ($key == 'template_layout' AND $val != '') {
                self::$_layout = $val;
                continue;
            }

            if ($key == 'template_css' AND $val != '') {
                //add css
                foreach ($config['template_css'] as $href => $media) {
                    self::add_css($href, $media);
                }
                continue;
            }

            if ($key == 'template_js' AND $val != '') {
                //add js
                foreach ($config['template_js'] as $src => $value) {
                    self::add_js($src, $value);
                }
                continue;
            }

            if ($key == 'template_vars' AND $val != '') {
                //add var
                foreach ($config['template_vars'] as $key => $val) {
                    self::set($key, $val);
                }
                continue;
            }
            self::${'_' . $key} = $val;
        }
    }

    /**
     * Get data
     *
     * @param string $name
     * @param string $content
     */
    public static function get_data($index = "") {
        if (empty($index))
            return self::$_data;

        return isset(self::$_data[$index]) ? self::$_data[$index] : FALSE;
    }

    /**
     * Get scripts
     *
     * @param string $name
     * @param string $content
     */
    public static function get_scripts() {
        return self::$_scripts;
    }

    /**
     * Add a meta tag
     *
     * @param string $name
     * @param string $content
     */
    public static function add_meta_tag($name, $value, $key = "name") {
        self::$_meta_tags[] = '<meta ' . $key . '="' . $name . '" content="' . $value . '" />';

//        return $this;
    }

    /**
     * Add a title segment
     *
     * @param string $segment
     */
    public static function add_title_segment($segment) {
        self::$_title_segments[] = $segment;

//        return $this;
    }

    /**
     * Set the base title
     *
     * @param string $base_title
     */
    public static function set_base_title($base_title) {
        self::$_base_title = $base_title;

//        return $this;
    }

    /**
     * Set the title separator
     *
     * @param string $title_separator
     */
    public static function set_title_separator($title_separator) {
        self::$_title_separator = $title_separator;

//        return $this;
    }

    /**
     * set layout
     *
     * @param string $layout
     */
    public static function set_layout($layout) {
        self::$_layout = $layout;

//        return $this;
    }

    /**
     * Allows you to set a custom variable to be accessed in your template file.
     *
     * @param string $name
     * @param mixed  $data
     */
    public static function set($name, $data) {
        self::$_data[$name] = $data;

//        return $this;
    }

    /**
     * add css
     *
     * @param  string $path
     * @param  string $media
     * @param  array $link
     * @return void
     */
    public static function add_css($href = NULL, $media = 'screen', $link_param = NULL) {
        $href = ltrim($href, "/");

        $link = array(
            'href' => $href,
        );
        if ($link_param && is_array($link_param))
            $link = array_merge($link, $link_param);
        else
            $link = array_merge($link, array('rel' => 'stylesheet', 'type' => 'text/css'));

        if (!empty($media)) {
            $link['media'] = $media;
        }

        self::$_styles[] = link_tag($link);
    }

    /**
     * add js
     * add script on header or footer (before </body>)
     *
     * @param string  $src
     * @param boolean $is_footer
     */
    public static function add_js($src, $is_footer = FALSE) {
        self::$_scripts[] = $src;
        if (!$is_footer) {
            self::$_scripts_header[] = self::script_tag($src);
        } else {
            self::$_scripts_footer[] = self::script_tag($src);
        }
    }

    /**
     * script_tag
     *
     * Generates an javascript heading tag. First param is the data.
     *
     * @access private
     * @param  string
     * @return string
     */
    private static function script_tag($src = NULL) {
        if (isset($src) and ! empty($src)) {
            return '<script src="' . $src . '" type="text/javascript"></script>';
        }

        return "";
    }

    /**
     * render
     *
     * @param  string  $view
     * @param  array   $data
     * @param  boolean $return
     * @return string
     */
    public static function render($view = '', $data = array(), $return = FALSE) {
        if (empty($view)) {
            $view = self::$_ci->router->method;
        }
        // check $data is array()
        if (!is_array($data)) {
            $data = array();
        }

        // merge template variable
        $data = array_merge($data, self::$_data);

        self::set('meta_tag', implode("\r\n", self::$_meta_tags) . "\r\n");
        self::set('styles', implode("\r\n", self::$_styles) . "\r\n");
        self::set('scripts_header', implode("\r\n", self::$_scripts_header) . "\r\n");
        self::set('scripts_footer', implode("\r\n", self::$_scripts_footer) . "\r\n");
        self::set('lang', str_replace('_', '-', self::$_ci->config->item('language')));
        self::set('meta_charset', strtolower(self::$_ci->config->item('charset')));
        self::set('content', self::$_ci->load->view($view, $data, TRUE));

        // handle site title
        self::$_data['site_title'] = '';
        if (count(self::$_title_segments) > 0) {
            self::$_data['site_title'] .= implode(self::$_title_separator, array_reverse(self::$_title_segments)) . self::$_title_separator;
        }
        self::$_data['site_title'] .= self::$_base_title;

        if ($return === TRUE) {
            $out = self::$_ci->load->view(self::$_layout, self::$_data, $return);

            return $out;
        }
        self::$_ci->load->view(self::$_layout, self::$_data);
    }

}

//--------------------------------------------------------------------

/**
 * A shorthand method that allows views (from the current/default themes)
 * to be included in any other view.
 *
 * This function also allows for a very simple form of mobile templates. If being
 * viewed from a mobile site, it will attempt to load a file whose name is prefixed
 * with 'mobile_'. If that file is not found it will load the regular view.
 *
 * @access  public
 * @example Rendering a view named 'index', the mobile version would be 'mobile_index'.
 *
 * @param string $view          The name of the view to render.
 * @param array  $data          An array of data to pass to the view.
 * @param bool   $ignore_mobile If TRUE, will not change the view name based on mobile viewing. If FALSE, will attempt to load a file prefixed with 'mobile_'
 *
 * @return string
 */
function theme_view($view = NULL, $data = NULL, $ignore_mobile = FALSE) {
    if (empty($view))
        return '';

    $ci = & get_instance();

    $output = '';

    // If we're allowed, try to load the mobile version
    // of the file.
    if (!$ignore_mobile) {
        $ci->load->library('user_agent');

        if ($ci->agent->is_mobile()) {
            Template::load_view('mobile_' . $view, $data, NULL, TRUE, $output);
        }
    }

    // If output is empty, then either no mobile file was found
    // or we weren't looking for one to begin with.
    if (empty($output)) {
        Template::load_view($view, $data, NULL, TRUE, $output);
    }

    return $output;
}

//end theme_view()
//--------------------------------------------------------------------

/**
 * A simple helper method for checking menu items against the current
 * class that is running.
 *
 * <code>
 *   <a href="<?php echo site_url(SITE_AREA . '/content'); ?>" <?php echo check_class(SITE_AREA . '/content'); ?> >
 *    Admin Home
 *  </a>
 *
 * </code>
 * @access public
 *
 * @param string $item       The name of the class to check against.
 * @param bool   $class_only If TRUE, will only return 'active'. If FALSE, will return 'class="active"'.
 *
 * @return string Either <b>class="active"</b> or an empty string.
 */
function check_class($item = '', $class_only = FALSE) {
    $ci = & get_instance();

    if (strtolower($ci->router->fetch_class()) == strtolower($item)) {
        return $class_only ? 'active' : 'class="active"';
    }

    return '';
}

//end check_class()
//--------------------------------------------------------------------

/**
 * A simple helper method for checking menu items against the current
 * class' method that is being executed (as far as the Router knows.)
 *
 * @access public
 *
 * @param string $item The name of the method to check against. Can be an array of names.
 *
 * @return string Either <b>class="active"</b> or an empty string.
 */
function check_method($item) {
    $ci = & get_instance();

    $items = array();

    if (!is_array($item)) {
        $items[] = $item;
    } else {
        $items = $item;
    }

    if (in_array($ci->router->fetch_method(), $items)) {
        return 'class="active"';
    }

    return '';
}

//end check_method()
//--------------------------------------------------------------------

/**
 * Will create a breadcrumb from either the uri->segments or
 * from a key/value paired array passed into it.
 *
 * @access public
 *
 * @param array $my_segments (optional) Array of Key/Value to make Breadcrumbs from
 * @param bool  $wrap        (boolean)  Set to TRUE to wrap in un-ordered list
 * @param bool  $echo        (boolean)  Set to TRUE to echo the output, set to FALSE to return it.
 *
 * @return string A Breadcrumb of your page structure.
 */
function breadcrumb($my_segments = NULL, $wrap = FALSE, $echo = TRUE) {
    $ci = & get_instance();

    $output = '';

    if (!class_exists('CI_URI')) {
        $ci->load->library('uri');
    }


    if ($ci->config->item('breadcrumb_symbol', 'template') == '') {
        $seperator = '&nbsp;&raquo;&nbsp;';
    } else {
        $seperator = $ci->config->item('breadcrumb_symbol');
    }

//	if ($wrap === TRUE)
//	{
//		$seperator = '<span class="divider">' . $seperator . '</span>' . PHP_EOL;
//	}


    if (empty($my_segments) || !is_array($my_segments)) {
        $segments = $ci->uri->segment_array();
        $total = $ci->uri->total_segments();
    } else {
        $segments = $my_segments;
        $total = count($my_segments);
    }

    $in_admin = (bool) (is_array($segments) && in_array(SITE_AREA, $segments));

    if ($in_admin == TRUE) {
        $home_link = site_url(SITE_AREA);
    } else {
        $home_link = site_url();
    }

    if ($wrap === TRUE) {
        $output = '<div class="breadcrumb">' . PHP_EOL;
//		$output .= '<a href="'.$home_link.'"><i class="icon-home">&nbsp;</i></a> '.$seperator . PHP_EOL;
    } else {
        $output = '<a href="' . $home_link . '">home</a> ' . $seperator;
    }

    $url = '';
    $count = 0;

    // URI BASED BREADCRUMB
    if (empty($my_segments) || !is_array($my_segments)) {
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $count += 1;

            if ($count == $total) {
                if ($wrap === TRUE) {
                    $output .= '<li class="active">' . ucwords(str_replace('_', ' ', $segment)) . '</li>' . PHP_EOL;
                } else {
                    $output .= ucwords(str_replace('_', ' ', $segment)) . PHP_EOL;
                }
            } else {
                if ($wrap === TRUE) {
                    $output .= '<li><a href="' . $url . '">' . str_replace('_', ' ', ucwords(mb_strtolower($segment))) . '</a>' . $seperator . '</li>' . PHP_EOL;
                } else {
                    $output .= '<a href="' . $url . '">' . str_replace('_', ' ', ucwords(mb_strtolower($segment))) . '</a>' . $seperator . PHP_EOL;
                }
            }
        }
    } else {
        // USER-SUPPLIED BREADCRUMB
        foreach ($my_segments as $title => $uri) {
            $url .= '/' . $uri;
            $count += 1;

            if ($count == $total) {
                if ($wrap === TRUE) {
                    $output .= str_replace('_', ' ', $title) . PHP_EOL;
                } else {
                    $output .= str_replace('_', ' ', $title);
                }
            } else {

                if ($wrap === TRUE) {
                    $output .= '<a href="' . site_url($url) . '">' . str_replace('_', ' ', ucwords(mb_strtolower($title))) . '</a>' . $seperator . '' . PHP_EOL;
                } else {
                    $output .= '<a href="' . site_url($url) . '">' . str_replace('_', ' ', ucwords(mb_strtolower($title))) . '</a>' . $seperator . PHP_EOL;
                }
            }
        }
    }

    if ($wrap === TRUE) {
        $output .= PHP_EOL . '</div>' . PHP_EOL;
    }

    unset($in_admin, $seperator, $url, $wrap);

    if ($echo === TRUE) {
        echo $output;
        unset($output);
    } else {
        return $output;
    }
}

//end breadcrumb()

//---------------------------------------------------------------

