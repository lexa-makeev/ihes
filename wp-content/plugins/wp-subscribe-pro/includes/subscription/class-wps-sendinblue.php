<?php
/**
 * Sendinblue Subscription
 */

class WPS_Subscription_Sendinblue extends WPS_Subscription_Base {

	public function init( $api_key ) {

		require_once 'libs/sendinblue.php';
		return new SendinBlue( $api_key );
	}

	public function get_lists( $api_key ) {

		$api = $this->init( $api_key );
		$result = $api->get_lists(array());

		if( isset( $result['code'] ) && 'failure' == $result['code'] ) {
			throw new Exception( $result['message'] );
		}

		$lists = array();
		foreach( $result['data'] as $list ) {
			$lists[ $list['id'] ] = $list['name'];
		}

		return $lists;
	}

    public function subscribe( $identity, $options ) {

		$email = $identity['email'];
		$listId = $options['list_id'];

		// get user
		$api = $this->init( $options['api_key'] );
		$result = $api->get_user( array( 'email' => $email ) );

		// user exists already
		$lists = array();
		if( isset( $result['code'] ) && 'success' == $result['code'] ) {

			if( !empty( $result['data']['listid'] ) ) {
				$lists = $result['data']['listid'];
			}

            if ( !in_array( $listId, $lists) ) {
				$lists[] = $listId;
			}
		}
		// user doesn't exist yet
		else {
			$lists[] = $listId;
		}

		unset($identity['email']);

        $result = $api->create_update_user(array(
            'email' => $email,
            'attributes' => array(
				'FIRSTNAME' => $identity['name'],
			),
            'listid' => $lists
        ));

		if( isset( $result['code'] ) && 'success' != $result['code'] ) {
			throw new Exception( $result['message'] );
		}

        return array('status' => 'subscribed');
	}

	public function get_fields() {

		$fields = array(

			'sendinblue_api_key' => array(
				'id'    => 'sendinblue_api_key',
				'name'  => 'sendinblue_api_key',
				'type'  => 'text',
				'title' => esc_html__( 'SendinBlue API Key', 'wp-subscribe' ),
				'desc'  => esc_html__( 'The API Key (version 2.0) of your Sendinblue account.', 'wp-subscribe' ),
				'link'  => 'https://my.sendinblue.com/advanced/apikey'
			),

			'sendinblue_list_id' => array(
				'id'    => 'sendinblue_list_id',
				'name'  => 'sendinblue_list_id',
				'type'  => 'select',
				'title' => esc_html__( 'SendinBlue List', 'wp-subscribe' ),
				'options' => array( 'none' => esc_html__( 'Select List', 'wp-subscribe' ) ) + wps_get_service_list('sendinblue'),
				'is_list'  => true
			)
		);

		return $fields;
	}
}
