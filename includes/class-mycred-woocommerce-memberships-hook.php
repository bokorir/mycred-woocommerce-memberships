<?php

/**
 * myCRED hook class
 * Hooks into the WooCommerce Memberships plugin purchases
 *
 * @since      1.0.0
 * @package    myCRED_WooCommerce_Memberships
 * @subpackage myCRED_WooCommerce_Memberships/includes
 * @author     Robert Bokori <robert@smarter.uk.com>
 */
if ( class_exists( 'myCRED_Hook' ) ) {

  class myCRED_WooCommerce_Memberships_Hook extends myCRED_Hook {

    private $textdomain;
    private $membership_plans;

    /**
     * Construct
     *
     * @param array $hook_prefs configured preferences
     * @param string point type
     */
    function __construct( $hook_prefs, $type = 'mycred_default' ) {

      if ( ! function_exists( 'wc_memberships_get_membership_plans' ) ) {
        return;
      }

      $defaults = array();

      $this->membership_plans = wc_memberships_get_membership_plans();

      $this->textdomain = 'mycred-woocommerce-memberships';

      $defaults['any'] = array(
        'creds' => 1,
        'log'   => '%plural% for any membership plan',
        'limit' => '0/x'
      );

      if ( ! empty( $this->membership_plans ) ) {

        foreach ( $this->membership_plans as $membership_plan ) {
          $defaults[ $this->sanitize_slug( $membership_plan->get_slug() ) ] = array(
            'creds' => 1,
            'log'   => '%plural% for membership plan ' . $membership_plan->get_name(),
            'limit' => '0/x'
          );
        }

      }

      if ( isset( $hook_prefs['mycred_woocommerce_memberships'] ) ) {
        $defaults = $hook_prefs['mycred_woocommerce_memberships'];
      }

      parent::__construct( array(
        'id'       => 'mycred_woocommerce_memberships',
        'defaults' => $defaults
      ), $hook_prefs, $type );

    }

    /**
     * Hook into WooCommerce Memberships. Called when executing myCRED hooks
     */
    public function run() {

      add_action( 'wc_memberships_grant_membership_access_from_purchase', array( $this, 'membership_earned' ), 99, 2 );

    }

    /**
     * Callback when a membership earned
     *
     * @param WC_Memberships_Membership_Plan $membership_plan membership plan
     * @param array $args
     */
    public function membership_earned( $membership_plan, $args ) {

      $prefs = $this->prefs;

      $membership_plan_slug = $this->sanitize_slug( $membership_plan->get_slug() );

      $user_id = $args['user_id'];

      // Check if user is excluded (required)
      if ( $this->core->exclude_user( $user_id ) === true ) {
        return;
      }

      // Award points for any membership plan
      // Make sure we award points other then zero
      if ( ! empty( $this->prefs['any']['creds'] ) && ! $this->prefs['any']['creds'] == 0 ) {

        // Check limit
        if ( ! $this->over_hook_limit( 'any', 'mycred_woocommerce_memberships', $user_id ) ) {

          $this->core->add_creds(
            'mycred_woocommerce_memberships',
            $user_id,
            $prefs['any']['creds'],
            $prefs['any']['log'],
            $membership_plan->get_id(),
            array( 'ref_type' => 'wc_membership_plan' ),
            $this->mycred_type
          );

        }

      }

      // Award points for specific membership plan
      // Make sure we award points other then zero
      if ( ! empty( $this->prefs[ $membership_plan_slug ]['creds'] ) && ! $this->prefs[ $membership_plan_slug ]['creds'] == 0 ) {

        // Check limit
        if ( ! $this->over_hook_limit( $membership_plan_slug, 'mycred_woocommerce_memberships_plan_' . $membership_plan_slug, $user_id ) ) {

          $this->core->add_creds(
            'mycred_woocommerce_memberships_plan_' . $membership_plan_slug,
            $user_id,
            $prefs[ $membership_plan_slug ]['creds'],
            $prefs[ $membership_plan_slug ]['log'],
            $membership_plan->get_id(),
            array( 'ref_type' => 'wc_membership_plan' ),
            $this->mycred_type
          );

        }

      }

    }

    /**
     * Change hyphens to underscores
     *
     * @param string $slug membership plan slug
     *
     * @return string $slug formatted membership plan slug
     */
    private function sanitize_slug( $slug ) {

      return str_replace( '-', '_', $slug );

    }

    /**
     * Prints preferences fields to config hook options
     */
    public function preferences() {

      $prefs = $this->prefs; ?>

      <label class="subheader">
        <?php echo $this->core->template_tags_general( __( '%plural% for any plan', $this->textdomain ) ); ?>
      </label>
      <ol>
        <li>
          <div class="h2">
            <input type="text"
                   name="<?php echo $this->field_name( array( 'any' => 'creds' ) ); ?>"
                   id="<?php echo $this->field_id( array( 'any' => 'creds' ) ); ?>"
                   value="<?php echo $this->core->number( $prefs['any']['creds'] ); ?>"
                   size="8"/>
          </div>
        </li>

        <li>
          <label for="<?php echo $this->field_id( array( 'any' => 'limit' ) ); ?>">
            <?php _e( 'Limit', $this->textdomain ); ?>
          </label>
          <?php echo $this->hook_limit_setting( $this->field_name( array( 'any' => 'limit' ) ), $this->field_id( array( 'any' => 'limit' ) ), $prefs['any']['limit'] ); ?>
        </li>
      </ol>

      <label class="subheader">
        <?php _e( 'Log template', $this->textdomain ); ?>
      </label>
      <ol>
        <li>
          <div class="h2">
            <input type="text"
                   name="<?php echo $this->field_name( array( 'any' => 'log' ) ); ?>"
                   id="<?php echo $this->field_id( array( 'any' => 'log' ) ); ?>"
                   value="<?php echo esc_attr( $prefs['any']['log'] ); ?>"
                   class="long"/>
          </div>

					<span class="description">
						<?php echo $this->available_template_tags( array( 'general', 'any' ) ); ?>
					</span>
        </li>
      </ol>

      <?php foreach ( $this->membership_plans as $membership_plan ) : ?>

        <?php $membership_plan_slug = $this->sanitize_slug( $membership_plan->get_slug() ); ?>

        <label class="subheader">
          <?php echo $this->core->template_tags_general( sprintf( __( 'Points for %s plan', $this->textdomain ), $membership_plan->get_name() ) ); ?>
        </label>
        <ol>
          <li>
            <div class="h2">
              <input type="text"
                     name="<?php echo $this->field_name( array( $membership_plan_slug => 'creds' ) ); ?>"
                     id="<?php echo $this->field_id( array( $membership_plan_slug => 'creds' ) ); ?>"
                     value="<?php echo $this->core->number( $prefs[ $membership_plan_slug ]['creds'] ); ?>"
                     size="8"/>
            </div>
          </li>

          <li>
            <label for="<?php echo $this->field_id( array( $membership_plan_slug => 'limit' ) ); ?>">
              <?php _e( 'Limit', $this->textdomain ); ?>
            </label>
            <?php echo $this->hook_limit_setting( $this->field_name( array( $membership_plan_slug => 'limit' ) ), $this->field_id( array( $membership_plan_slug => 'limit' ) ), $prefs[ $membership_plan_slug ]['limit'] ); ?>
          </li>
        </ol>

        <label class="subheader">
          <?php _e( 'Log template', $this->textdomain ); ?>
        </label>
        <ol>
          <li>
            <div class="h2">
              <input type="text"
                     name="<?php echo $this->field_name( array( $membership_plan_slug => 'log' ) ); ?>"
                     id="<?php echo $this->field_id( array( $membership_plan_slug => 'log' ) ); ?>"
                     value="<?php echo esc_attr( $prefs[ $membership_plan_slug ]['log'] ); ?>"
                     class="long"/>
            </div>

						<span class="description">
							<?php echo $this->available_template_tags( array( 'general', $membership_plan_slug ) ); ?>
						</span>
          </li>
        </ol>

        <?php
      endforeach;
    }

    /**
     * Sanitise Preferences
     */
    public function sanitise_preferences( $data ) {

      if ( isset( $data['any']['limit'] ) && isset( $data['any']['limit_by'] ) ) {

        $limit = sanitize_text_field( $data['any']['limit'] );

        if ( $limit == '' ) {
          $limit = 0;
        }

        $data['any']['limit'] = $limit . '/' . $data['any']['limit_by'];

        unset( $data['any']['limit_by'] );

      }

      foreach ( $this->membership_plans as $membership_plan ) {

        $membership_plan_slug = $this->sanitize_slug( $membership_plan->get_slug() );

        if ( isset( $data[ $membership_plan_slug ]['limit'] ) && isset( $data[ $membership_plan_slug ]['limit_by'] ) ) {

          $limit = sanitize_text_field( $data[ $membership_plan_slug ]['limit'] );

          if ( $limit == '' ) {
            $limit = 0;
          }

          $data[ $membership_plan_slug ]['limit'] = $limit . '/' . $data[ $membership_plan_slug ]['limit_by'];

          unset( $data[ $membership_plan_slug ]['limit_by'] );

        }

      }

      return $data;
    }

  }

}
