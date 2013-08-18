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
if ($format != "raw")
{ ?>
    <h1><?php echo $header; ?></h1>
    
<?php } ?>

<form id="edit_form" method="POST" action="<?php echo 'index.php?task=companies.save'; ?>" onsubmit="return save(this)">
    <?php
    if ($this->company['id'] != -1)
    {
        echo '<input type="hidden" name="id" value="' . $this->company['id'] . '" />';
    }
    ?>
    <div id="editForm">

    <ul class="nav nav-tabs" id="myTab">
      <li class="active"><a href="#Company" data-toggle="tab" ><?php echo ucwords(TextHelper::_('COBALT_COMPANY')); ?></a></li>
      <li><a href="#Address" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ADDRESS')); ?></a></li>
      <li><a href="#Assignment" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ASSIGNMENT')); ?></a></li>
      <li><a href="#Details" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_DETAILS')); ?></a></li>
      <li><a href="#Custom" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_CUSTOM')); ?></a></li>
    </ul>
 
    <div class="tab-content">
      <div class="tab-pane active fade in" id="Company">
        <div class="row-fluid">
            <div class="span6">
            <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME')); ?><span class="required">*</span></strong></label>
            <input type="text" onblur="checkCompanyName(this);" class="required" name="name" id="company_name" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME_NULL')); ?>" value="<?php if (array_key_exists('name', $this->company))
                {
                    echo $this->company['name'];
                } ?>"/>
                <input type="hidden" name="company_id" id="company_id" value=""/>
                <div id="company_message"></div>
            </div>
            <div class="span6">
                <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_CATEGORY')); ?></strong></label>
                <?php echo CompanyHelper::getCategoryDropdown($this->company['category_id'],'category_id','span6'); ?>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
            <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE')); ?></strong></label>
            <input type="text" name="phone" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE_NULL')); ?>" value="<?php if (array_key_exists('phone', $this->company))
                {
                    echo $this->company['phone'];
                } ?>"/>
            </div>
            <div class="span6">
            <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX')); ?></strong></label>
            <input type="text" name="fax" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX_NULL')); ?>" value="<?php if (array_key_exists('fax', $this->company))
                {
                    echo $this->company['fax'];
                } ?>"/>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
            <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL')); ?></strong></label>
            <input type="text" name="email" placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL_NULL')); ?>" value="<?php if (array_key_exists('email', $this->company))
                {
                    echo $this->company['email'];
                } ?>"/>
            </div>
            <div class="span6">
            <label><strong><?php echo TextHelper::_('COBALT_COMPANY_WEB'); ?></strong></label>
                <input type="text" placeholder="<?php echo TextHelper::_('COBALT_WEBSITE_NULL'); ?>" name="website" value="<?php if (array_key_exists('website', $this->company))
                {
                    echo $this->company['website'];
                } ?>"/>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="Address">
        <div class="row-fluid" id="address_button">
            <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ADDRESS')); ?></strong></label>
        </div>
        <div id="address_info">
            <div class="row-fluid">
                    <input class="inputbox address_one" type="text" placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_1_NULL'); ?>" name="address_1" value="<?php if (array_key_exists('address_1', $this->company))
                    {
                        echo $this->company['address_1'];
                    } ?>"/>
                    <br/>
                    <input class="inputbox address_two" type="text" placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_2_NULL'); ?>" name="address_2" value="<?php if (array_key_exists('address_2', $this->company))
                    {
                        echo $this->company['address_2'];
                    } ?>"/>
                    <br/>
                    <input class="inputbox address_city" type="text" placeholder="<?php echo TextHelper::_('COBALT_CITY_NULL'); ?>" name="address_city" value="<?php if (array_key_exists('address_city', $this->company))
                    {
                        echo $this->company['address_city'];
                    } ?>"/>
                    <input class="inputbox address_state" type="text" placeholder="<?php echo TextHelper::_('COBALT_STATE_NULL'); ?>" name="address_state" value="<?php if (array_key_exists('address_state', $this->company))
                    {
                        echo $this->company['address_state'];
                    } ?>"/>
                    <input class="inputbox address_zip" type="text" placeholder="<?php echo TextHelper::_('COBALT_ZIP_NULL'); ?>" name="address_zip" value="<?php if (array_key_exists('address_zip', $this->company))
                    {
                        echo $this->company['address_zip'];
                    } ?>"/>
                    <br/>
                    <input class="inputbox address_country" type="text" placeholder="<?php echo TextHelper::_('COBALT_COUNTRY_NULL'); ?>" name="address_country" value="<?php if (array_key_exists('address_country', $this->company))
                    {
                        echo $this->company['address_country'];
                    } ?>"/>
            </div>
        </div>
    </div>
    <div class="tab-pane fade in" id="Assignment">
        <?php if (UsersHelper::getRole() == 'exec' || UsersHelper::getRole() == "manager" || !($this->company['id'] > 0) || (array_key_exists('owner_id', $this->company) && UsersHelper::getUserId() == $this->company['owner_id']) || UsersHelper::isAdmin())
        { ?>
            <div class="row-fluid">
                <label><strong><?php echo ucwords(TextHelper::_('COBALT_COMPANY_OWNER')); ?></strong></label>
                    <?php $ownerId = array_key_exists('owner_id', $this->company) ? $this->company['owner_id'] : UsersHelper::getUserId(); ?>
                    <?php echo DropdownHelper::getOwnerDropdown($ownerId); ?>
            </div>
        <?php } ?>
        <div class="row-fluid">
            <label><strong><?php echo TextHelper::_('COBALT_COMPANY_DESCRIPTION'); ?></strong></label>
                <textarea name="description"><?php if (array_key_exists('description', $this->company))
                    {
                        echo $this->company['description'];
                    } ?></textarea>
        </div>
    </div>
    <div class="tab-pane fade in" id="Details">
        <div class="row-fluid">
            <label><strong><?php echo TextHelper::_('COBALT_SOCIAL_MEDIA'); ?></strong></label>
                <input class="span6" type="text" name="facebook_url" value="<?php if (array_key_exists('facebook_url', $this->company))
                {
                    echo $this->company['facebook_url'];
                } ?>" placeholder="<?php echo TextHelper::_('COBALT_FACEBOOK_URL'); ?>" />
                <input class="span6" data-minlength="4" type="text" name="twitter_user" value="<?php if (array_key_exists('twitter_user', $this->company))
                {
                    echo $this->company['twitter_user'];
                } ?>" placeholder="<?php echo TextHelper::_('COBALT_TWITTER_USER'); ?>" />
        </div>
        <div class="row-fluid">
                <input class="span6" type="text" name="flickr_url" value="<?php if (array_key_exists('flickr_url', $this->company))
                {
                    echo $this->company['flickr_url'];
                } ?>" placeholder="<?php echo TextHelper::_('COBALT_FLICKR_URL'); ?>" />

                <input class="span6" type="text" name="youtube_url" value="<?php if (array_key_exists('youtube_url', $this->company))
                {
                    echo $this->company['youtube_url'];
                } ?>" placeholder="<?php echo TextHelper::_('COBALT_YOUTUBE_URL'); ?>" />
        </div>
    </div>
    <div class="tab-pane fade in" id="Custom">
        <?php if ($format != "raw")
        { ?>
            <?php echo $this->edit_custom_fields_view->display(); ?>
        <?php
        } ?>
    </div>
    </div>
</form>
