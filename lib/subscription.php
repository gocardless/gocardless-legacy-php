<?php

class GoCardless_Subscription extends Resource {
	
	public $endpoint = '/subscriptions/:id';
	
	//public static function get($id) {
	//	$endpoint = 'subscriptions/' . $id;
	//	return $this->gocardless->fetch_resource($endpoint);
	//}
	//
	//public function cancel($id) {
	//	$endpoint = 'subscriptions/' . $id . '/cancel';
	//	return $this->gocardless->fetch_resource($endpoint);
	//}
	
}

?>