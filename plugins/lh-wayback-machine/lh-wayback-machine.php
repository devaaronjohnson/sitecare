<?php
/**
 * Plugin Name: LH Wayback Machine
 * Plugin URI: https://lhero.org/portfolio/lh-wayback-machine/
 * Description: Automatically add content to the internet archive
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com
 * Text Domain: lh_wayback_machine
 * Domain Path: /languages
 * Version: 1.03
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists('LH_wayback_machine_plugin')) {


class LH_wayback_machine_plugin {
    
private static $instance;

static function return_plugin_namespace(){

return 'lh_wayback_machine';

}

// Wayback Machine API endpoints.

static function return_wayback_machine_url_save(){

return 'https://web.archive.org/save/';    
    
}

static function return_wayback_machine_url_fetch_archives(){

return 'https://web.archive.org/cdx/';    
    
}

static function return_wayback_machine_url_view(){

return 'https://web.archive.org/web/';    
    
}

static function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(plugin_basename( __FILE__ ).' - '.print_r($log, true));
            } else {
                error_log(plugin_basename( __FILE__ ).' - '.$log);
            }
        }
    }
    
static function queue_single(){

if( ! wp_next_scheduled( 'lh_wayback_machine_single' ) ) {

//schedule event to be fired right away
wp_schedule_single_event( time(), 'lh_wayback_machine_single' );

}   
    
}

static function get_waybackable_post_statuses(){
    
$statuses = array('publish');

return array_unique(apply_filters( 'lh_wayback_machine_post_status_filter', $statuses ));

}

static function get_waybackable_taxonomies(){
    
$args = array('public' => true);

$taxonomies = get_taxonomies($args); 

return apply_filters( 'lh_wayback_machine_taxonomy_filter', $taxonomies );

}


    /**
	 * Trigger a URL to be archived on the Wayback Machine.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url The URL to archive.
	 *
	 * @return string The link to the newly created archive, if it exists.
	 */
static function trigger_url_snapshot( $url ) {

		// Ping archive machine.
		$wayback_machine_save_url = self::return_wayback_machine_url_save() . $url;
		$response = wp_remote_get( $wayback_machine_save_url );

		$archive_link = '';

		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( ! empty( $response['headers']['x-archive-wayback-runtime-error'] ) ) {
			return new WP_Error( 'wayback_machine_error', $response['headers']['x-archive-wayback-runtime-error'], $response );
		} elseif ( ! empty( $response['headers']['content-location'] ) ) {
			return $response['headers']['content-location'];
		}

	}





public function posts_query(){
    
$types = get_post_types( array('public'   => true ), 'names' );


$args = array(
    'post_type' => $types,
    'post_status' => self::get_waybackable_post_statuses(),
    'posts_per_page' => '1',
'meta_query' => array(
    array(
     'key' => '_lh_wayback_machine-timestamp',
     'compare' => 'NOT EXISTS' // this should work...
    ),
),
);
// The Query
$custom_query = new WP_Query( $args );



if ( $custom_query->have_posts() ) {

$posts = $custom_query->get_posts();


foreach($posts as $post) {

$url = get_permalink( $post->ID );

$snapshot = self::trigger_url_snapshot( $url );

$timestamp = current_time('mysql');

// Save the time this was pinged to the internet archive
update_post_meta( $post->ID, '_lh_wayback_machine-timestamp', $timestamp );



}

self::queue_single();

return true;

} else {

return false;

}


}

public function terms_query(){

$taxonomies = self::get_waybackable_taxonomies();


if  (isset($taxonomies)) {

$args = array(
    'taxonomy' => $taxonomies,
    'number' => '1',
'meta_query' => array(
    array(
     'key' => '_lh_wayback_machine-timestamp',
     'compare' => 'NOT EXISTS' // this should work...
    ),
),
);
// The Query
$term_query = new WP_Term_Query( $args );

print_r($term_query);


  if( empty( $term_query->terms ) ){
        return false;
    } else {
        
        
 foreach ( $term_query->terms as $term ) {
     
     	$url = get_term_link( $term->term_id, $term->taxonomy);
		$snapshot = self::trigger_url_snapshot( $url );
		
		$timestamp = current_time('mysql');

// Save the time this was pinged to the internet archive
update_term_meta( $term->term_id, '_lh_wayback_machine-timestamp', $timestamp );

// Save the result of the snapshot to term meta
update_term_meta( $term->term_id, '_lh_wayback_machine-last_result', $snapshot );
     
     
     
 }
 
self::queue_single();
        
   return true;     
        
    }



}


}

public function users_query(){
    
    $args = array( 'who' => 'authors',
     'number' => '1',
'meta_query' => array(
    array(
     'key' => '_lh_wayback_machine-timestamp',
     'compare' => 'NOT EXISTS' // this should work...
    )
    ));
    
   $user_query = new WP_User_Query($args); 
    
}


	/**
	 * Queue a post snapshot.
	 *
	 *
	 * @param int $post_id ID of the post to archive.
	 */
	public function trigger_post_snapshot( $post_id ) {

// Don't do anything if the post does not have a waybackable status.
$post_status = get_post_status( $post_id );

if ( !in_array($post_status, self::get_waybackable_post_statuses()) || wp_is_post_revision( $post_id ) ) {
			return;
		} else {
//remove the _lh_wayback_machine-timestamp post meta key, this will get it picked up in the query used by the queue
delete_post_meta($post_id, '_lh_wayback_machine-timestamp');

}



	}

/**
	 * Trigger a taxonomy term snapshot.
	 *
	 *
	 * @param int $term_id  ID of the taxonomy term to archive.
	 * @param int $taxonomy Taxonomy to which the current term belongs.
	 */
	public function trigger_term_snapshot( $term_id, $taxonomy_id, $taxonomy ) {


delete_term_meta($term_id, '_lh_wayback_machine-timestamp');


	}

	/**
	 * Trigger a user snapshot.
	 *
	 *
	 * @param int $user_id  ID of the user to archive.
	 */
	public function trigger_user_snapshot( $user_id ) {

delete_user_option($user_id, '_lh_wayback_machine-timestamp');

	}









public function run_processes(){
    
    //remove the cron job, we can put it back if we need too.
    wp_clear_scheduled_hook( 'lh_wayback_machine_single' );	

$check = $this->posts_query();

if (!empty($check)){
    

//do nothing


} else {

//running terms

$this->terms_query();

}


}

public function add_post_column($columns){
    

	
$columns['lh_wm-timestamp'] = 'Wayback Date';

return $columns;

}

// SHOW THE Wayback date and link it
public function show_post_column_content($column_name, $post_id) {
    
    
    

    if ($column_name == 'lh_wm-timestamp') {
        
        $permalink = get_the_permalink($post_id);
        $post_status = get_post_status( $post_id );
        $timestamp = get_post_meta( $post_id, '_lh_wayback_machine-timestamp', true );
        
if (!in_array($post_status, self::get_waybackable_post_statuses()) && empty($timestamp)){

echo 'Not Applicable';   
    
}  elseif (!empty($timestamp)) {    
        
echo '<a href="'.self::return_wayback_machine_url_view().'" target="_blank">'.get_post_meta( $post_id, '_lh_wayback_machine-timestamp', true ).'</a>';

} else {
    
echo 'Queued';    
    
}
    }
} 


public function show_term_column_content( $content, $column_name, $term_id ){
     if ($column_name == 'lh_wm-timestamp') {
        
        $permalink = get_term_link($term_id);
        $timestamp = get_term_meta( $term_id, '_lh_wayback_machine-timestamp', true );
        
        if (!empty($timestamp)){
        
        $content = '<a href="'.self::return_wayback_machine_url_view().$permalink.'" target="_blank">'.$timestamp.'</a>';
        
        } else {
            
            $content = 'Queueed';
            
        }
         
    }
	return $content;
}


public function hide_columns_by_default( $hidden, $screen ) {
    
    
$hidden[] = 'lh_wm-timestamp';


return $hidden;
    
}



public function admin_init(){
    
    
$args = array(
   'public' => true,
);

$post_types = get_post_types( $args, 'names', 'and'); 

foreach ( $post_types  as $post_type ) {

add_filter('manage_'.$post_type.'_posts_columns', array($this,'add_post_column'));




}


add_action('manage_posts_custom_column', array($this,'show_post_column_content'), 10, 2);

add_action('manage_pages_custom_column', array($this,'show_post_column_content'), 10, 2);


$taxonomies = self::get_waybackable_taxonomies();

foreach ( $taxonomies as $taxonomy ) {
    
add_filter('manage_edit-'.$taxonomy.'_columns', array($this,'add_post_column'),10,1);

add_action('manage_'.$taxonomy.'_custom_column', array($this,'show_term_column_content'), 10, 3);    
    
    
}


add_filter( 'default_hidden_columns', array($this,'hide_columns_by_default'), 10, 2 );  

    
    
    
}

public function plugin_init(){
    
// Set up automated archive trigger actions.
add_action( 'save_post',      array( $this, 'trigger_post_snapshot' ) );
add_action( 'edited_term',    array( $this, 'trigger_term_snapshot' ), 10, 3 );
add_action( 'profile_update', array( $this, 'trigger_user_snapshot' ), 10, 3 );


//add columns listing wayback ping date
add_action( 'admin_init', array($this,'admin_init'));

//to attach processes to the ongoing cron job
add_action( 'lh_wayback_machine_process', array($this,'run_processes'));

//add processing to the single cron job
add_action( 'lh_wayback_machine_single', array($this,'run_processes'));
    
  
    
}

 /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
    


   

static function on_activate($network_wide) {
    
//self::write_log("started activate");


    if ( is_multisite() && $network_wide ) { 

    $args = array('number' => 500, 'fields' => 'ids');
        
    $sites = get_sites($args);
    
            foreach ($sites as $blog_id) {
            switch_to_blog($blog_id);

wp_clear_scheduled_hook( 'lh_wayback_machine_process' );

if (! wp_next_scheduled( 'lh_wayback_machine_process' )) {
wp_schedule_event( time(), 'hourly', 'lh_wayback_machine_process' );
}

            restore_current_blog();
        } 

    } else {


wp_clear_scheduled_hook( 'lh_wayback_machine_process' );

if (! wp_next_scheduled( 'lh_wayback_machine_process' )) {
wp_schedule_event( time(), 'hourly', 'lh_wayback_machine_process' );
}


}


}

static function on_deactivate($network_wide) {
    
//self::write_log("started deactivate");

    if ( is_multisite() && $network_wide ) { 

    $args = array('number' => 500, 'fields' => 'ids');
        
    $sites = get_sites($args);
    
            foreach ($sites as $blog_id) {
            switch_to_blog($blog_id);

wp_clear_scheduled_hook( 'lh_wayback_machine_process' );



            restore_current_blog();
        } 

    } else {


wp_clear_scheduled_hook( 'lh_wayback_machine_process' );

}
    
    
}



public function __construct() {
    
	 //run our hooks on plugins loaded to as we may need checks       
    add_action( 'plugins_loaded', array($this,'plugin_init'));

}

}

$lh_wayback_machine_instance = LH_wayback_machine_plugin::get_instance();

register_activation_hook(__FILE__, array('LH_wayback_machine_plugin','on_activate') );
register_deactivation_hook( __FILE__, array( 'LH_wayback_machine_plugin', 'on_deactivate' ) );

}



?>