<?php

/**
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\Market\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IRequest;

class PageController extends Controller {
	/** @var IConfig */
	private $config;

	public function __construct($appName, IRequest $request, IConfig $config) {
		parent::__construct($appName, $request);
		$this->config = $config;
	}

	/**
	 * @NoCSRFRequired
	 *
	 * Required for marketplace login to generate a callbackurl with
	 * hash sign, or else login token will be attached before it resulting
	 * in a broken url.
	 */
	public function indexHash() {
		return $this->index();
	}

	/**
	 * @NoCSRFRequired
	 */
	public function index() {
		$templateResponse = new TemplateResponse($this->appName, 'index', []);
		$policy = new ContentSecurityPolicy();
		// live storage
		$policy->addAllowedImageDomain('https://marketplace-storage.owncloud.com');
		// staging - for internal testing
		$policy->addAllowedImageDomain('https://marketplace-storage.staging.owncloud.services');
		// local dev storage
		$policy->addAllowedImageDomain('http://minio:9000');
		// configured appstore (may serve its own images)
		$storeUrl = $this->config->getSystemValue('appstoreurl', 'https://marketplace.owncloud.com');
		$storeDomain = $this->extractDomain($storeUrl);
		if ($storeDomain !== null) {
			$policy->addAllowedImageDomain($storeDomain);
		}
		$templateResponse->setContentSecurityPolicy($policy);

		return $templateResponse;
	}

	/**
	 * Reduce a URL to scheme + host (+ port) since CSP works on the domain
	 * level - any path in the configured appstore URL must be stripped.
	 *
	 * @return string|null the domain, or null if it cannot be determined
	 */
	private function extractDomain(string $url): ?string {
		$parts = \parse_url($url);
		if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
			return null;
		}
		$domain = $parts['scheme'] . '://' . $parts['host'];
		if (isset($parts['port'])) {
			$domain .= ':' . $parts['port'];
		}
		return $domain;
	}
}
