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
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php if ( is_array($this->mail) && count ( $this->mail ) > 0 ) { foreach ($this->mail as $email) { ?>
    <tr id="email_row_<?php echo $email['overview']->msgno; ?>">
        <td><a href="javascript:void(0);" onclick="readEmail(<?php echo $email['overview']->msgno; ?>);"><?php  echo $email['overview']->subject; ?></a></td>
        <td><?php if ( array_key_exists('headers',$email) && isset($email['headers']->from) && array_key_exists(0,$email['headers']->from) ) { echo "<a href='mailto:".$email['headers']->from[0]->mailbox.'@'.$email['headers']->from[0]->host."'>".$email['headers']->from[0]->mailbox.'@'.$email['headers']->from[0]->host.'</a>'; } ?></td>
        <td><?php if ( array_key_exists('headers',$email) && isset($email['headers']->to) && array_key_exists(0,$email['headers']->to) ) { echo "<a href='mailto:".$email['headers']->to[0]->mailbox.'@'.$email['headers']->to[0]->host."'>".$email['headers']->to[0]->mailbox.'@'.$email['headers']->to[0]->host.'</a>'; } ?></td>
        <td><?php  echo CobaltHelperDate::formatDate($email['overview']->date); ?></td>
        <td>
            <a href="javascript:void(0);" onclick="deleteEmail(<?php echo $email['overview']->msgno; ?>);" class="delete"></a>
             <div class="email_modal" id="email_modal_<?php echo $email['overview']->msgno; ?>">
                <form action="" method="post" id="email_form_<?php echo $email['overview']->msgno; ?>">
                    <div class="email_association_message"><?php echo CRMText::_('COBALT_EMAIL_ASSOCIATION_MESSAGE'); ?></div>
                    <div class="message_header"><?php echo ucwords(CRMText::_('COBALT_PERSON_FIRST_OR_LAST')); ?></div>
                    <div>
                        <input type="text" id="person_name_<?php echo $email['overview']->msgno; ?>" onkeyup="checkPersonName(<?php echo $email['overview']->msgno; ?>)" name="person_name" class="inputbox" />
                        <input type="hidden" id="person_id_<?php echo $email['overview']->msgno; ?>" name="person_id" value="" />
                        <span class="person_message" id="person_message_<?php echo $email['overview']->msgno; ?>" >
                        </span>
                    </div>
                    <div class="message_header"><?php echo ucwords(CRMText::_('COBALT_DEAL_NAME_EMAIL')); ?></div>
                    <div>
                        <input type="text" id="deal_name_<?php echo $email['overview']->msgno; ?>"onkeyup="checkDealName(<?php echo $email['overview']->msgno; ?>)" name="deal_name" class="inputbox" />
                        <input type="hidden" id="deal_id_<?php echo $email['overview']->msgno; ?>"name="deal_id" value="" />
                        <span class="deal_message" id="deal_message<?php echo $email['overview']->msgno; ?>">
                        </span>
                    </div>
                    <div><input type="button" class="button" onclick="saveEmail(<?php echo $email['overview']->msgno; ?>);" value="<?php echo CRMText::_('COBALT_SAVE'); ?>" /></div>
                    <div class="message_header"><?php echo ucwords(CRMText::_('COBALT_MESSAGE_CONTENT')); ?></div>
                    <div class="email_message">
                        <?php echo nl2br(htmlspecialchars($email['message'])); ?>
                    </div>
                </form>
            </div>
        </td>
    </tr>
<?php } }
