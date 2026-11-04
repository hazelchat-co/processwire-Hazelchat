<?php namespace ProcessWire;

/*
 * Hazelchat for ProcessWire: adds the Hazelchat AI chat agent to every page
 * of a ProcessWire site from one installation code.
 *
 * (C) 2026 Hazelchat. MIT License; see LICENSE.
 */

require_once __DIR__ . '/Key.php';
require_once __DIR__ . '/Tag.php';

use Hazelchat\ProcessWire\Key;
use Hazelchat\ProcessWire\Tag;

/**
 * The installation code and one switch live in this module's settings
 * (Modules → Configure → Hazelchat, HazelchatConfig.php), which ProcessWire
 * opens only to superusers and roles with module-admin. Only the key is
 * saved, never the pasted code.
 *
 * @property string $code the saved key, or ''
 * @property int|bool $consent "Wait for my consent manager"
 */
class Hazelchat extends WireData implements Module, ConfigurableModule {

	public static function getModuleInfo() {
		return array(
			'title' => 'Hazelchat',
			'summary' => 'Adds your Hazelchat AI chat agent to every page from one installation code.',
			'version' => '1.0.0',
			'author' => 'Hazelchat',
			'href' => 'https://hazelchat.co/integrations/processwire/',
			'icon' => 'comments',
			'autoload' => true,
			'singular' => true,
			'requires' => array('ProcessWire>=3.0.200', 'PHP>=7.3'),
		);
	}

	public function __construct() {
		parent::__construct();
		$this->set('code', '');
		$this->set('consent', 0);
	}

	public function init() {
		$this->addHookBefore('Modules::saveConfig', $this, 'hookSaveConfig');
	}

	public function ready() {
		$this->addHookAfter('Page::render', $this, 'hookRender');
	}

	/**
	 * The one sentence a refused code gets, here and in HazelchatConfig.
	 */
	public function refusedText() {
		return $this->_('This is not a Hazelchat installation code. Copy it again from Hazelchat.');
	}

	/**
	 * Saving the settings keeps only the key of a pasted installation code.
	 * Anything else that is not empty is refused in one sentence, and the
	 * key saved before stays.
	 */
	public function hookSaveConfig(HookEvent $event) {
		$modules = $this->wire()->modules;
		if($modules->getModuleClass($event->arguments(0)) !== 'Hazelchat') return;
		$data = $event->arguments(1);
		if(is_string($data)) {
			// saveConfig('Hazelchat', 'code', $value): one property.
			if($data !== 'code') return;
			$event->arguments(2, $this->cleanCode($event->arguments(2)));
			return;
		}
		if(!is_array($data)) return;
		if(array_key_exists('code', $data)) $data['code'] = $this->cleanCode($data['code']);
		if(array_key_exists('consent', $data)) $data['consent'] = empty($data['consent']) ? 0 : 1;
		$event->arguments(1, $data);
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	protected function cleanCode($value) {
		$input = is_string($value) ? trim($value) : '';
		if($input === '') return '';
		$key = Key::fromInput($input);
		if($key !== null) return $key;
		$this->error($this->refusedText());
		$saved = $this->wire()->modules->getConfig('Hazelchat', 'code');
		return Key::valid($saved) ? $saved : '';
	}

	/**
	 * The tag goes into the finished page being requested, just before its
	 * last </body>: not into pages a template renders inside it, not into
	 * field renders, the admin, FormBuilder's frame, AJAX requests or
	 * templates whose content type is not HTML.
	 */
	public function hookRender(HookEvent $event) {
		$page = $event->object;
		$html = $event->return;
		if(!is_string($html) || !$page instanceof Page || $page !== $this->wire()->page) return;
		$what = $event->arguments(0);
		if(is_string($what) && $what !== '' && ctype_alnum(str_replace('_', '', $what))) return;
		if($this->wire()->config->ajax) return;
		$template = $page->template;
		if(!$template || in_array($template->name, array('admin', 'form-builder'), true)) return;
		$type = strtolower(trim((string) $template->contentType));
		if($type !== '' && $type !== 'html' && strpos($type, 'text/html') !== 0) return;
		$event->return = Tag::insert($html, $this->tag());
	}

	/**
	 * The tag for the saved settings, or null when nothing may be printed.
	 */
	public function tag() {
		$key = Key::fromInput((string) $this->get('code'));
		if($key === null) return null;
		return Tag::tag($key, Tag::mode((bool) $this->get('consent'), $this->privacyWireActive()));
	}

	/**
	 * PrivacyWire is installed and switched on, so its banner decides.
	 */
	public function privacyWireActive() {
		$modules = $this->wire()->modules;
		if(!$modules->isInstalled('PrivacyWire')) return false;
		return (bool) $modules->getConfig('PrivacyWire', 'is_active');
	}

	/**
	 * PrivacyWire offers its visitors the Functional group. Without it, only
	 * "Accept all" allows the chat.
	 */
	public function privacyWireOffersGroup() {
		$groups = $this->wire()->modules->getConfig('PrivacyWire', 'cookie_groups');
		return is_array($groups) && in_array(Tag::PRIVACYWIRE_GROUP, $groups, true);
	}
}
