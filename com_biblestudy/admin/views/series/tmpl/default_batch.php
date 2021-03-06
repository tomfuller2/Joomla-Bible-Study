<?php
/**
 * Batch Template
 *
 * @package    BibleStudy.Admin
 * @copyright  2007 - 2015 (C) Joomla Bible Study Team All rights reserved
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.JoomlaBibleStudy.org
 * */
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
			<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('JBS_CMN_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('JBS_CMN_BATCH_TIP'); ?></p>

		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.access'); ?>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<?php echo JHtml::_('batch.language'); ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button"
		        onclick="document.id('batch-series-id');document.id('batch-access').value='';document.id('batch-language-id').value=''"
		        data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('serie.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>
