<?php echo $args['before_widget']; ?>
<?php echo $args['before_title']. $instance['title']. $args['after_title']; ?>

<div class="cakemail-box" id="cakemail-box-<?php echo $instance['selected_list']->id; ?>">
<?php //echo '<pre>'. print_r($instance,true) . '</pre>'?>
  <!-- Subscription form -->
  <div class="content cake_subscription_form" id="cake_subscription_form_<?php echo $instance['selected_list']->id; ?>">
    <div class="title"><?php echo $instance['description']; ?></div>

    <div class="error-box"></div>

    <div id="<?php echo $this->get_field_id( 'form' ); ?>">
      <?php foreach ( $instance['selected_list']->fields as $field => $values ) { ?>
        <?php $placeholder = $values['label'] ? $values['label'] : $field; ?>

        <?php if($values['show'] || $field == 'email'){ ?>
          <?php if(isset($instance['show_label']) && $instance['show_label']){ ?>
            <label for="<?php echo $this->get_field_id( $field ); ?>"><?php echo $values['label'] ? $values['label'] : $field; ?></label>
            <?php $placeholder = ''; ?>
          <?php } ?>

          <input type="text" class="<?php echo $placeholder == '' ? 'with-label' : ''; ?>" value="" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" placeholder="<?php echo $placeholder; ?>"/>
        <?php } ?>
      <?php } ?>

      <input type="submit" class="submit_cakemail_subscribe block-button" id="<?php echo $this->get_field_id( 'submit' ); ?>" id="<?php echo $this->get_field_name( 'submit' ); ?>" value="<?php echo $instance['submit_txt']; ?>" />

      <input type="hidden" id="<?php echo $this->get_field_id( 'user_key' ); ?>" name="<?php echo $this->get_field_name( 'user_key' ); ?>" value="<?php echo $instance['user']->user_key ?>" />
      <input type="hidden" id="<?php echo $this->get_field_id( 'list_id' ); ?>" name="<?php echo $this->get_field_name( 'list_id' ); ?>" value="<?php echo $instance['selected_list']->id; ?>" />
    </div>
  </div>

  <!-- Confirmation message -->
  <div class="content cake_confirmation_message" id="cake_confirmation_message_<?php echo $instance['selected_list']->id; ?>">
    <p><?php echo $instance['confirmationmessage'] ?></p>
    <!--p class="powered"><?php //echo __('Powered by','cakemail-subscription-widget') ?> <a href="http://www.cakemail.com">CakeMail</a></p-->
  </div>

</div>

<?php echo $args['after_widget']; ?>
