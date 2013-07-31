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
<?php if ( !($app->input->get('id')) ) { ?>
  <thead>
    <th class="checkbox_column"><input type="checkbox" onclick="selectAll(this);" /></th>
    <th class="avatar" ></th>
    <th><div class="sort_order"><a class="p.last_name" href="javascript:void(0);" onclick="sortTable('p.last_name',this)"><?php echo CRMText::_('COBALT_PEOPLE_NAME'); ?></a></div></th>
    <th class="company"><div class="sort_order"><a href="javascript:void(0);" class="c.name" onclick="sortTable('c.name',this)"><?php echo CRMText::_('COBALT_PEOPLE_COMPANY'); ?></a></div></th>
    <th class="owner" ><div class="sort_order"><a href="javascript:void(0);" class="u.last_name" onclick="sortTable('u.last_name',this)"><?php echo CRMText::_('COBALT_PEOPLE_OWNER'); ?></a></div></th>
    <th class="email" ><div class="sort_order"><a href="javascript:void(0);" class="p.email" onclick="sortTable('p.email',this)"><?php echo CRMText::_('COBALT_PEOPLE_EMAIL'); ?></a></div></th>
    <th class="phone" ><div class="sort_order"><a href="javascript:void(0);" class="p.phone" onclick="sortTable('p.phone',this)"><?php echo CRMText::_('COBALT_PEOPLE_PHONE'); ?></a></div></th>
    <th class="status" ><div class="sort_order"><a href="javascript:void(0);" class="stat.ordering" onclick="sortTable('stat.ordering',this)"><?php echo CRMText::_('COBALT_PEOPLE_STATUS'); ?></a></div></th>
    <th class="source" ><div class="sort_order"><a href="javascript:void(0);" class="source.ordering" onclick="sortTable('source.ordering',this)"><?php echo CRMText::_('COBALT_PEOPLE_SOURCE'); ?></a></div></th>
    <?php /*
        <th class="tags" ><?php echo CRMText::_('COBALT_PEOPLE_TAGS'); ?></th>
     */ ?>
    <th class="type" ><div class="sort_order"><a class="p.type" onclick="sortTable('p.type',this)"><?php echo CRMText::_('COBALT_PEOPLE_TYPE'); ?></a></div></th>
    <th class="notes" ><?php echo CRMText::_('COBALT_PEOPLE_NOTES'); ?></th>
    <th class="address" ><?php echo CRMText::_('COBALT_ADDRESS'); ?></th>
    <th class="added" ><div class="sort_order"><a class="p.created" onclick="sortTable('p.created',this)"><?php echo CRMText::_('COBALT_PEOPLE_ADDED'); ?></a></div></th>
    <th class="updated" ><div class="sort_order"><a class="p.modified" onclick="sortTable('p.modified',this)"><?php echo CRMText::_('COBALT_PEOPLE_UPDATED'); ?></a></div></th>
</thead>
<tbody id="list">
<?php } ?>
<?php
        $n = count($this->people);
        $k = 0;
            for ($i=0;$i<$n;$i++) {
                $person = $this->people[$i];
                $k = $i%2;
                $pView = CobaltHelperView::getView('people','entry','phtml');
                $pView->person = $person;
                $pView->k = $k;
                echo $pView->render();
            }
        ?>
<?php if ( !($app->input->get('id')) ) { ?>
    </tbody>
<tfoot>
    <tr>
       <td colspan="20"><?php echo $this->pagination->getListFooter(); ?></td>
    </tr>
 </tfoot>
<script type="text/javascript">
    //update total people count
    jQuery("#people_matched").empty().html("<?php echo $this->total; ?>");
    window.top.window.assignFilterOrder();
</script>
<?php }
