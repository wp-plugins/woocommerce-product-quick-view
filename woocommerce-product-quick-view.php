<?php
/*
  Plugin Name: Woocommerce Product Quick View
  Plugin URI: http://www.unicodesystems.in
  Description: This plugin is used for adding the quick view functionality to your woocommerce store. Woocommerce plugin is pre-requisite for this plugin to run.
  Version: 1.1.3
  Author: Harshita
  Author URI: http://www.unicodesystems.in

 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!defined('QV_PLUGIN_PATH'))
    define('QV_PLUGIN_PATH', plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . '/quick-view';

if (!defined('QV_PLUGIN_URL'))
    define('QV_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!defined('QV_PLUGIN_JS_URL'))
    define('QV_PLUGIN_JS_URL', QV_PLUGIN_URL . 'js');

if (!defined('QV_PLUGIN_CSS_URL'))
    define('QV_PLUGIN_CSS_URL', QV_PLUGIN_URL . 'css');

register_activation_hook(__FILE__, 'child_plugin_activate');

function child_plugin_activate() {

    if (((!is_plugin_active('woocommerce-master/woocommerce.php')) and (!is_plugin_active('woocommerce/woocommerce.php'))) and current_user_can('activate_plugins')) {
        wp_die('Sorry, this plugin requires Woocommerce Plugin to be installed and active. <br><a href="' . admin_url('plugins.php') . '">&laquo; Return to Plugins</a>');
    }
}
add_action('admin_menu', 'qv_admin_actions'); // Displays link to our settings page in the admin menu

function qv_admin_actions() {
    $page_hook_suffix = add_options_page("Quick View Settings", "Quick View Settings", 'manage_options', "quick-view", "qv_admin");

    add_action('admin_print_scripts-' . $page_hook_suffix, 'qv_admin_scripts');
}


function qv_admin_scripts() {
   wp_enqueue_script('popup', QV_PLUGIN_JS_URL . '/jscolor.js', array('jquery'));
}

function qv_admin() { // Function that includes the actual settings page
    include('inc/admin.php');
}

add_action('wp_enqueue_scripts', 'enqueue_scripts');


function enqueue_scripts() {

    wp_enqueue_script('popup', QV_PLUGIN_JS_URL . '/jquery.colorbox-min.js', array('jquery'));
     wp_enqueue_script('carousel', QV_PLUGIN_JS_URL . '/owl.carousel.min.js', array('jquery'));

    wp_enqueue_style('popup', QV_PLUGIN_CSS_URL . '/colorbox.css');
    wp_enqueue_style('carousel', QV_PLUGIN_CSS_URL . '/owl.carousel.css');
    wp_enqueue_style('stylesheet', QV_PLUGIN_CSS_URL . '/qv-style.css');
}

add_action('woocommerce_before_single_product_summary', 'addingGallery');

function addingGallery() {
    
    
    
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $tn = $('.thumbnails');
            var $ti = $('.images');
            var src;
            $tn.find('a:first').addClass('selected');
            $tn.find('a').click(function(e) {
                e.preventDefault();
                $tn.find('a').removeClass('selected');
                src = $(this).attr('href');
                $(this).addClass('selected');
                $ti.find('a.active').attr('href', src);
                $ti.find('a.active img').attr('src', src);
            });
        });
    </script>


    <?php
}

add_action('woocommerce_before_shop_loop', 'addingScript');

function addingScript() {

    wp_dequeue_script('woocommerce');
    wp_dequeue_script('prettyPhoto');
    wp_dequeue_script('prettyPhoto-init');
    wp_dequeue_style('woocommerce_prettyPhoto_css');

    if (!get_option('quick_view_text')) {
        $text = 'Quick View';
        update_option('quick_view_text', $text);
    } else {
        $text = get_option('quick_view_text');
    }

    if (!get_option('quick_view_color')) {
        $color = '#ec4918';
        update_option('quick_view_color', $color);
    } else {
        $color = get_option('quick_view_color');
    }
    
    if (!get_option('quick_view_font_color')) {
        $fontcolor = '#ffffff';
        update_option('quick_view_font_color', $fontcolor);
    } else {
        $fontcolor = get_option('quick_view_font_color');
    }

//    $text = apply_filters('quick_view_text', 'Quick View');
    ?>



    <script type="text/javascript">

        jQuery(document).ready(function($) {
            function callfancy(url) {
                var content;
                $('#view-content').html('');
                $('#view-content').load(url, function(response, status, xhr) {
                    if (status == "error") {
                        content = "Sorry but there was an error: ";
                    } else {
                        jQuery(this).find('.products,#secondary,.woocommerce-tabs,header,footer,.woocommerce-breadcrumb').remove();
                        var product = jQuery(this).find('.product').clone();
                        jQuery(this).find('div:first').html(product);
                        jQuery(this).find('.product').unwrap();
                        var $tn = jQuery(this).find('.thumbnails');
                        var $ti = jQuery(this).find('.images');
                        var rlink = jQuery(this).find('.woocommerce-review-link').attr('href');
                        jQuery(this).find('.woocommerce-review-link').attr('href', url + rlink);
                        var src;
                        $ti.find('a').each(function() {
                            src = jQuery(this).attr('href');
                            jQuery(this).find('img').attr('src', src);
                        });
                        var tia = jQuery(this).find('.images a:first').clone();
                        $tn.prepend(tia);
                        $ti.find('a:first').addClass('active');
                        $tn.find('a').show().removeClass('first').removeClass('last');
                        content = jQuery('#view-content').html();
                        $.colorbox({
                            width: '80%',
                            height: '80%',
                            overlayClose: false,
                            reposition: true,
                            html: content,
                            onOpen: function() {
                                jQuery('#cboxOverlay').remove();
                                jQuery('#colorbox').addClass('quickview-product-box');
                            },
                            onClosed: function() {
                                jQuery('.overlay').remove();
                                jQuery('.quantity .increment').die('click');
                                jQuery('.quantity .decrement').die('click');
                            },
                            onComplete: function() {
                                var owl = $("#colorbox .thumbnails");
                                if (owl.find('a').size() > 3) {
                                    owl.owlCarousel({
                                        navigation: true, // Show next and prev buttons
                                        slideSpeed: 300,
                                        paginationSpeed: 400,
                                        items: 3
                                    });
                                }
                                setTimeout(function() {
                                    jQuery('.quantity .minus').remove();
                                    jQuery('.quantity .plus').remove();
                                }, 500);
                                jQuery('div.quantity').append('<input type="button" value="+" class="increment" />').prepend('<input type="button" value="-" class="decrement" />');
                                jQuery('.quantity .qty').attr('value', 1);
                                jQuery('.quantity .decrement').live('click', function(e) {
                                    e.preventDefault();
                                    var value = jQuery('.quantity .qty').attr('value');
                                    if (value > 1) {
                                        value--;
                                        jQuery('.quantity .qty').attr('value', value);
                                        jQuery('.quantity .qty').val(value);
                                        jQuery('.quantity .qty').trigger('change');
                                    }
                                    return false;
                                });
                                jQuery('.quantity .increment').live('click', function(e) {
                                    e.preventDefault();
                                    var value = jQuery('.quantity .qty').attr('value');
                                    value++;
                                    jQuery('.quantity .qty').attr('value', value);
                                    jQuery('.quantity .qty').val(value);
                                    jQuery('.quantity .qty').trigger('change');
                                    return false;
                                });
                            }
                        });
                    }
                });
            }


            $('#content').append('<div id="view-content" style="display:none;"><div class="page-content"></div></div>');
            var text = '<?php echo $text; ?>';
            var color = '<?php echo $color; ?>';
            var fontcolor = '<?php echo $fontcolor; ?>';
            $('.product').each(function() {
                var id = $(this).find('a.add_to_cart_button').attr('data-product_id');
                var $af = $(this).find('a:first');
                var href = $af.attr('href');
               $(this).prepend('<span class="overlay-view-more id-' + id + '" style="display:none;"><a href="' + href + '" class="view-more" data-link="' + href + '" data-id="' + id + '" style="background:' + color + ';color:'+fontcolor+';"><span class="view-icon">' + text + '</span></a></span>');
            });
            var $product = $('.product');
            $product.mouseenter(function() {
                $(this).find('.overlay-view-more').addClass('current').show();
            });
            $product.mouseleave(function() {
                $(this).find('.overlay-view-more').removeClass('current').hide();
            });
            var pid, nid, index, pi, ni;
            var lst = $('li.product:last').index();
            var frt = $('li.product:first').index();
            var $product = $('.product');
            function checkPi() {
                if (pi < frt) {
                    index = frt;
                    pi = lst;
                }
            }
            function checkNi() {
                if (ni > lst) {
                    index = lst;
                    ni = frt;
                }
            }
            $product.on('click', '.view-more', function(e) {
                e.preventDefault();
                $('body').prepend('<div class="overlay"><a href="" class="prev-prod">Prev</a><a href="" class="next-prod">Next</a></div>');
                index = $(this).parents('li').index();
                pi = index - 1;
                ni = index + 1;
                pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                var url = $(this).attr('data-link');
                checkPi();
                checkNi();
                callfancy(url);
                $('.input-text.qty').attr('size', 1);
            });


            $('.prev-prod').live('click', function(e) {
                e.preventDefault();
                if ((typeof pid !== 'undefined') || pid > 0) {
                    $(this).removeClass('disabled');
                    var url = $('li.post-' + pid).find('a.view-more').attr('data-link');
                    index = pi;
                    ni = index + 1;
                    pi--;
                    checkPi();
                    jQuery('.quantity .decrement').die('click');
                    jQuery('.quantity .increment').die('click');
                    pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                    nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                    callfancy(url);
                }
                return false;
            });
            $('.next-prod').live('click', function(e) {
                e.preventDefault();
                if (typeof nid !== 'undefined') {
                    $(this).removeClass('disabled');
                    var url = $('li.post-' + nid).find('a.view-more').attr('data-link');
                    index = ni;
                    pi = index - 1;
                    ni++;
                    checkNi();
                    jQuery('.quantity .decrement').die('click');
                    jQuery('.quantity .increment').die('click');
                    pid = $('li.product:eq(' + pi + ')').find('a.add_to_cart_button').attr('data-product_id');
                    nid = $('li.product:eq(' + ni + ')').find('a.add_to_cart_button').attr('data-product_id');
                    callfancy(url);
                }
                return false;
            });
        });
    </script>    

    <?php
}

