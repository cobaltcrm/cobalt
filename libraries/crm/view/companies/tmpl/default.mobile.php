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

<script type="text/javascript">
    var loc = 'companies';
</script>

    <div data-role='header' data-theme='b'>
        <h1><?php echo ucwords(CRMText::_('COBALT_COMPANY_HEADER')); ?></h1>
            <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
            <?php echo CRMText::_('COBALT_BACK'); ?>
        </a>
    </div>

    <div data-role="content">
        <ul class="ui-listview" data-role="listview" data-filter="true" data-autodividers="true" data-theme="c">
            <?php
            $n = count($this->companies);
            $k = 0;
                for ($i=0;$i<$n;$i++) {
                    $company = $this->companies[$i];
                    if ($i==0 || substr_compare($company['name'],$this->companies[$i-1]['name'],0,1)) {
                        echo "<li data-role='list-divider'>".ucfirst(substr($company['name'],0,1))."</li>";
                    }
            ?>
                <li data-filtertext="<?php echo $company['name']; ?>">
                    <a href="<?php echo JRoute::_('index.php?view=companies&layout=company&id='.$company['id']); ?>">
                        <h3 class="ui-li-heading"><?php echo $company['name']; ?></h3>
                        <p class="ui-li-desc">
                            <?php echo '<br />'.$company['phone']; ?></p>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
