<?php

class GoCardless_Subscription {
	
	public static function find($id) {
		$endpoint = 'subscriptions/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	public function cancel($id) {
		$endpoint = 'subscriptions/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>