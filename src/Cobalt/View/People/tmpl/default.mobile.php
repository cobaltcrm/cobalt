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

    $app = \Cobalt\Factory::getApplication();
    $lead = $app->input->get('type')=='leads' ? '&lead=true' : '';
?>

<script type="text/javascript">
    var loc = 'people';
</script>

    <div data-role='header' data-theme='b'>
        <h1><?php echo TextHelper::_('COBALT_LEADS_HEADER'); ?></h1>
            <a href="<?php echo RouteHelper::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
                <?php echo TextHelper::_('COBALT_BACK'); ?>
            </a>
            <a href="<?php echo RouteHelper::_('index.php?view=people&layout=edit&'.$lead); ?>" data-icon="plus" class="ui-btn-right">
                <?php echo TextHelper::_('COBALT_NEW'); ?>
            </a>
    </div>

    <div data-role="content">
        <ul class="ui-listview" data-role="listview" data-filter="true" data-autodividers="true" data-theme="c">
            <?php
            $n = count($this->people);
            $k = 0;
                for ($i=0;$i<$n;$i++) {
                    $person = $this->people[$i];
                    if ($i==0 || substr_compare($person['last_name'],$this->people[$i-1]['last_name'],0)) {
                        echo "<li data-role='list-divider'>".ucfirst(substr($person['last_name'],0,1))."</li>";
                    }
            ?>
                <li data-filtertext="<?php echo $person['first_name'].' '.$person['last_name']; ?>">
                    <a href="<?php echo RouteHelper::_('index.php?view=people&layout=person&id='.$person['id']); ?>">
                        <h3 class="ui-li-heading"><?php echo $person['first_name'].' '.$person['last_name']; ?></h3>
                        <p class="ui-li-desc"><?php echo JText::sprintf('COBALT_MOBILE_PERSON_DESC',$person['position'],$person['company_name']); ?></p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
