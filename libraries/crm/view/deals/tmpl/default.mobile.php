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
defined( '_CEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
    var loc = 'deals';
</script>

    <div data-role='header' data-theme='b'>
        <h1><?php echo ucwords(CRMText::_('COBALT_DEALS_HEADER')); ?></h1>
            <a href="<?php echo JRoute::_('index.php?view=dashboard'); ?>" data-icon="back" class="ui-btn-left">
            <?php echo CRMText::_('COBALT_BACK'); ?>
        </a>
    </div>

    <div data-role="content">
    <ul class="ui-listview" data-role="listview" data-filter="true" data-autodividers="true" data-theme="c">
        <?php
            $n = count($this->dealList);
            $k = 0;
                for ($i=0;$i<$n;$i++) {
                    $deal = $this->dealList[$i];
                    $k = $i%2;
                    if ($i==0 || substr_compare($deal['name'],$this->dealList[$i-1]['name'],0,1)) {
                        echo "<li data-role='list-divider'>".ucfirst(substr($deal['name'],0,1))."</li>";
                    }
                    echo '<li data-filtertext="'.$deal['name'].'" ><a href="'.JRoute::_('index.php?view=deals&layout=deal&id='.$deal['id']).'">'.$deal['name'].'</a></li>';

                }
        ?>
    </ul>
    </div><!-- /content -->
