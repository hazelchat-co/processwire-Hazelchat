<?php namespace ProcessWire;

/*
 * This file is part of Hazelchat for ProcessWire.
 *
 * (C) 2026 Hazelchat. MIT License; see LICENSE.
 */

require_once __DIR__ . '/Key.php';

use Hazelchat\ProcessWire\Key;

/**
 * Modules → Configure → Hazelchat: the installation code, the consent
 * switch, and at most two lines of help (the sign-up link before a key is
 * saved; PrivacyWire's missing Functional group when that matters).
 */
class HazelchatConfig extends ModuleConfig {

	public function getDefaults() {
		return array(
			'code' => '',
			'consent' => 0,
		);
	}

	public function getInputfields() {
		$modules = $this->wire()->modules;
		$inputfields = $this->wire(new InputfieldWrapper());
		$hazelchat = $modules->get('Hazelchat');

		/** @var InputfieldText $code */
		$code = $modules->get('InputfieldText');
		$code->attr('name', 'code');
		$code->label = $this->_('Installation code');
		$code->description = $this->_('Paste the installation code from Hazelchat, or just its key. Leave it empty to remove the chat.');
		$code->icon = 'comments';
		$inputfields->add($code);

		if(!Key::valid($this->get('code'))) {
			/** @var InputfieldMarkup $signup */
			$signup = $modules->get('InputfieldMarkup');
			$signup->attr('name', '_signup');
			$signup->skipLabel = Inputfield::skipLabelHeader;
			$signup->value = '<p><a href="https://app.hazelchat.co/signup?utm_source=processwire&amp;utm_medium=module&amp;utm_campaign=settings" target="_blank" rel="noopener">'
				. $this->wire()->sanitizer->entities($this->_('Create a free Hazelchat account')) . '</a></p>';
			$inputfields->add($signup);
		}

		/** @var InputfieldCheckbox $consent */
		$consent = $modules->get('InputfieldCheckbox');
		$consent->attr('name', 'consent');
		$consent->label = $this->_('Visitor consent');
		$consent->checkboxLabel = $this->_('Wait for my consent manager');
		$consent->description = $this->_('The chat starts only once your cookie banner allows it.');
		if($hazelchat && $hazelchat->privacyWireActive()) {
			$consent->notes = $this->_('With PrivacyWire, the chat starts once the visitor allows Functional cookies.');
		}
		$inputfields->add($consent);

		if($this->get('consent') && $hazelchat && $hazelchat->privacyWireActive() && !$hazelchat->privacyWireOffersGroup()) {
			/** @var InputfieldMarkup $group */
			$group = $modules->get('InputfieldMarkup');
			$group->attr('name', '_privacywire');
			$group->skipLabel = Inputfield::skipLabelHeader;
			$group->value = '<p>' . $this->wire()->sanitizer->entities($this->_('PrivacyWire does not offer Functional cookies, so the chat starts only for visitors who accept all cookies; add Functional cookies to its cookie groups to let them choose it.')) . '</p>';
			$inputfields->add($group);
		}

		return $inputfields;
	}
}
