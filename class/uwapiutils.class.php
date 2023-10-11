<?php
class uwApiUtils {


	public static function callAPI($endpoint, $method,  $data = false)
	{
		global $conf;

	    $apikey = $conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_KEY;
	    if(isset($_SERVER['HTTPS']))
	    	$url = 'https://' . $conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_SERVERNAME . '/api/index.php/' . $endpoint;
	    else
	    	$url = 'http://' . $conf->global->FACTURE_SYNCHRONE_SYNCHRO_API_SERVERNAME . '/api/index.php/' . $endpoint;

		// echo "<pre>callAPI : " . $url . " > " . $method. " [" . $apikey . "]</pre>";
		uwApiUtils::logfile("Distant API : " . $url . " > " . $method. " [" . $apikey . "]");

	    $curl = curl_init();
	    	    
	    $httpheader = ['DOLAPIKEY: '.$apikey];
	    switch ($method)
	    {
	        case "POST":
	            curl_setopt($curl, CURLOPT_POST, 1);
	            $httpheader[] = "Content-Type:application/json";

	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

	            break;
	        case "PUT":

		    	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
	            $httpheader[] = "Content-Type:application/json";

	            if ($data)
	                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

	            break;
	        default:
	            if ($data)
	                $url = sprintf("%s?%s", $url, http_build_query($data));
	    }

	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $httpheader);
	    curl_setopt($curl, CURLOPT_FAILONERROR, true);

	    $result = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$curl_errno = curl_errno($curl);
		
		if($result === false) {
			uwApiUtils::logfile("Distant API - CURL Error [" . $http_status . "] : " . curl_error($curl));
			return "Distant API - CURL Error: " . curl_error($curl);
		}
				
	    uwApiUtils::logfile("Distant API - resultat : " . json_encode($result));

	    curl_close($curl);

	    return  json_decode($result);
	}


	public static function logfile( $text ) {
		global $conf;
		if((int)$conf->global->FACTURE_SYNCHRONE_SYNCHRO_LOG == 1) {
			$logfile = dirname(__FILE__) . "/../logs/apilogs.txt";
			file_put_contents($logfile, date("Y-m-d H:i:s") . " - " . $text . "\n", FILE_APPEND );

		}
	}
}