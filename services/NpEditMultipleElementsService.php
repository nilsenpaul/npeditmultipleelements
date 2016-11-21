<?php
/**
 * NP Edit Multiple Elements plugin for Craft CMS
 *
 * NpEditMultipleElements Service
 *
 * @author    Paul Verheul for Nils en Paul
 * @copyright Copyright (c) 2016 Paul Verheul for Nils en Paul
 * @link      https://www.nilsenpaul.nl
 * @package   NpEditMultipleElements
 * @since     1.0.0
 */

namespace Craft;

class NpEditMultipleElementsService extends BaseApplicationComponent
{
	public function handleSaveEvent()
	{
		$elementsRemaining = craft()->session->get('npEditMultiple_remaining');
		if (!empty($elementsRemaining)) {
			$firstElementIdInLine = array_shift($elementsRemaining);
			craft()->session->add('npEditMultiple_remaining', $elementsRemaining);
			craft()->session->add('npEditMultiple_first', $firstElementIdInLine);
			
			return craft()->request->redirect(craft()->elements->getElementById($firstElementIdInLine, null, craft()->session->get('npEditMultiple_locale'))->cpEditUrl);
		} else {
			$this->destroySessions();
		}
	}

	public function handleNonEditRequests()
	{
		if (!craft()->request->isAjaxRequest && !empty(craft()->session->get('npEditMultiple_remaining'))) {
			// Destroy session if this is a non-edit request, or if the currently requested element is not in the list
			$segments = craft()->request->segments;
			if (isset($segments[2])) {
				$elementIdFromUri = explode('-', $segments[2])[0];
			}

			if (	
				!in_array($segments[0], ['entries', 'categories', 'users'])
				|| !isset($segments[2])
				|| (
					craft()->session->get('npEditMultiple_remaining')[0] != $elementIdFromUri
					&& craft()->session->get('npEditMultiple_first') != $elementIdFromUri
				)
			) {
					$this->destroySessions();
			}
		}
	}

	protected function destroySessions()
	{
			craft()->session->destroy('npEditMultiple_first');
			craft()->session->destroy('npEditMultiple_remaining');
			craft()->session->destroy('npEditMultiple_locale');
	}
}
