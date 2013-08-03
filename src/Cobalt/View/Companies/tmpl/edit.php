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
$company = $this->company;
?>
<form id="edit_form" method="post" action="<?php echo 'index.php?view=companies&controller=save'; ?>" onsubmit="return save(this)" >

    <ul class="nav nav-tabs" id="myTab">
      <li class="active"><a href="#Company" data-toggle="tab" >Company</a></li>
      <li><a href="#Address" data-toggle="tab" >Address</a></li>
      <li><a href="#Details" data-toggle="tab">Details</a></li>
      <li><a href="#Custom" data-toggle="tab" >Custom</a></li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane active fade in" id="Company">
            <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME')); ?><span class="required">*</span></div>
            <div class="cobaltValue"><input class="required form-control" type="text" name="name" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME_NULL')); ?>" value="<?php if(array_key_exists('name',$company)) echo $company['name']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE')); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="phone" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE_NULL')); ?>" value="<?php if(array_key_exists('phone',$company)) echo $company['phone']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX')); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="fax" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX_NULL')); ?>" value="<?php if(array_key_exists('fax',$company)) echo $company['fax']; ?>"/></div>
        </div>
        <div class="cobaltRow">
            <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL')); ?></div>
            <div class="cobaltValue"><input class="form-control" type="text" name="email" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL_NULL')); ?>" value="<?php if(array_key_exists('email',$company)) echo $company['email']; ?>"/></div>
        </div>
      </div>
      <div class="tab-pane fade" id="Address">
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ADDRESS')); ?></div>
                <div class="cobaltValue">
                    <input class="form-control address_one" type="text" placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_1_NULL'); ?>" name="address_1" value="<?php if(array_key_exists('address_1',$company)) echo $company['address_1']; ?>" />
                    <br />
                    <input class="form-control address_two" type="text" placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_2_NULL'); ?>" name="address_2" value="<?php if(array_key_exists('address_2',$company)) echo $company['address_2']; ?>" />
                    <br />
                    <input class="form-control address_city" type="text" placeholder="<?php echo TextHelper::_('COBALT_CITY_NULL'); ?>" name="address_city" value="<?php if(array_key_exists('address_city',$company)) echo $company['address_city']; ?>" />
                    <input class="form-control address_state" type="text" placeholder="<?php echo TextHelper::_('COBALT_STATE_NULL'); ?>" name="address_state" value="<?php if(array_key_exists('address_state',$company)) echo $company['address_state']; ?>" />
                    <input class="form-control address_zip" type="text" placeholder="<?php echo TextHelper::_('COBALT_ZIP_NULL'); ?>" name="address_zip" value="<?php if(array_key_exists('address_zip',$company)) echo $company['address_zip']; ?>" />
                    <br />
                    <input class="form-control address_country" type="text" placeholder="<?php echo TextHelper::_('COBALT_COUNTRY_NULL'); ?>" name="address_country" value="<?php if(array_key_exists('address_country',$company)) echo $company['address_country']; ?>" />
                </div>
            </div>
      </div>
      <div class="tab-pane fade" id="Details">
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_COMPANY_WEB'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" placeholder="<?php echo TextHelper::_('COBALT_WEBSITE_NULL'); ?>" name="website" value="<?php if(array_key_exists('website',$company)) echo $company['website']; ?>" /></div>
            </div>
            <div class="cobaltRow">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_COMPANY_DESCRIPTION'); ?></div>
                <div class="cobaltValue">
                    <textarea class="form-control" name="description"><?php if(array_key_exists('description',$company)) echo $company['description']; ?></textarea>
                </div>
            </div>
            <div class="cobaltRow" id="other_button">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_FACEBOOK_URL'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" name="facebook_url" value="<?php if(array_key_exists('facebook_url',$company)) echo $company['facebook_url']; ?>" /></div>
            </div>
            <div class="cobaltRow" id="other_button">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_TWITTER_USER'); ?></div>
                <div class="cobaltValue"><input class="form-control" data-minlength="4" type="text" name="twitter_user" value="<?php if(array_key_exists('twitter_user',$company)) echo $company['twitter_user']; ?>" /></div>
            </div>
            <div class="cobaltRow" id="other_button">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_FLICKR_URL'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" name="flickr_url" value="<?php if(array_key_exists('flickr_url',$company)) echo $company['flickr_url']; ?>" /></div>
            </div>
            <div class="cobaltRow" id="other_button">
                <div class="cobaltField"><?php echo TextHelper::_('COBALT_YOUTUBE_URL'); ?></div>
                <div class="cobaltValue"><input class="form-control" type="text" name="youtube_url" value="<?php if(array_key_exists('youtube_url',$company)) echo $company['youtube_url']; ?>" /></div>
            </div>
      </div>
      <div class="tab-pan fade" id="Custom">
              <?php echo $this->edit_custom_fields_view->render(); ?>
      </div>
    </div>
        <?php
            if ($company['id'] != -1) {
                echo '<input type="hidden" name="id" value="'.$company['id'].'" />';
            }
        ?>
    <input type="hidden" name="model" value="company" />
</form>
