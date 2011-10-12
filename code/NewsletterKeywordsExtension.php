<?php
/**
 * Updates the CMS fields and provides the keyword replacements.
 *
 * @package silverstripe-newsletter-keywords
 */
class NewsletterKeywordsExtension extends DataObjectDecorator {

	public function updateCMSFields(FieldSet $fields) {
		Requirements::css('newsletter-keywords/css/NewsletterKeywordsAdmin.css');

		$keywords = $this->getKeywordReplacements();
		$list     = new DataObjectSet();

		foreach (array_keys($keywords) as $keyword) {
			$list->push(new ArrayData(array(
				'Keyword' => "{\$$keyword}",
				'Title'   => FormField::name_to_label($keyword)
			)));
		}

		$data = new ArrayData(array(
			'KeywordReplacements' => $list
		));
		$keywords = $data->renderWith('NewsletterKeywordReplacements');

		$fields->addFieldToTab('Root.Newsletter', new ToggleCompositeField(
			'KeywordReplacements', 'Keyword Replacements', new LiteralField('', $keywords)
		));
	}

	/**
	 * Returns the keyword replacements available on the newsletter email. You
	 * can hook into this using the `updateKeywordReplacements` extension hook.
	 *
	 * @param  NewsletterEmail $email
	 * @return array
	 */
	public function getKeywordReplacements(NewsletterEmail $email = null) {
		if ($email) {
			$member = DataObject::get_one('Member', sprintf(
				'"Email" = \'%s\'', Convert::raw2sql($email->To())
			));
		} else {
			$member = null;
		}

		$keywords = array(
			'ID'              => $member ? $member->ID : null,
			'FirstName'       => $member ? $member->FirstName : null,
			'Surname'         => $member ? $member->Surname : null,
			'Email'           => $member ? $member->Email : null,
			'UnsubscribeLink' => $email ? $email->UnsubscribeLink() : null
		);

		$this->owner->extend('updateKeywordReplacements', $keywords, $email);
		return $keywords;
	}

}
