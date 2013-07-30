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

<div class="container-fluid">
    <?php echo $this->menu['quick_menu']->render(); ?>
    <div class="row-fluid">
        <div class="span12" id="content">
            <div id="system-message-container"></div>
            <div class="row-fluid">
                <?php echo $this->menu['menu']->render(); ?>
                <div class="span9">
                    <ul class="nav nav-tabs" id="myTab">
                        <li class="active"><a data-toggle="tab" href="#import_begin"><?php echo JText::_('COBALT_IMPORT_BEGIN'); ?></a></li>
                        <li><a data-toggle="tab" href="#import_review"><?php echo JText::_('COBALT_IMPORT_REVIEW'); ?></a></li>
                        <li><a data-toggle="tab" href="#import_sample_tab"><?php echo JText::_('COBALT_IMPORT_SAMPLE_DATA'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="import_begin">
                            <div class="step">
                                <div class="title">
                                    <h2><?php echo JText::_('COBALT_STEP_ONE'); ?></h2>
                                </div>
                                <div class="text">
                                    <ul>
                                        <li><?php echo JText::_('COBALT_EXPORT_YOUR_FILE_INSTRUCTIONS'); ?></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="step">
                                <div class="title">
                                    <h2><?php echo JText::_('COBALT_STEP_TWO'); ?></h2>
                                </div>
                                <div class="text">
                                    <ul>
                                        <li><?php echo JText::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED'); ?></li>
                                        <li><?php echo JText::_('COBALT_ENSURE_YOUR_FILE_IS_FORMATTED_INSTRUCTIONS'); ?></li>
                                        <li>
                                            <form class="inline-form" method="post">
                                                <input class="btn" onclick="downloadImportTemplate(this)" type="button" value="<?php echo JText::_('COBALT_DOWNLOAD_COMPANIES_TEMPLATE'); ?>" />
                                                <input type="hidden" name="template_type" value="companies" />
                                            </form>
                                        </li>
                                        <li>
                                            <form class="inline-form" method="post">
                                                <input class="btn" onclick="downloadImportTemplate(this)" type="button" value="<?php echo JText::_('COBALT_DOWNLOAD_DEALS_TEMPLATE'); ?>" />
                                                <input type="hidden" name="template_type" value="deals" />
                                            </form>
                                        </li>
                                        <li>
                                            <form class="inline-form" method="post">
                                                <input class="btn" onclick="downloadImportTemplate(this)" type="button" value="<?php echo JText::_('COBALT_DOWNLOAD_PEOPLE_TEMPLATE'); ?>" />
                                                <input type="hidden" name="template_type" value="people" />
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="step">
                                <div class="title">
                                    <h2><?php echo JText::_('COBALT_STEP_THREE'); ?></h2>
                                </div>
                                <div class="text">
                                    <ul>
                                        <li><?php echo JText::_('COBALT_UPLOAD_YOUR_FILE'); ?></li>
                                        <li><?php echo JText::_('COBALT_SELECT_YOUR_CSV'); ?>
                                            <form id="import_form" action="index.php?view=import" method="post" enctype="multipart/form-data">
                                                <div class="input_upload_button" >
                                                    <label><?php echo JText::_('COBALT_TYPE'); ?></label>
                                                    <?php echo CobaltHelperDropdown::showImportTypes(); ?>
                                                    <input type="hidden" name="type" value="people" />
                                                    <label><?php echo JText::_('COBALT_FILE'); ?></label>
                                                    <input class="input-file" type="file" name="document" />
                                                    <label></label>
                                                    <input class="btn btn-primary" type="submit" value="<?php echo JText::_('COBALT_IMPORT_DATA'); ?>" />
                                                </div>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                         <div class="tab-pane" id="import_review" >
                             <div id="import_review_container">
                                 <?php if ($this->import_post==TRUE) { ?>
                                    <h1><?php echo JText::_('COBALT_REVIEW_YOUR_IMPORT'); ?></h1>
                                <?php if ( isset($this->import_data) && count($this->import_data) > 0 ) { ?>
                                    <p><?php echo JText::_('COBALT_REVIEW_IMPORT_MESSAGE'); ?></p>
                                <?php } else { ?>
                                    <p><div class="alert alert-error"><?php echo JText::_('COBALT_REVIEW_IMPORT_MESSAGE_ERROR'); ?></div></p>
                                <?php } ?>
                                <?php if ( isset($this->import_data) && count($this->import_data) > 1 ) { ?>
                                    <div id="import_seek">
                                        <span>
                                            <?php echo JText::_('COBALT_VIEWING_ENTRY'); ?>
                                            <span id="viewing_entry">1</span>
                                            <?php echo JText::_('COBALT_OF'); ?>
                                            <?php echo ' '.count($this->import_data); ?>
                                            <a href="javascript:void(0)" onclick="seekImport(-1);"><?php echo JText::_('COBALT_PREVIOUS'); ?></a> -
                                            <a href="javascript:void(0)" onclick="seekImport(1);"><?php echo JText::_('COBALT_NEXT'); ?></a>
                                        </span>
                                    </div>
                                <?php } ?>
                                <form action="index.php?controller=import" method="post">
                                        <div id="editForm">
                                        <?php if ( isset($this->import_data) && count($this->import_data) > 0 ) { foreach ($this->import_data as $key => $data) { ?>
                                        <?php if ($key > 0) { $style = "style='display:none;'"; } else { $style = ""; } ?>
                                        <div <?php echo $style; ?> id="import_entry_<?php echo $key; ?>">
                                            <?php foreach ($data as $field => $value) { ?>
                                                <?php $header = ( $array_key = array_search($field,$this->headers) ) ? $this->headers[$array_key] : $field; ?>
                                                <div class="cobaltRow">
                                                <div class="cobaltField"><?php echo ucwords(str_replace('_',' ',str_replace('id','Name',$header))); ?></div>
                                                    <?php if (is_array($value)) { ?>
                                                    <?php if ( array_key_exists('dropdown',$value) ) { ?>
                                                        <div class="cobaltValue wide"><?php echo $value['dropdown']; ?></div>
                                                    <?php } elseif ( array_key_exists('value',$value)) { ?>
                                                        <div class="cobaltValue wide">
                                                                <input type="hidden" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value['value']; ?>" >
                                                                <input type="text" class="inputbox" name="import_id[<?php echo $key; ?>][<?php echo str_replace('id','name',$field); ?>]" value="<?php echo $value['label']; ?>" /></div>
                                                    <?php } else { ?>
                                                        <div class="cobaltValue wide"><?php echo JText::_('COBALT_NO_RESULTS_FOUND'); ?></div>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <div class="cobaltValue wide"><input class="inputbox" type="text" name="import_id[<?php echo $key; ?>][<?php echo $field; ?>]" value="<?php echo $value; ?>" /></div>
                                                <?php } ?>
                                                </div>
                                        <?php } ?>
                                        </div>
                                        <?php }  ?>
                                        <div class="import_buttons">
                                            <input class="btn btn-primary" type="submit" value="<?php echo JText::_('COBALT_IMPORT'); ?>" /><a class="btn" onclick="window.location.href='index.php?view=import'"><?php echo JText::_('COBALT_CANCEL'); ?></a>
                                        </div>
                                        <?php } ?>
                                        </div>
                                    <input type="hidden" name="import_type" value="<?php echo $this->import_type; ?>" />
                                </form>
                                <?php } else { ?>
                                    <h1><?php echo JText::_('COBALT_NO_DATA_IMPORTED'); ?></h1>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tab-pane" id="import_sample_tab">
                            <div class="sample_text">
                                <h1><?php echo JText::_('COBALT_INSTALL_SAMPLE_DATA_TITLE'); ?></h1>
                                <p><div class="alert alert-info"><?php echo JText::_('COBALT_INSTALL_SAMPLE_DATA_DESC'); ?></div></p>
                                <form action="<?php echo JRoute::_('index.php?view=import'); ?>" method="post" name="adminForm" id="adminForm" class="inline-form"  >
                                    <input type="submit" value="<?php echo JText::_('COBALT_INSTALL_SAMPLE_BUTTON'); ?>" class="btn btn-primary" />
                                    <input type="hidden" name="id" value="1" />
                                    <input type="hidden" name="task" value="installSampleData" />
                                    <input type="hidden" name="controller" value="import" />
                                    <input type="hidden" name="layout" value="default" />
                                    <input type="hidden" name="view" value="import" />
                                    <?php echo JHtml::_('form.token'); ?>
                                </form>
                                <form action="<?php echo JRoute::_('index.php?view=import'); ?>" method="post" name="adminForm" id="adminForm" class="inline-form"  >
                                    <input type="submit" value="<?php echo JText::_('COBALT_REMOVE_SAMPLE_BUTTON'); ?>" class="btn btn-danger" />
                                    <input type="hidden" name="id" value="1" />
                                    <input type="hidden" name="task" value="removeSampleData" />
                                    <input type="hidden" name="controller" value="import" />
                                    <input type="hidden" name="layout" value="default" />
                                    <input type="hidden" name="view" value="import" />
                                    <?php echo JHtml::_('form.token'); ?>
                                </form>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->menu['quick_menu']->render(); ?>
</div>
