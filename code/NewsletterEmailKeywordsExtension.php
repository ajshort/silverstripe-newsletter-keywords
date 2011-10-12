<?php
/**
 * Performs simple keyword replacement on the email before sending.
 *
 * @package silverstripe-newsletter-keywords
 */
class NewsletterEmailKeywordsExtension extends Extension {

	public function onBeforeSend() {
		$body         = $this->owner->Body();
		$replacements = $this->owner->Newsletter()->getKeywordReplacements($this->owner);
		$keywords     = array();

		foreach ($replacements as $k => $v) {
			$keywords[] = "{\$$k}";
		}

		$this->owner->setBody(DBField::create('HTMLText', str_replace(
			$keywords,
			array_values($replacements),
			is_object($body) ? $body->forTemplate() : $body
		)));
	}

}
