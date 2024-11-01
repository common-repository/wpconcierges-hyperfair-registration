<?php

/**a
 * Provide a view for a section
 *
 * Enter text below to appear below the section title on the Settings page
 *
 * @link       https://www.wpconcierges.com/plugins/order_postback_woo/
 * @since      1.0.0
 *
 * @package    order_postback_woo
 * @subpackage order_postback_woo/admin/partials
 */

?>
 <div class="hyperfair-registration-note">
                            <h3><?php 
            echo  esc_html__( 'Instructions', 'hyperfair-registration' ) ;
            ?></h3>
                            <p><?php 
            echo  sprintf( wp_kses( __( 'Fill in the Place, Secret, and choose the registration system', 'hyperfair-registration' ), array(
                'a' => array(
                'href'   => array(),
                'target' => array(),
            ),
            ) ), esc_url( 'https://www.wpconcierges.com/plugins/hyperfair-registration' ) ) ;
            ?></p>
           
                        </div>