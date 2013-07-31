<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/
// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );
if ( count($this->newsletters) > 0 ) {
    $i = 0;
    foreach ($this->newsletters as $newsletter) { ?>
      <?php $i %= 2; ?>
        <tr class="cobalt_row_<?php echo $i; ?>">
          <td class="mailing_list_subject">
              <?php if ($newsletter->open) { ?>
                  <a href="javascript:void(0);" onclick="showNewsletterLinks(<?php echo $newsletter->mailid; ?>);">
              <?php } ?>
                  <?php echo $newsletter->subject; ?></td>
              <?php if ($newsletter->open) { ?>
                  </a>
              <?php } ?>
          <td class="mailing_list_senddate"><?php echo CobaltHelperDate::formatDate($newsletter->senddate); ?></td>
          <td class="mailing_list_open"><?php echo $newsletter->open ? CRMText::_('COBALT_YES') : CRMText::_('COBALT_NO') ; ?></td>
        </tr>
     <?php $i++; ?>
<?php } }
