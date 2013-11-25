<?php
/*
Plugin Name: CakeMail Subscription Widget
Plugin URI: hppt://www.cakemail.com 
Description: CakeMail is an easy-to-use email marketing application that lets you build & send professional-looking email campaigns in minutes and track results.
Version: 1.0
Author: CakeMail
Author URI: http://www.cakemail.com
*/

error_reporting( E_ALL );

require_once ( dirname(__FILE__) . '/inc/request.php' );
require_once ( dirname(__FILE__) . '/inc/cakeapi.php' );
#require_once ( dirname(__FILE__) . '/inc/ajaxcallbacks.php' );

/**
 * Adds Cakemail widget.
 */
class CakeMailSubscriptionForm extends WP_Widget {

    public function __construct($number = null) {
        load_plugin_textdomain( 'cakemail-subscription-widget', false, 'cakemail-subscription-widget/locale' );

        $baseId = 'cakemailsubscriptionform';
        $name = 'CakeMail';

        parent::__construct(
            $baseId, // Base ID
            $name, // Name
            array( 'description' => __('CakeMail is an easy-to-use email marketing application that lets you build & send professional-looking email campaigns in minutes and track results.', 'cakemail-subscription-widget' ), ) // Args
        );

        if($number){
            $this->id = $baseId.'-'.$number;
            $this->number = $number;
        }

        // Register style content
        wp_register_style( 'cakemail-subscription-backend', plugins_url('cakemail-subscription-form/css/cakemail_subscription_backend.css') );
        wp_register_style( 'cakemail-subscription-frontend', plugins_url('cakemail-subscription-form/css/cakemail_subscription_frontend.css') );

        // Register script content
        wp_register_script( 'cakemail-base', plugins_url('cakemail-subscription-form/js/cakemail_base.js') );
        wp_register_script( 'cakemail-subscription-backend', plugins_url('cakemail-subscription-form/js/cakemail_subscription_backend.js') );
        wp_register_script( 'cakemail-subscription-frontend', plugins_url('cakemail-subscription-form/js/cakemail_subscription_frontend.js') );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        if( !isset($instance['user']) )
            return FALSE;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('cakemail-base');
        wp_enqueue_script('cakemail-subscription-frontend');

        wp_enqueue_style( 'cakemail-subscription-frontend' );

        include dirname(__FILE__) . '/views/cakemail_subscription_frontend.php';
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance )
    {
        $instance['username']            = isset( $instance[ 'username' ] ) ? $instance[ 'username' ] : '';
        $instance['title']               = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __('Newsletter','cakemail-subscription-widget');
        $instance['description']         = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : __('Subscribe to our newsletter!','cakemail-subscription-widget');
        $instance['confirmationmessage'] = isset( $instance[ 'confirmationmessage' ] ) ? $instance[ 'confirmationmessage' ] : __('We just sent you an email that you need to click on before you get added to the list','cakemail-subscription-widget');
        $instance['submit_txt']          = isset( $instance[ 'submit_txt' ] ) ? $instance[ 'submit_txt' ] : __('Subscribe','cakemail-subscription-widget');
        $instance['registered']          = isset( $instance[ 'registered' ] ) ? $instance[ 'registered' ] : false;
        $instance['is_lists_open']       = isset( $instance[ 'is_lists_open' ] ) ? $instance[ 'is_lists_open' ] : 0;
        $instance['is_settings_open']    = isset( $instance[ 'is_settings_open' ] ) && $instance[ 'is_settings_open' ] != '' ? $instance[ 'is_settings_open' ] : 1;

        $instance['widget_id'] = $this->id;

        if( $instance['registered'] ) {
            $instance['lists'] = $this->getLists( $instance['user']->user_key );

            if(!isset($instance['selected_list']) || $instance['selected_list'] == null)
                $instance['selected_list'] = reset($instance['lists']);
        }

        wp_enqueue_script('cakemail-base');
        wp_enqueue_script('cakemail-subscription-backend');

        wp_enqueue_style( 'cakemail-subscription-backend' );
 
        include dirname(__FILE__) . '/views/cakemail_subscription_backend.php';

        return $instance;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = $new_instance;

        $instance['error_code'] = 0;

        // Get User information
        if( $instance['username'] != '' && ( isset($instance['password']) && $instance['password'] != '' ) ) {
            $instance['user'] = $this->getUser( $instance );
            if( isset($instance['user']) && $instance['user'] ) {
                $instance['registered'] = true;
            }
            $instance['client'] = $this->getClient($instance['user']->user_key);
        }
        else {
	    $instance['user'] = $old_instance['user'];
            $instance['client'] = $old_instance['client'];
            $instance['registered'] = $old_instance['registered'];  
        }

        // Get Lists
        if( isset( $instance['user'] ) ) {
            $instance['lists'] = $this->getLists( $instance['user']->user_key );

            if($instance['opt-lists'])
                $instance['selected_list'] = $instance['lists'][$instance['opt-lists']];
            else
                $instance['selected_list'] = reset($instance['lists']);

            foreach ($instance['lists'][$instance['opt-lists']]->fields as $name => $values) {
                $field = $instance['lists'][$instance['opt-lists']]->fields[$name];

                $field['label'] = $instance['field-'.$name];
                $field['show']  = $instance['field-chk-'.$name] == on ? true : $name == 'email' ? true : false;
                $field['index'] = $instance['field-chk-index-'.$name]; 

               $instance['lists'][$instance['opt-lists']]->fields[$name] = $field;
            }
            uasort($instance['lists'][$instance['opt-lists']]->fields, "self::sortByFieldOrder");
        }
        else
            $instance['selected_list'] = reset($instance['lists']);

        update_option('cakemail_subscription_widget_'.$this->id.'_lists', $instance['lists']);

        $instance['widget_id'] = $this->id;

        unset( $instance['password'] );

        if ($instance['error_code'] != 0)
        {
            unset( $instance['user'] );
            unset( $instance['lists'] );
            unset( $instance['selected_list'] );
        }

        return $instance;
    }

    /**
     * 
     */
    public function getHTMLFields( $instance )
    {
        ob_start();
        include dirname(__FILE__) . "/views/cakemail_subscription_backend_fields.php";
        return ob_get_clean();
    }

    /**
     * Retrieve user information.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getUser( &$instance )
    {
        if(!$instance['registered']) {
            unset( $instance['user'] );
            $user = CakeAPI::getLogin($instance['username'], $instance['password']);
        }
        $user = CakeAPI::getUser($user->user_key, $user->id);
        $user->gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user->email)));

        return $user;
    }

    /**
     * Retrieve company information.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getClient( $user_key )
    {
        unset( $instance['user'] );
        return CakeAPI::getClient($user_key);
    }

    /**
     * Retrieve user's lists.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getLists( $user_key )
    {
        $lists = array();
        $obj = CakeAPI::getLists($user_key);

        $saved_lists = get_option('cakemail_subscription_widget_'.$this->id.'_lists', array());

        foreach ($obj->lists as $id => $list) {
            $lists[$list->id] = $list;
            $lists[$list->id]->fields = (array)$this->getFields($user_key, $list->id);
            foreach ($lists[$list->id]->fields as $name => $values) {
                $exists = isset($saved_lists[$list->id]) && isset($saved_lists[$list->id]->fields[$name]) ? $saved_lists[$list->id]->fields[$name] : false;
                $lists[$list->id]->fields[$name] = array(
                    'type' =>  $lists[$list->id]->fields[$name],
                    'show' =>  $exists ? $exists['show']  : 0,
                    'label' => $exists ? $exists['label'] : '',
                    'index' => $exists ? $exists['index'] : 9999,
                ); 
            }
        }
        update_option('cakemail_subscription_widget_'.$this->id.'_lists', $lists);

        return $lists;
    }

    /**
     * Retrieve fields list.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getFields( $user_key, $list_id )
    {
        return CakeAPI::getFields($user_key, $list_id);
    }

    /**
     *
     */
    public static function sortByFieldOrder( $el1, $el2 )
    {
        return $el1['index'] - $el2['index'];
    }

} // class Cakemail

add_action('widgets_init',
     create_function('', 'return register_widget("CakeMailSubscriptionForm");')
);

add_action( 'wp_ajax_get_fields', function(){
    $widget = new CakeMailSubscriptionForm($_POST['widget_index']);

    $instance = $widget->get_settings();
    $instance = $instance[$_POST['widget_index']];

    $instance['selected_list'] = $instance['lists'][$_POST['list_id']];

    die($widget->getHTMLFields($instance));
});

?>
