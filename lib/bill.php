<?php

class GoCardless_Bill extends Resource {
	
	public function get($id) {
		$endpoint = 'bills/' . $id;
		return $this->gocardless->fetch_resource($endpoint);
	}
	
	public function cancel($id) {
		$endpoint = 'bills/' . $id . '/cancel';
		return $this->gocardless->fetch_resource($endpoint);
	}
	
}

?>