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
$ownerId = is_object($this->company) && isset($this->company->owner_id) ? $this->company->owner_id : UsersHelper::getUserId();
$format = $app->input->get('format');
if ($format != "raw")
{ ?>
    <h1><?php echo $header; ?></h1>
    
<?php } ?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">&times;</span><span class="sr-only">
            <?php echo ucwords(TextHelper::_('COBALT_CLOSE')); ?>
        </span>
    </button>
    <h3 class="modal-title" id="dealModal">
        <?php if (isset($this->company->id) && $this->company->id) { ?>
            <?php echo ucwords(TextHelper::_('COBALT_EDIT_COMPANY')); ?>
        <?php } else { ?>
            <?php echo ucwords(TextHelper::_('COBALT_ADD_COMPANY')); ?>
        <?php } ?>
    </h3>
</div>
<div class="modal-body">
    <form id="edit_form" method="POST" action="<?php echo 'index.php?task=save'; ?>" onsubmit="return sumbitForm(this)">
        <?php
        if (isset($this->company->id) && $this->company->id != -1)
        {
            echo '<input type="hidden" name="id" value="' . $this->company->id . '" />';
        }
        ?>

        <ul class="nav nav-tabs" id="myTab">
            <li class="active">
                <a href="#Company" data-toggle="tab" ><?php echo ucwords(TextHelper::_('COBALT_COMPANY')); ?></a>
            </li>
            <li>
                <a href="#Address" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ADDRESS')); ?></a>
            </li>
            <li>
                <a href="#Assignment" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_COMPANY_ASSIGNMENT')); ?></a>
            </li>
            <li>
                <a href="#Details" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_SOCIAL_MEDIA')); ?></a>
            </li>
            <li>
                <a href="#Custom" data-toggle="tab"><?php echo ucwords(TextHelper::_('COBALT_CUSTOM')); ?></a>
            </li>
        </ul>
     
        <div class="tab-content">
          <div class="tab-pane active fade in" id="Company">
              <div class="row">
                  <div class="col-sm-12">
                      <div class="form-group">
                          <label class="control-label" for="company_name">
                              <?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME')); ?>*
                          </label>
                          <div class="controls">
                              <input  type="text"

                                      class="required form-control"
                                      name="name"
                                      id="company_name"
                                      placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_NAME_NULL')); ?>"
                                      value="<?php echo isset($this->company->name) ? $this->company->name : '' ?>"/>
                              <input type="hidden" name="company_id" id="company_id" value=""/>
                              <div id="company_message"></div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label class="control-label" for="company_phone">
                              <?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE')); ?>
                          </label>
                          <div class="controls">
                              <input  type="text"
                                      name="phone"
                                      id="company_phone"
                                      class="form-control"
                                      placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_PHONE_NULL')); ?>"
                                      value="<?php echo isset($this->company->phone) ? $this->company->phone : ''; ?>"/>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label class="control-label" for="company_fax">
                              <?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX')); ?>
                          </label>
                          <div class="controls">
                              <input  type="text"
                                      name="fax"
                                      id="company_fax"
                                      class="form-control"
                                      placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_FAX_NULL')); ?>"
                                      value="<?php echo isset($this->company->fax) ? $this->company->fax : "" ?>"/>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label class="control-label" for="company_email">
                              <?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL')); ?>
                          </label>
                          <div class="controls">
                              <input  type="text"
                                      name="email"
                                      id="company_email"
                                      class="form-control"
                                      placeholder="<?php echo ucwords(TextHelper::_('COBALT_COMPANY_EMAIL_NULL')); ?>"
                                      value="<?php echo isset($this->company->email)? $this->company->email : "" ?>"/>
                          </div>
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div class="form-group">
                          <label class="control-label" for="company_website">
                              <?php echo TextHelper::_('COBALT_COMPANY_WEB'); ?>
                          </label>
                          <div class="controls">
                              <input  type="text"
                                      name="website"
                                      id="company_website"
                                      class="form-control"
                                      placeholder="<?php echo TextHelper::_('COBALT_WEBSITE_NULL'); ?>"
                                      value="<?php echo isset($this->company->website) ? $this->company->website : ""?>"/>
                          </div>
                      </div>
                  </div>
              </div>
        </div>
        <div class="tab-pane fade in" id="Address">
            <div id="address_info">
                <br />
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_one"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_1_NULL'); ?>"
                                        name="address_1"
                                        value="<?php echo isset($this->company->address_1) ? $this->company->address_1 : "" ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_two"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_ADDRESS_2_NULL'); ?>"
                                        name="address_2"
                                        value="<?php echo isset($this->company->address_2) ? $this->company->address_2 : "" ?>"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_city"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_CITY_NULL'); ?>"
                                        name="address_city"
                                        value="<?php echo isset($this->company->address_city) ? $this->company->address_city : "" ?>"/>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_state"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_STATE_NULL'); ?>"
                                        name="address_state"
                                        value="<?php echo isset($this->company->address_state) ? $this->company->address_state : "" ?>"/>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_zip"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_ZIP_NULL'); ?>"
                                        name="address_zip"
                                        value="<?php echo isset($this->company->address_zip) ? $this->company->address_zip : "" ?>"/>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="controls">
                                <input  class="form-control address_country"
                                        type="text"
                                        placeholder="<?php echo TextHelper::_('COBALT_COUNTRY_NULL'); ?>"
                                        name="address_country"
                                        value="<?php echo isset($this->company->address_country) ? $this->company->address_country : "" ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="Assignment">
            <?php if (UsersHelper::getRole() == 'exec'
            || UsersHelper::getRole() == "manager"
            || !($this->company->id > 0)
            || (array_key_exists('owner_id', $this->company)
                && UsersHelper::getUserId() == $this->company->owner_id)
            || UsersHelper::isAdmin()) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label" for="">
                            <?php echo ucwords(TextHelper::_('COBALT_COMPANY_OWNER')); ?>
                        </label>
                        <div class="controls">
                            <?php echo DropdownHelper::generateDropdown('owner', $ownerId, 'company'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label" for="company_description">
                            <?php echo TextHelper::_('COBALT_COMPANY_DESCRIPTION'); ?>
                        </label>
                        <div class="controls">
                            <textarea name="description" id="company_description" class="form-control"><?php echo isset($this->company->description) ? $this->company->description : "" ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="Details">
            <br />
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="controls">
                            <input  class="form-control"
                                    type="text"
                                    name="facebook_url"
                                    value="<?php echo isset($this->company->facebook_url) ? $this->company->facebook_url : "" ?>"
                                    placeholder="<?php echo TextHelper::_('COBALT_FACEBOOK_URL'); ?>" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="controls">
                            <input  class="form-control"
                                    data-minlength="4"
                                    type="text"
                                    name="twitter_user"
                                    value="<?php echo isset($this->company->twitter_user) ? $this->company->twitter_user : "" ?>"
                                    placeholder="<?php echo TextHelper::_('COBALT_TWITTER_USER'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="controls">
                            <input  class="form-control"
                                    type="text"
                                    name="flickr_url"
                                    value="<?php echo isset($this->company->flickr_url) ? $this->company->flickr_url : "" ?>"
                                    placeholder="<?php echo TextHelper::_('COBALT_FLICKR_URL'); ?>" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <div class="controls">
                            <input  class="form-control"
                                    type="text"
                                    name="youtube_url"
                                    value="<?php echo isset($this->company->youtube_url) ? $this->company->youtube_url : "" ?>"
                                    placeholder="<?php echo TextHelper::_('COBALT_YOUTUBE_URL'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade in" id="Custom">
            <?php if ($format != "raw")
            { ?>
                <?php echo $this->edit_custom_fields_view->display(); ?>
            <?php
            } ?>
        </div>

        <?php if (isset($this->company->id) && $this->company->id) { ?>
        <input class="form-control" type="hidden" name="id" value="<?php echo $company->id ?>" />
        <?php } ?>
        <input type="hidden" name="model" value="company" />
    </form>
</div>
<div class="modal-footer">
    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
        <?php echo ucwords(TextHelper::_('COBALT_CANCEL')); ?>
    </button>
    <button onclick="Cobalt.sumbitModalForm(this)" class="btn btn-primary">
        <?php echo ucwords(TextHelper::_('COBALT_SAVE')); ?>
    </button>
</div>