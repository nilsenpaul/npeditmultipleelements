<?php
/**
 * NP Edit Multiple Elements plugin for Craft CMS
 *
 * This plugin lets you edit multiple elements sequentially.
 *
 * @author	Paul Verheul for Nils en Paul
 * @copyright Copyright (c) 2016 Paul Verheul for Nils en Paul
 * @link	  https://www.nilsenpaul.nl
 * @package   NpEditMultipleElements
 * @since	 1.0.0
 */

namespace Craft;

class NpEditMultipleElementsPlugin extends BasePlugin
{
	public function init()
	{
		require_once('elementactions/EditMultipleElementsAction.php');

		// Do we have a remainingIds param? If so, fill sessions and redirect to first element edit page
		if (craft()->request->isGetRequest && craft()->request->getParam('remainingIds') !== null) {
			// Put ids in a session
			craft()->session->add('npEditMultiple_first', explode('-', craft()->request->segments[2])[0]);
			craft()->session->add('npEditMultiple_remaining', explode('|', craft()->request->getParam('remainingIds')));
			craft()->session->add('npEditMultiple_locale', isset(craft()->request->segments[3]) ? craft()->request->segments[3] : null);
			
			return craft()->request->redirect(UrlHelper::getUrl(craft()->request->path));
		}

		// Any other than an edit page and session is set? Destroy session value.
		craft()->npEditMultipleElements->handleNonEditRequests();

		// Listen for onSaveEntry event
		craft()->on('entries.saveEntry', function(Event $event) {
			if (!$event->params['isNewEntry']) {
				craft()->npEditMultipleElements->handleSaveEvent();
			}
		});

		// Listen for onSaveCategory event
		craft()->on('categories.saveCategory', function(Event $event) {
			if (!$event->params['isNewCategory']) {
				craft()->npEditMultipleElements->handleSaveEvent();
			}
		});
	}

	public function getName()
	{
		 return Craft::t('NP Edit Multiple Elements');
	}

	public function getDescription()
	{
		return Craft::t('This plugin lets you edit multiple entries or categories sequentially.');
	}

	public function getVersion()
	{
		return '1.0.0';
	}

	public function getSchemaVersion()
	{
		return '1.0.0';
	}

	public function getDeveloper()
	{
		return 'Paul Verheul for Nils & Paul';
	}

	public function getDeveloperUrl()
	{
		return 'https://www.nilsenpaul.nl';
	}

	public function addEntryActions($source)
	{
		return $this->getActionsToAdd($source);
	}

	public function addCategoryActions($source)
	{
		return $this->getActionsToAdd($source);
	}

	protected function getActionsToAdd($source)
	{
		return [
			new EditMultipleElementsAction(),
		];
	}
}
