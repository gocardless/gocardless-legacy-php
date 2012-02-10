<?php

abstract class Resource {
	
	public function __construct() {
		//echo parent::$endpoint;
	}
	
	function new_with_client($client_obj, $attrs) {
		//return self::new($attrs).tap { |obj| obj.client = client }
	}
	
	
	function find_with_client($client_obj, $id) {
		
		global $client;
		
		// path = endpoint.gsub(':id', id.to_s)
        // data = client_obj.api_get(path)
        // obj = self.new(data)
        // obj.client = client_obj
        // obj
		
		//return self::$endpoint;
		
		$path = GoCardless_Merchant::$endpoint;
		$path = substr($path, 0, -3).$id;
		
		return Utils::fetch_resource($path);
		
	}
	
	function find($id) {
		
		global $client;
		
		if (is_object($client)) {
			$a = self::find_with_client($client, $id);
		} else {
			throw new Exception("Merchant details not found, set GoCardless.account_details");
		}
		
		var_dump($a);
		
		echo '<p>Returning details about ' . $id . '</p>';
		
		//var_dump(get_class_methods('Resource'));
		
		//echo "$id\n";
		//var_dump(GoCardless::$account_details);
		//var_dump(Client::$base_urls);
		
		//global $client;
		//
		//var_dump($client);
		//
		//if (!Client) {
		//	throw new Exception("Merchant details not found, set GoCardless.account_details");
		//}
		//
		//self::find_with_client(Client, id);
		
	}
	
}

?>