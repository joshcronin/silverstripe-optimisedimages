<?php

/**
 * OptimisedImage Class
 *
 * @author Josh Cronin
 * @version 0.1.0
 * @copyright Copyright 2017, Josh Cronin. All rights reserved.
 * @license GNU
 */
class OptimisedImage extends DataExtension {

	//File types to use lossy compression - only Kraken
	private $lossyExtensions = array("gif","jpg","jpeg");

	/**
	* onAfterUpload
	*
	* Run after each image is uploaded through the CMS
	*/
	public function onAfterUpload() {
		//Get the path to file on server
		$filePath = BASE_PATH . "/" . $this->owner->Filename;
		//If the file does not exist, exit
		if (!file_exists($filePath)) return;
		//Save the original image
		$this->saveOriginal($filePath);
		//Check service to use
		$service = Config::inst()->get('OptimisedImage', 'Use');
		//Get service config
		$config = Config::inst()->get('OptimisedImage', $service);
		//use the service
		if ($service == "TinyPNG") {
			$this->useTinyPNG($filePath, $config);
		} else {
			$this->useKraken($filePath, $config);
		}
	}

	/**
	* saveOriginal
	*
	* Saves the originally uploaded image to different location.
	*
	* @param string $filePath This is a single path name to the source image and is where the output image will be saved
	*/
	private function saveOriginal($filePath) {
		//Don't copy if disabled in config
		if (!Config::inst()->get('OptimisedImage', 'SaveOriginal')) return false;
		//Get dir to save to
		$originalDir = Config::inst()->get('OptimisedImage', 'OriginalDir');
		//Create the destination path
		$destPath = __DIR__ . "/../../" . $originalDir . $this->owner->Filename;
		//Get the path info
		$dest = pathinfo($destPath);
		//Check directory exists
		if (!file_exists($dest['dirname'])) {
			//If not, create it
			mkdir($dest['dirname'], 0777, true);
		}
		//Copy image
		copy($filePath, $destPath);
	}

	/**
	* useTinyPNG
	*
	* Runs when TintPNG is the selected image optimisation service.  It will replace
	* the source image with the optimised one
	*
	* @param string $filePath This is a single path name to the source image and is where the output image will be saved
	* @param array $config This is an array containing the API key
	*/
	private function useTinyPNG($filePath, $config) {
		//Get API key
		$api_key = $config['api_key'];
		//If API key is still default
		if ($api_key === "undefined") return;
		//Set API Key
		$tinify = new TinyPNG($api_key);
		//Optimise image
		$tinify->compress($filePath, $filePath);
	}

	/**
	* useKraken
	*
	* Runs when Kraken is the selected image optimisation service.  It will replace
	* the source image with the optimised one
	*
	* @param string $filePath This is a single path name to the source image and is where the output image will be saved
	* @param array $config This is an array containing the API key and secret
	*/
	private function useKraken($filePath, $config) {
		//Get API key and secret
		$api_key = $config['api_key'];
		$api_secret = $config['api_secret'];
		//If either are still default
		if ($api_key === "undefined" || $api_secret === "undefined") return;
		//Create Kraken object
		$kraken = new Kraken($api_key, $api_secret);
		//Create the parameters
		$params = array(
			"file" => $filePath,
			"lossy" => in_array(strtolower($this->owner->getExtension()), $this->lossyExtensions),
			"wait" => true
		);
		//Upload the file to Kraken
		$data = $kraken->upload($params);
		//If error, exit
		if (!$data["success"]) return;
		//Get the optimised image
		$optimisedImage = file_get_contents($data["kraked_url"]);
		//Write the optimised image to file system
		$fp = fopen($filePath, "w");
		fwrite($fp, $optimisedImage);
		fclose($fp);
	}

}
