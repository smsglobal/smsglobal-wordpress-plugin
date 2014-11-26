<?php
/*
* This file is part of SMSGlobal/VersionManager package.
*
* (c) SMSGlobal <www.smsglobal.com>
* Sahil Saggar <sahil.saggar@smsglobal.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace SMSGlobal\VersionManager;

/**
 * @author Sahil Saggar <sahil.saggar@smsglobal.com>
 */

class VersionManager
{
	private static $objVM = null;
	private $versions = array();
	private $versionsJson = '';
	private $pluginsVersions = array();

	public static function getInstance()
	{
		if(self::$objVM == null) {
			self::$objVM = new self();
		}

		return self::$objVM;
	}

	public function setPluginVersion($plugin, $version)
	{
		$this->pluginsVersions[$plugin] = $version;
	}

	public function isAvailable($package)
	{
		$supportedVersion = $this->versions[$package];
		if(version_compare($this->pluginsVersions[$package], $supportedVersion['min'], '>=') && version_compare($this->pluginsVersions[$package], $supportedVersion['max'], '<=')) {
			return true;
		}
		return false;
	}

	public function getSupportedVersions($package)
	{
		return $this->versions[$package];
	}

	protected function __construct()
	{
		$this->readVersionsFile();
		$this->parseJson();
	}

	private function parseJson()
	{
		$this->versions = json_decode(trim($this->versionsJson), true);
		if($this->versions === null) {
			throw new \Exception('Could not parse JSON string. Please verify the JSON is in correct format.');
		}
	}

	private function readVersionsFile()
	{
		$this->versionsJson = file_get_contents(__DIR__ . '/../versions.json');
		if($this->versionsJson === false) {
			throw new \Exception('versions.json not found. Place it in same folder as Version Manager package');
		}
	}

}
