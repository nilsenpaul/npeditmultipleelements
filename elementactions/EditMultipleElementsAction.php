<?php
namespace Craft;

class EditMultipleElementsAction extends BaseElementAction
{
	public function getName()
	{
		return Craft::t('Edit selected elements sequentially');
	}

	public function getTriggerHtml()
	{
		$js = <<<EOT
(function()
{
	var trigger = new Craft.ElementActionTrigger({
		handle: 'EditMultipleElementsAction',
		batch: true,
		validateSelection: function(\$selectedItems)
		{	
			return true;
		},
		activate: function(\$selectedItems)
		{
			var firstTr = \$selectedItems.first();
			var firstId = firstTr.data('id');
			var firstElementToEdit = firstTr.find('.element[data-id="' + firstId + '"]');

			var remainingIds = [];
			\$selectedItems.each(function() {
				var id = $(this).data('id');
				if (id != firstId) {
					remainingIds.push(id);
				}
			});
			
			Craft.redirectTo(firstElementToEdit.find('a').attr('href') + '?remainingIds=' + remainingIds.join('|'));
		}
	});
})();
EOT;

		craft()->templates->includeJs($js);
	}
}
