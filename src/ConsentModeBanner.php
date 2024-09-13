<?php

namespace TagConcierge\ConsentModeBannerFree;

use TagConcierge\ConsentModeBannerFree\DependencyInjection\Container;

class ConsentModeBanner {

	const SNAKE_CASE_NAMESPACE = 'gtm_consent_mode_banner';

	const SPINE_CASE_NAMESPACE = 'gtm-consent-mode-banner';

	const DATALAYER_SCRIPT_HANDLE = 'gtm-consent-mode-datalayer';

	const BANNER_SCRIPT_HANDLE = 'gtm-consent-mode-banner';

	private $container;

	private $pluginFile;

	public function __construct( $plugin_file ) {
		$this->pluginFile = $plugin_file;
	}

	public function initialize() {
		$this->container = new Container();

		add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	}

	public function getAssetUrl( $relativePath) {
		return plugins_url($relativePath, $this->pluginFile);
	}

	public function isDisabled() {
		return '1' === $this->container->getSettingsUtil()->getOption('disabled');
	}

	public function enqueueScripts() {
		if ($this->isDisabled()) {
			return;
		}

		wp_register_script(self::DATALAYER_SCRIPT_HANDLE, '', [], false, true);
		
		wp_add_inline_script(
			self::DATALAYER_SCRIPT_HANDLE, 
			$this->container->getGtmConsentModeService()->initialScripts()
		);
		
		wp_enqueue_script( 
			self::BANNER_SCRIPT_HANDLE, 
			$this->getAssetUrl('consent-banner/script.js'), 
			[
				self::DATALAYER_SCRIPT_HANDLE,
			],
			'1.2.3',
			true
		);

		wp_add_inline_script(
			self::BANNER_SCRIPT_HANDLE, 
			$this->container->getGtmConsentModeService()->bannerScripts()
		);

		wp_enqueue_style(
			self::BANNER_SCRIPT_HANDLE,
			$this->getAssetUrl('consent-banner/style-light.css')
		);
	}
}
