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

$person = $this->person;
$mediaURI = \Cobalt\Factory::getApplication()->get('uri.media.full');
echo '<tr class="cobalt_row_'.$this->k.'">';

    if ( array_key_exists('avatar',$person) && $person->avatar)
    {
        echo '<td><img src="' . $mediaURI . 'avatars/' . $person->avatar . '"/></td>';
    }
    else
    {
        echo '<td><img src="' . $mediaURI . 'images/person.png"/></td>';
    }
    
    echo '<td><a href="'.RouteHelper::_('index.php?view=people&layout=person&id=' . $person->id) . '">' . $person->first_name . ' ' . $person->last_name . '</a></td>';
    echo '<td>' . $person->phone . '</td>';
    echo '<td>' . $person->owner_first_name . ' ' . $person->owner_last_name . '</td>';
    echo '<td>' . ucwords($person->type).'</td>';
    echo '<td>' . DateHelper::formatDate($person->modified).'</td>';
echo '</tr>';
