<?php

namespace TagConcierge\ConsentModeBannerFree;

use TagConcierge\ConsentModeBannerFree\DependencyInjection\Container;

class ConsentModeBanner {

	const SNAKE_CASE_NAMESPACE = 'gtm_consent_mode_banner';

	const SPINE_CASE_NAMESPACE = 'gtm-consent-mode-banner';

	private $container;

	private $pluginFile;

	public function __construct( $plugin_file ) {
		$this->pluginFile = $plugin_file;
	}

	public function initialize() {
		$this->container = new Container();

		$this->container->getOutputUtil()->setPluginFile( $this->pluginFile );
	}
}
