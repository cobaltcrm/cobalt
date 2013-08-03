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
$format = $app->input->get('format');

$person = $this->person;
if ( array_key_exists('company_id',$person) ) { $company_id = $person['company_id']; } elseif ($app->input->get('company_id')) { $company_id = $app->input->get('company_id');} else {$company_id = "";}
?>
<?php if ($format!='raw') { ?>
    <h1><?php echo $person['header']; ?></h1>
<?php } ?>

<form id="edit_form" method="post" action="<?php echo 'index.php?controller=people&task=save'; ?>" onsubmit="return save(this)" >

<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#Person" data-toggle="tab" >Person</a></li>
  <li><a href="#Home" data-toggle="tab" >Home</a></li>
  <li><a href="#Work" data-toggle="tab">Work</a></li>
  <li><a href="#Assignment" data-toggle="tab" >Assignment</a></li>
  <li><a href="#Details" data-toggle="tab">Details</a></li>
  <li><a href="#Custom" data-toggle="tab">Custom</a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane active fade in" id="Person">
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_FIRST'); ?><span class="required">*</span></div>
            <div class="cobaltValue wide"><input class="required form-control" type="text" name="first_name" placeholder="<?php echo TextHelper::_('COBALT_PERSON_FIRST_NULL'); ?>" value="<?php echo $person['first_name']; ?>" /></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_LAST'); ?><span class="required">*</span></div>
            <div class="cobaltValue wide"><input class="required form-control" type="text" name="last_name" placeholder="<?php echo TextHelper::_('COBALT_PERSON_LAST_NULL'); ?>" value="<?php echo $person['last_name']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON_COMPANY')); ?></div>
            <div class="cobaltValue">
                <input type="text" onblur="checkCompanyName(this);" class="form-control" name="company" id="company_name" value="<?php if ( array_key_exists('company_name',$person) ) echo $person['company_name']; ?>" />
                <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id; ?>" />
                <div id="company_message"></div>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_POSITION'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="position" placeholder="<?php echo TextHelper::_('COBALT_PERSON_POSITION_NULL'); ?>" value="<?php echo $person['position']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_PHONE'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="phone" placeholder="<?php echo TextHelper::_('COBALT_PERSON_PHONE_NULL'); ?>" value="<?php echo $person['phone']; ?>"/></div>
        </div>
        <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_EMAIL'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" name="email" placeholder="<?php echo TextHelper::_('COBALT_PERSON_EMAIL_NULL'); ?>" value="<?php echo $person['email']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <label class="checkbox">
                <div class="cobaltField"></div>
                <div class="cobaltValue"><input class="form-control" value="lead" type="checkbox" name="type" <?php $checked = ( $person['type'] == "Lead") ? "checked" : ""; echo $checked; ?> /><?php echo TextHelper::_('COBALT_THIS_PERSON_IS_A_LEAD'); ?></div>
            </label>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON_SOURCE')); ?></div>
            <div class="cobaltValue">
                <?php echo DropdownHelper::generateDropdown('source',$person['source_id']); ?>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON_STATUS')); ?></div>
            <div class="cobaltValue">
                <?php echo DropdownHelper::generateDropdown('people_status',$person['status_id']); ?>
            </div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_PERSON_DEAL')); ?></div>
            <div class="cobaltValue">
                <?php
                    if ( array_key_exists('deal_id',$person) && $person['deal_id'] ) {
                        echo $person['deal_name'];
                    } else {
                        echo DropdownHelper::generateDropdown('deal');
                    }
                ?>
            </div>
        </div>
  </div>
  <div class="tab-pane fade" id="Home">
        <div id="home_info" >
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_HOME'); ?></div>
                <div class="cobaltValue">
                    <input class="form-control address_one" placeholder="<?php echo TextHelper::_("COBALT_ADDRESS_1_NULL"); ?>" type="text" name="home_address_1" value="<?php if ( array_key_exists('home_address_1',$person)) echo $person['home_address_1']; ?>" />
                    <br />
                    <input class="form-control address_two" placeholder="<?php echo TextHelper::_("COBALT_ADDRESS_2_NULL"); ?>" type="text" name="home_address_2" value="<?php if ( array_key_exists('home_address_2',$person)) echo $person['home_address_2']; ?>" />
                    <br />
                    <input class="form-control address_city" placeholder="<?php echo TextHelper::_("COBALT_CITY_NULL"); ?>" type="text" name="home_city" value="<?php if ( array_key_exists('home_city',$person)) echo $person['home_city']; ?>" />
                    <input class="form-control address_state" placeholder="<?php echo TextHelper::_("COBALT_STATE_NULL"); ?>" type="text" name="home_state" value="<?php if ( array_key_exists('home_state',$person)) echo $person['home_state']; ?>" />
                    <input class="form-control address_zip" placeholder="<?php echo TextHelper::_("COBALT_ZIP_NULL"); ?>" type="text" name="home_zip" value="<?php if ( array_key_exists('home_zip',$person)) echo $person['home_zip']; ?>" />
                    <br />
                    <input class="form-control address_country" placeholder="<?php echo TextHelper::_("COBALT_COUNTRY_NULL"); ?>" type="text" name="home_country" value="<?php if ( array_key_exists('home_country',$person)) echo $person['home_country']; ?>" />
                </div>
            </div>
        </div>
  </div>
  <div class="tab-pane fade" id="Work">
          <div id="work_info" >
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_WORK'); ?></div>
                <div class="cobaltValue">
                    <input class="form-control address_one" placeholder="<?php echo TextHelper::_("COBALT_ADDRESS_1_NULL"); ?>" type="text" name="work_address_1" value="<?php if ( array_key_exists('work_address_1',$person)) echo $person['work_address_1']; ?>" />
                    <br />
                    <input class="form-control address_two" placeholder="<?php echo TextHelper::_("COBALT_ADDRESS_2_NULL"); ?>" type="text" name="work_address_2" value="<?php if ( array_key_exists('work_address',$person)) echo $person['work_address_2']; ?>" />
                    <br />
                    <input class="form-control address_city" placeholder="<?php echo TextHelper::_("COBALT_CITY_NULL"); ?>" type="text" name="work_city" value="<?php if ( array_key_exists('work_city',$person)) echo $person['work_city']; ?>" />
                    <input class="form-control address_state" placeholder="<?php echo TextHelper::_("COBALT_STATE_NULL"); ?>" type="text" name="work_state" value="<?php if ( array_key_exists('work_state',$person)) echo $person['work_state']; ?>" />
                    <input class="form-control address_zip" placeholder="<?php echo TextHelper::_("COBALT_ZIP_NULL"); ?>" type="text" name="work_zip" value="<?php if ( array_key_exists('work_zip',$person)) echo $person['work_zip']; ?>" />
                    <br />
                    <input class="form-control address_country" placeholder="<?php echo TextHelper::_("COBALT_COUNTRY_NULL"); ?>" type="text" name="work_country" value="<?php if ( array_key_exists('work_country',$person)) echo $person['work_country']; ?>" />
                </div>
            </div>
        </div>
  </div>
  <div class="tab-pane fade" id="Assignment">
        <div id="assignment_info" >
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_OWNER'); ?></div>
                <div class="cobaltValue">
                    <input class="form-control" type="text" name="assignee_name" value="<?php if(array_key_exists('assignee_name',$person)) echo $person['assignee_name']; ?>"  />
                    <input type="hidden" name="assignee_id" value="<?php if(array_key_exists('assignee_id',$person) && $person['assignee_id'] != 0) echo $person['assignee_id']; ?>" />
                </div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_ASSIGNMENT_NOTE'); ?></div>
                <div class="cobaltValue">
                    <textarea class="form-control" name="assignment_note"><?php if(array_key_exists('assignment_note',$person)) echo $person['assignment_note']; ?></textarea>
                </div>
            </div>
        </div>
  </div>
  <div class="tab-pane fade" id="Details">
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_MOBILE_PHONE'); ?></div>
            <div class="cobaltValue">
                <input class="form-control" type="text" name="mobile_phone" value="<?php if(array_key_exists('mobile_phone',$person) && $person['mobile_phone'] != 0 ) echo $person['mobile_phone']; ?>" />
            </div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_HOME_EMAIL'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="home_email" value="<?php if(array_key_exists('home_email',$person)) echo $person['home_email']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_OTHER_EMAIL'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="other_email" value="<?php if(array_key_exists('other_email',$person)) echo $person['other_email']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_HOME_PHONE'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="home_phone" value="<?php if(array_key_exists('home_phone',$person)) echo $person['home_phone']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_FAX'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="fax" value="<?php if(array_key_exists('fax',$person)) echo $person['fax']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_WEBSITE'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="website" value="<?php if(array_key_exists('website',$person)) echo $person['website']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_FACEBOOK_URL'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="facebook_url" value="<?php if(array_key_exists('facebook_url',$person)) echo $person['facebook_url']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_TWITTER_USER'); ?></div>
            <div class="cobaltValue"><input class="form-control" data-minlength="4" type="text" name="twitter_user" value="<?php if(array_key_exists('twitter_user',$person)) echo $person['twitter_user']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_LINKEDIN_URL'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="linkedin_url" value="<?php if(array_key_exists('linkedin_url',$person)) echo $person['linkedin_url']; ?>" /></div>
        </div>
        <div class="cobaltRow" id="other_button">
            <div class="cobaltField"><?php echo TextHelper::_('COBALT_PERSON_AIM'); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="aim" value="<?php if(array_key_exists('aim',$person)) echo $person['aim']; ?>" /></div>
        </div>
  </div>
  <div class="tab-pane fade" id="Custom">
        <?php echo $this->edit_custom_fields_view->render(); ?>
  </div>
</div>
    <?php
        if ( $app->input->get('deal_id') ) {
            echo '<input type="hidden" name="deal_id" value="'.$person['deal_id'].'" />';
        }
        if ( array_key_exists('id',$person) && $person['id'] ) {
            echo '<input type="hidden" name="id" value="'.$person['id'].'" />';
        }
    ?>
    <input type="hidden" name="model" value="people" />
</form>
