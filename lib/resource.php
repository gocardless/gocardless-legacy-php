<?php

class Resource {
	
	function find_with_client($client_obj, $id) {
		
		Subscription.find_with_client($client_obj, $id);
		
	}
	
}

?>