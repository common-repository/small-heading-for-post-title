<?php
/*
Plugin Name: Small Heading For Post Title
Plugin URI: https://github.com/mostafa272/Small-Heading-For-Post-Title
Description: The Small Heading For Post Title is a simple plugin for displaying small headings before or after post title.
Version: 1.0
Author: Mostafa Shahiri<mostafa2134@gmail.com>
Author URI: https://github.com/mostafa272
*/
/*  Copyright 2009  Mostafa Shahiri(email : mostafa2134@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
register_uninstall_hook( __FILE__, 'shfpt_SmallHeadingPostsTitle_uninstall' );
add_action('admin_menu', 'shfpt_admin_setup_menu');
add_action('admin_init', 'shfpt_admin_register_settings');
//creating menu and settings form
function shfpt_admin_setup_menu(){
add_menu_page( 'Small Heading For Post Title Plugin', 'Small Heading For Post Title', 'manage_options', 'shfpt-admin-option', 'shfpt_menu_init' );
}
function shfpt_admin_register_settings(){

    register_setting('shfpt-options-settings', 'style_shfpt');
     register_setting('shfpt-options-settings', 'home_shfpt');
     register_setting('shfpt-options-settings', 'frontpage_shfpt');
     register_setting('shfpt-options-settings', 'archive_shfpt');
     register_setting('shfpt-options-settings', 'category_shfpt');
     register_setting('shfpt-options-settings', 'post_shfpt');
}
function shfpt_menu_init(){
if(current_user_can('manage_options'))
{ ?>
        <h1>Small Heading For Post Title Setting</h1>
        <form  method="post" action="options.php" >
        <?php settings_fields( 'shfpt-options-settings' ); ?>
    <?php do_settings_sections( 'shfpt-options-settings' ); ?>

    <p style="margin-left:20px;font-weight:bold;"><label>Style:</label></p>
    <p> <textarea id="style_shfpt" style="margin-left:20px;" rows="7" cols="50" name="style_shfpt" ><?php echo get_option( 'style_shfpt' ); ?></textarea> </p>
<p><input type="checkbox" id="home_shfpt" name="home_shfpt" style="margin-left:20px;" value="1" <?php checked( 1, get_option( 'home_shfpt' ) ); ?>/>Show on blog home page?</p>
<p><input type="checkbox" id="frontpage_shfpt" name="frontpage_shfpt" style="margin-left:20px;" value="1" <?php checked( 1, get_option( 'frontpage_shfpt' ) ); ?>/>Show on front page?</p>
<p><input type="checkbox" id="archive_shfpt" name="archive_shfpt" style="margin-left:20px;" value="1" <?php checked( 1, get_option( 'archive_shfpt' ) ); ?>/>Show on archive pages?</p>
<p><input type="checkbox" id="category_shfpt" name="category_shfpt" style="margin-left:20px;" value="1" <?php checked( 1, get_option( 'category_shfpt' ) ); ?>/>Show on category pages?</p>
<p><input type="checkbox" id="post_shfpt" name="post_shfpt" style="margin-left:20px;" value="1" <?php checked( 1, get_option( 'post_shfpt' ) ); ?>/>Show on single page|post?</p>
 <?php submit_button('Save') ?>

        </form>
<?php }
      else {
      echo "You don't have enough permission";
      }
}
//Delete meta keys when you want to remove plugin
function shfpt_SmallHeadingPostsTitle(){
global $wpdb;
$table = $wpdb->prefix.'postmeta';
 $wpdb->delete ($table, array('meta_key' => '_slug_value_key'));
 $wpdb->delete ($table, array('meta_key' => '_position_value_key'));
}

 function shfpt_SmallHeadingPostsTitleClass() {
    new shfpt_SmallHeadingPostsTitle();
}

if ( is_admin() ) {
    add_action( 'load-post.php',     'shfpt_SmallHeadingPostsTitleClass' );
    add_action( 'load-post-new.php', 'shfpt_SmallHeadingPostsTitleClass' );
}

/**
 * The Class.
 */
class shfpt_SmallHeadingPostsTitle {

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post',      array( $this, 'save'         ) );
    }

    /**
     * Adds the meta box container.
     */
 public function add_meta_box( $post_type ) {
        // Limit meta box to certain post types.
        $post_types = array( 'post', 'page' );
  if ( in_array( $post_type, $post_types ) )
  add_meta_box( 'slug_meta_box_name',__('Small Heading For Post Title','small-heading-for-post-title'),array( $this,'render_meta_box_content' ),$post_type,'advanced','high' );
    }
  /**
     * Save the meta when the post is saved.
     */
    public function save( $post_id ) {

        // Check if our nonce is set.
        if ( ! isset( $_POST['shfpt_box_nonce'] ) ) {
            return $post_id;
        }
        $nonce = $_POST['shfpt_box_nonce'];

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce, 'shfpt_custom_box' ) ) {
            return $post_id;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        if(wp_is_post_revision($post_id)){
        return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // Sanitize the user input.
        $slug = sanitize_text_field( $_POST['shfpt_slug_field'] );
        $position=intval(sanitize_text_field( $_POST['shfpt_position'] ));
        // Update the meta field.
        update_post_meta( $post_id, '_slug_value_key', $slug );
        update_post_meta( $post_id, '_position_value_key', $position );
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content( $post ) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'shfpt_custom_box', 'shfpt_box_nonce' );

        // Use get_post_meta to retrieve an existing value from the database.
        $value = get_post_meta( $post->ID, '_slug_value_key', true );
        $position = get_post_meta( $post->ID, '_position_value_key', true );
        // Display the form, using the current value.
        ?>
        <label for="shfpt_new_field">
            <?php _e( 'Small Heading For Post Title', 'small-heading-for-post-title' ); ?>
        </label>
        <p><input type="text" id="shfpt_slug_field" name="shfpt_slug_field" value="<?php echo esc_attr( $value ); ?>" size="30" /></p>
        <label><?php _e( 'Position', 'small-heading-for-post-title' ); ?></label>
        <p><input type="radio" id="shfpt_position" style="margin-left:20px;" name="shfpt_position" value="0" <?php checked( 0,$position ); ?>/>Before Title
 <input type="radio" id="shfpt_position" name="shfpt_position" style="margin-left:20px;" value="1" <?php checked( 1, $position ); ?>/>After Title</p>
        <?php
    }
}

function shfpt_add_slug($title){
$style=get_option( 'style_shfpt');
$checkpost=get_option( 'post_shfpt');
$checkhome=get_option( 'home_shfpt');
$checkfront=get_option( 'frontpage_shfpt');
$checkarchive=get_option( 'archive_shfpt');
$checkcats=get_option( 'category_shfpt');
 $id= get_the_ID();
 $slug=get_post_meta($id,'_slug_value_key',true);
 $position=get_post_meta($id,'_position_value_key',true);
 $show=0;
if($checkpost==1)
{
  if((is_single() || is_page()) && in_the_loop()&& !empty($slug))
    $show=1;
}
if($checkhome==1)
{
  if(is_home() && in_the_loop()&& !empty($slug))
    $show=1;
}
if($checkfront==1)
{
  if(is_front_page() && in_the_loop()&& !empty($slug))
    $show=1;
}
if($checkarchive==1)
{
  if((is_archive() || is_tag() || is_author()) && in_the_loop()&& !empty($slug))
    $show=1;
}
if($checkcats==1)
{
  if(is_category() && in_the_loop()&& !empty($slug))
    $show=1;
}

if($show==1)
{
    if($position==1)
   $title.='<p style="'.$style.'"><small>'.$slug.'</small></p>';
   else
   $title='<p style="'.$style.'"><small>'.$slug.'</small></p>'.$title;

}

return $title;
}
add_filter('the_title','shfpt_add_slug');