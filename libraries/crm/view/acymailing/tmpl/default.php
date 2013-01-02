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
<form class="print_form">
        <div id="controls_area">
            <a href="javascript:void(0);" onclick="manageMailingLists();" ><?php echo ucwords(CRMText::_('COBALT_MANAGE_MAILING_LISTS')); ?></a> 
        </div>
<div id="mailing_list">
<div class="container">
        <div class="filter_container">
            <?php echo CRMText::_('COBALT_SHOW_NEWSLETTERS_FOR'); ?>:
            <span class="filters" ><a class="dropdown" id="mailing_lists_link" ><?php if ( count($this->mailing_lists) > 0 ){ echo $this->mailing_lists[0]->name; } else { echo CRMText::_('COBALT_NO_MAILING_LISTS'); }  ?></a></span>
            <div class="filters" id="mailing_lists">
                <ul>
                    <?php
                        if ( count($this->mailing_lists) > 0 ){
                            foreach($this->mailing_lists as $list){
                                echo "<li><a class='filter_mailing_list_".$list->listid."' onclick='updateNewsletters(".$list->listid.")'>".$list->name."</a></li>";
                            }
                        } 
                    ?>
                </ul>
            </div>
        </div>
    <div id="task_container">
    <div id="newsletter_list">
        <table id="newsletter_table" class="com_cobalt_table">
            <thead>
                <tr>
                    <th><?php echo CRMText::_('COBALT_SUBJECT'); ?></th>
                    <th><?php echo CRMText::_('COBALT_SENDDATE'); ?></th>
                    <th><?php echo CRMText::_('COBALT_OPENED'); ?></th>
                </tr>
            </thead>
            <tbody id="newsletter_entries">
                <?php
                     $newsletter_list_view = CobaltHelperView::getView('acymailing','list','phtml',array('newsletters'=>$this->newsletters));
                     echo $newsletter_list_view->render();
                ?>
            </tbody>
        </table>
    </div>
    </div>
</div>
</div>
<div id="mailing_list_modal"></div>