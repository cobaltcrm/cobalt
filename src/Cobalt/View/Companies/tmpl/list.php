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
$app = JFactory::getApplication();

?>
<thead>
    <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
    <th class="name"><div class="sort_order"><a href="javascript:void(0);" class="c.name" onclick="sortTable('c.name',this)"><?php echo TextHelper::_('COBALT_COMPANIES_NAME'); ?></a></div></th>
    <th class="contact"><?php echo ucwords(TextHelper::_('COBALT_CONTACT_DETAILS')); ?></th>
    <th class="added" ><div class="sort_order"><a href="javascript:void(0);" class="c.created" onclick="sortTable('c.created',this)"><?php echo TextHelper::_('COBALT_COMPANIES_ADDED'); ?></a></div></th>
    <th class="updated" ><div class="sort_order"><a href="javascript:void(0);" class="c.modified" onclick="sortTable('c.modified',this)"><?php echo TextHelper::_('COBALT_COMPANIES_UPDATED'); ?></a></div></th>
    <th class="notes" >&nbsp;</th>
</thead>
<tbody id="list">
<?php
    $n = count($this->companies);
    for ($i=0; $i<$n; $i++) {
        $company = $this->companies[$i];
        $k = $i%2;
        $cView = ViewHelper::getView('companies','entry','phtml');
        $cView->company = $company;
        $cView->k = $k;
        echo $cView->render();
    } ?>
</tbody>
<tfoot>
    <tr>
       <td colspan="20"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
 </tfoot>
<script type="text/javascript">
    //update company count
    jQuery("#companies_matched").empty().html("<?php echo $this->total; ?>");
    window.top.window.assignFilterOrder();
</script>
