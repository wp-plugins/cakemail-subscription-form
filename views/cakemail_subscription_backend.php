<?php 
    $is_logged_id = $instance['registered'];
    $error_code = 0;
?>

<div class="cakemail" id="<?php echo $instance['widget_id']; ?>">
    <div class="header <?php echo $is_logged_id ? 'loggedin' : ''; ?>">
        <div class="username"><?php echo  $instance['user']->first_name . ' ' . $instance['user']->last_name; ?></div>
        <div class="company"><?php echo $instance['client']->company_name ?></div>
        <img class="logo"  src="<?php echo plugins_url('cakemail-subscription-form/img/logo_cake_wp.png'); ?>" />
    </div>

    <!-- TITLE -->
    <div class="section <?php echo $is_logged_id ? 'collapsable' : ''; ?>">
        <div class="title first">
            <div class="label"><?php echo __('Account','cakemail-subscription-widget') ?></div>
            <div class="arrow <?php echo !$is_logged_id ? 'hidden' : ''; ?>"></div>
        </div>

        <div class="form <?php echo $is_logged_id ? 'hidden' : ''; ?>">
            <label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php echo __('Email','cakemail-subscription-widget') ?></label>
            <input class="widefat<?php echo $error_code==1 ? " form_error" : "" ?>" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo $instance['username']; ?>" />

            <label for="<?php echo $this->get_field_id( 'password' ); ?>"><?php echo __('Password','cakemail-subscription-widget') ?></label>
            <input class="widefat<?php echo $error_code==1 ? " form_error" : "" ?>" id="<?php echo $this->get_field_id( 'password' ); ?>" name="<?php echo $this->get_field_name( 'password' ); ?>" type="password" value="" />

            <div class="noaccount<?php echo $is_logged_id ? " hidden" : ""; ?>">
                <p><?php echo __('Don\'t have a CakeMail account?','cakemail-subscription-widget') ?> <a target="_blank" href="http://www.cakemail.com"><?php echo __('Create an account','cakemail-subscription-widget') ?></a></p>
            </div>

        </div>
    </div>

    <!-- WIDGET SETTINGS -->
    <div class="section collapsable <?php echo !$is_logged_id ? 'hidden' : ''; ?>">
        <input type="hidden" id="<?php echo $this->get_field_id( 'is_settings_open' ); ?>" name="<?php echo $this->get_field_name( 'is_settings_open' ); ?>"  value="<?php echo $instance['is_settings_open']; ?>" />
        <div class="title<?php echo $instance['is_settings_open'] ? ' open' : ''; ?>" id="settings">
            <div class="label"><?php echo __('Widget configuration','cakemail-subscription-widget') ?></div>
            <div class="arrow"></div>
        </div>
        <div class="form <?php echo !$instance['is_settings_open'] ? 'hidden' : ''; ?>">
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo __('Widget title','cakemail-subscription-widget') ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />

            <label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php echo __('Description','cakemail-subscription-widget') ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo $instance['description']; ?></textarea>

            <label for="<?php echo $this->get_field_id( 'confirmationmessage' ); ?>"><?php echo __('Confirmation message','cakemail-subscription-widget') ?></label>
            <textarea class="widefat" id="<?php echo $this->get_field_id( 'confirmationmessage' ); ?>" name="<?php echo $this->get_field_name( 'confirmationmessage' ); ?>"><?php echo $instance['confirmationmessage']; ?></textarea>

            <label for="<?php echo $this->get_field_id( 'submit_txt' ); ?>"><?php echo __('Subscribe button text','cakemail-subscription-widget') ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'submit_txt' ); ?>" name="<?php echo $this->get_field_name( 'submit_txt' ); ?>" type="text" value="<?php echo $instance['submit_txt']; ?>" />

            <!--input class="powered_check" id="<?php //echo $this->get_field_id( 'show_powered' ); ?>" name="<?php //echo $this->get_field_name( 'show_powered' ); ?>" type="checkbox" disabled="disabled" checked="checked" />
            <label for="<?php //echo $this->get_field_id( 'show_powered' ); ?>"><?php //echo __('"Powered by CakeMail" message.','cakemail-subscription-widget') ?></label>
            <p class="powered_message"><?php //echo __('Don\'t want to show the message?','cakemail-subscription-widget') ?> <a href="http://www.cakemail.com"><?php //echo __('Upgrade your account','cakemail-subscription-widget') ?></a>!</p-->
        </div>
    </div>

    <!-- SUBSCRIBERS LISTS -->
    <div class="section collapsable <?php echo !$is_logged_id ? 'hidden' : ''; ?>">
        <input type="hidden" id="<?php echo $this->get_field_id( 'is_lists_open' ); ?>" name="<?php echo $this->get_field_name( 'is_lists_open' ); ?>" value="<?php echo $instance['is_lists_open']; ?>" />
        <div class="title<?php echo $instance['is_lists_open'] ? ' open' : ''; ?>" id="lists">
            <div class="label"><?php echo __('Subscribers lists','cakemail-subscription-widget') ?></div>
            <div class="arrow"></div>
        </div>

        <div class="form <?php echo !$instance['is_lists_open'] ? 'hidden' : ''; ?>">
            <?php if( $instance['registered'] ) { ?>
                <?php if(count($instance['lists']) > 1) { ?>
                    <select name="<?php echo $this->get_field_name( 'opt-lists' ); ?>" class="opt-list widefat">
                    <?php foreach ($instance['lists'] as $list) { ?>
                        <option <?php echo ($list->id == $instance['selected_list']->id ? " selected=\"selected\" " : "") ?> value="<?php echo $list->id ?>"><?php echo $list->name ?></option>
                    <?php } ?>
                    </select>
                <?php } else { ?>
                    <input type="hidden" name="<?php echo $this->get_field_name( 'opt-lists' ); ?>" value="<?php echo $instance['selected_list']->id ?>" />
                <?php } ?>
                <ul class="fields-container">
                    <?php echo $this->getHTMLFields($instance) ?>
                </ul>

                <input id="<?php echo $this->get_field_id( 'show_label' ); ?>" name="<?php echo $this->get_field_name( 'show_label' ); ?>" type="checkbox" <?php echo isset($instance['show_label']) && $instance['show_label'] == 'on' ? 'checked="checked"' : ''; ?>/>
                <label for="<?php echo $this->get_field_id( 'show_label' ); ?>"><?php echo __('Show fields label','cakemail-subscription-widget') ?></label>

                <input class="fields-sorted" name="<?php echo $this->get_field_name( 'fields-sorted' ); ?>" id="<?php echo $this->get_field_id( 'fields-sorted' ); ?>" type="hidden" value="0" />
            <?php } ?>
        </div>
    </div>
</div>

<script>
<?php if(strrpos($instance['widget_id'],'__i__') === false) { ?>
    jQuery( document ).ready(function() {
        new CakeMail.Subscription.Widget('<?php echo $instance['widget_id']; ?>');
    });
<?php } ?>
</script>
