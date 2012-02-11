<?php

class GoCardless_Bill {
	
	public function get($id) {
		$endpoint = 'bills/' . $id;
		return Utils::fetch_resource($endpoint);
	}
	
	public function cancel($id) {
		$endpoint = 'bills/' . $id . '/cancel';
		return Utils::fetch_resource($endpoint);
	}
	
}

?>