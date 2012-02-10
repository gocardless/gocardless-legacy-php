<?php

class Resource {
	
	function find_with_client($client_obj, $id, $path) {
		
		
		
		// path = endpoint.gsub(':id', id.to_s)
        // data = client_obj.api_get(path)
        // obj = self.new(data)
        // obj.client = client_obj
        // obj
		
	}
	
	function find($id, $path) {
		Resource::find_with_client($client_obj = Client, $id, $path);
	}
	
}

?>