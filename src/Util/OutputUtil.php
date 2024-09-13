<?php

namespace TagConcierge\ConsentModeBannerFree\Util;

class OutputUtil {

	private $inlineScripts = ['header' => [], 'footer' => []];
	private $scriptFiles = ['header' => [], 'footer' => []];
	private $styleFiles = [];
	private $pluginFile;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [$this, 'wpEnqueueScripts'] );
	}

	public function setPluginFile( $pluginFile) {
		$this->pluginFile = $pluginFile;
	}

	public function getAssetUrl( $relativePath) {
		return plugins_url($relativePath, $this->pluginFile);
	}

	public function addScript( $handle, $url, $footer = true): OutputUtil {
		$this->scriptFiles[true === $footer ? 'footer' : 'header'][$handle] = $url;
		
		return $this;
	}

	private function hasScript( $handle, $footer = true): bool {
		return isset($this->scriptFiles[true === $footer ? 'footer' : 'header'][$handle]);
	}

	public function addInlineScript( $handle, $script, $footer = true): OutputUtil {
		$placement = ( true === $footer ) ? 'footer' : 'header';

		if (!isset($this->inlineScripts[$placement][$handle])) {
			$this->inlineScripts[$placement][$handle] = [];
		}

		// Ensure that a parent exists for this inline script.
		if (!$this->hasScript($handle, $placement)) {
			$this->addScript($handle, false, $placement);
		}

		$this->inlineScripts[$placement][$handle][] = $script;

		return $this;
	}

	public function addStyle( $handle, $url): OutputUtil {
		$this->styleFiles[$handle] = $url;

		return $this;
	}

	public function wpEnqueueScripts(): void {
		foreach ($this->scriptFiles['header'] as $handle => $url) {
			wp_enqueue_script($handle, empty($url) ? null : $url);

			if (!empty($this->inlineScripts['header'][$handle])) {
				foreach ($this->inlineScripts['header'][$handle] as $script) {
					wp_add_inline_script($handle, $script, 'after');
				}
			}
		}

		foreach ($this->scriptFiles['footer'] as $handle => $url) {
			wp_enqueue_script($handle, empty( $url ) ? null : $url, [], null, true);

			if (!empty($this->inlineScripts['footer'][$handle])) {
				foreach ($this->inlineScripts['footer'][$handle] as $script) {
					wp_add_inline_script($handle, $script, 'after');
				}
			}
		}

		foreach ($this->styleFiles as $handle => $url) {
			wp_enqueue_style($handle, $url);
		}
	}
}
