<?php
$edit = false;
$name = "add";
$string = "You can add a project here.";
if(isset($data['edit']) && $data['edit'] === true){
    $name = "edit";
    $string = "You can edit a project here";
    $edit = true;
}
//randomized number
$counterAutofill = rand();
//hasData, will be set to  true if data is assigned
$hD = false;
//$errors array met errors
$errors = array();
//array with previously filled in data
$oldValues = array();
if(isset($data['oldData'])){
    $oldValues = $data['oldData'];
    $counterAutofill = $oldValues['randomString'];
    $hD = true;
}

if(isset($data['errors'])){
    $errors = $data['errors'];
}
$stati = "";
if(isset($data['stati'])){
    foreach($data['stati'] AS $key => $val){
        $selected = "";
        if($hD && $oldValues['project_status'] == $key){$selected = "selected";}
        $stati .= "<option value=\"" . $key . "\" " . $selected . ">" . $val . "</option>";
    }
}

$persons = array();
if(isset($data['persons'])){
    $persons = $data['persons'];
}else{$persons="";}
if(isset($data['pName'])){
    $pName = $data['pName'];
}else{$pName="";}

if(isset($data['rName'])){
    $rName = $data['rName'];
}else{$rName="";}

if(isset($data['cName'])){
    $cName = $data['cName'];
}else{$cName="";}


if(isset($data['iName'])){
    $iName = $data['iName'];
}else{$iName="";}


$companies = array();
if(isset($data['companies'])){
    $companies = $data['companies'];
}
$settings = array();
if(isset($data['settings'])){
    $settings = $data['settings'];
}
$showProjectInfo = (isset($settings['projectDropdownContractor']) && $settings['projectDropdownContractor']) || 
        (isset($settings['projectDeadline']) && $settings['projectDeadline']) || 
        (isset($settings['projectHourlywage']) && $settings['projectHourlywage']) || 
        (isset($settings['projectSOhourly']) && $settings['projectSOhourly']) || 
        (isset($settings['projectSOdescription']) && $settings['projectSOdescription']) || 
        (isset($settings['projectCmcDropdown']) && $settings['projectCmcDropdown']) || 
        (isset($settings['projectInterDropdown']) && $settings['projectInterDropdown']) || 
        (isset($settings['projectMainResponDropdown']) && $settings['projectMainResponDropdown']);
$showProjectLinks = (isset($settings['projectDevLink']) && $settings['projectDevLink']) || 
        (isset($settings['projectAndroidLink']) && $settings['projectAndroidLink']) || 
        (isset($settings['projectiOSLink']) && $settings['projectiOSLink']) || 
        (isset($settings['projectSlug']) && $settings['projectSlug']) || 
        (isset($settings['projectResposiLink']) && $settings['projectResposiLink']) || 
        (isset($settings['projectDescription']) && $settings['projectDescription']);

$newlogo = "no_avatar.jpg";
if(isset($oldValues['image']) && file_exists("img/projects/".$oldValues['image']) && strlen($oldValues['image']) > 4){
    $newlogo = $oldValues['image'];
}

?>
 <a id="addButton" href="<?php echo SITE_ROOT . 'projects' . '/'; ?>" type='button' class='btn btn-primary'><i class="fas fa-chevron-left"></i> <?php echo _('Go back'); ?></a>
<div class="row">
    <div class="bs-callout bs-callout-primary col-lg-5">
      <h4><?php echo _($name . ' a project'); ?></h4>
      <?php echo _($string); ?>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php if(isset($oldValues['name'])){ echo _('Edit  ') . $oldValues['name'];}else{echo _('Add project');} ?></h3>
    </div>
    <div class="panel-body">
        <form autocomplete="off" method="post" enctype="multipart/form-data">
            <div class="row customRow">
                <div class="col-md-12 add_editPF" align="center">
                    <div class="pvI">
                        <p class="customerinformation">Customer information</p> 
                        <img id="previewImage" alt="Project logo" src="<?php echo SITE_ROOT;?>img/projects/<?php echo $newlogo;?>" class="img-responsive"> 
                        <input accept="image/*" type="file" id="file" name="file" class="<?php if(isset($errors['image']) && $errors['image']){echo 'has-error';} ?>">
                        <p class="p_PVI"><?php echo _("* Leave empty for no change."); ?></p>
                    </div>
                </div>
                <input type="hidden" id="id" name="id" value="<?php if(isset($oldValues['id'])){ echo $oldValues['id']; } ?>">
                <div class=" midDiv midDivProject col-md-12 col-lg-12">
                    <div class="control-group">
                        
                        <div class="name_project projects_name_input">
                            <div class="col-md-4 labelProject projects_name_input"><img  class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-solid.png" alt="name icon"><?php echo _('Name:'); ?></div>
                            <div class="form-group input-group col-sm-12 col-lg-6 projects_name_input">
                                <input id="nameProject" class="form-control <?php if($hD && isset($errors["name"]) && $errors["name"]){echo 'has-error';}?>" name="name" type="text" required placeholder="<?php echo _('Project name'); ?>" required <?php if(isset($oldValues['name'])){echo 'value="' . $oldValues['name'] . '"';}?>>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div id="projectBudget" class="budget_project projects_budget_input">
                            <div class="col-md-4 labelProject projects_budget_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/file-invoice-dollar-solid.png" alt="budget icon"><?php echo _('Budget:'); ?></div>
                            <div class="form-group input-group col-sm-12 col-lg-6 projects_budget_input">
                                <input id="budget" class="form-control <?php if($hD && isset($errors["budget"]) && $errors["budget"]){echo 'has-error';}?>" name="budget" type="text" placeholder="<?php echo _('Project budget'); ?>" <?php if(isset($oldValues['budget'])){echo 'value="' . $oldValues['budget'] . '"';}?>>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        
                        <div id="projectCustomer" class="customer_project projects_company_input">
                            <div class="col-md-4 labelProject projects_company_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="customer icon"><?php echo _('Customer:'); ?></div>
                            <div class="form-group input-group col-sm-12 col-lg-6 projects_company_input">
                                <input type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="company_id" id="company_id" data-id="companyPerson" value="<?php if(isset($oldValues['company_id'])){echo $oldValues['company_id'];}else{echo '-1';}?>"/>
                                <input id="customer" name="company_name-<?php echo $counterAutofill; ?>" placeholder="<?php echo _('Customer');?>" type="text" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp" data-callback="companyPerson" data-method="getCompany" data-target-id="company_id" data-control="api" autocomplete="off" <?php if(isset($oldValues['company_name-' . $counterAutofill])){echo 'value="' . $oldValues['company_name-' . $counterAutofill] . '"'; }?> />
                                <div class="input-group-addon" onClick="window.open('<?php echo SITE_ROOT . 'companies/addCompany/'; ?>')">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="companyPerson">
                                    <div class="info"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="projectStatusDropdown" class="status_project project_status_input">
                            <div class="col-md-4 labelProject project_status_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png" alt="Status icon"><?php echo _('Status:'); ?></div>
                            <div  class="form-group input-group col-sm-12 col-lg-6">
                                <select class="form-control selectpicker" id="project_status" name="project_status">
                                    <?php echo $stati; ?>
                                </select>
                            </div>
                        </div>
                        
                        <hr class="fieldSeparators" />
                        <div class="clearfix"></div>
                        <div class="<?php if(!$showProjectInfo){echo "hidden";} ?>">
                            <p class="projectinformation">Project information</p>
                            <div id="projectDropdownContractor" class="contracter_project projects_contractor_name_input">
                                <div class="col-md-4 labelProject projects_contractor_name_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="contractor icon"><?php echo _('Contractor:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_contractor_name_input">
                                    <select id="contractor" name="contractor_id" class="selectpicker form-control" data-live-search="true" data-container="body" data-size="8">Company
                                        <option value="<?php if(isset($oldValues['contractor_id'])){echo $oldValues['contractor_id'];} ?>">
                                                       <?php if(isset($oldValues['contractor_id'])){echo $cName;} ?></option>
                                        <option data-divider="true"></option>
                                        <option value="-1">No one</option>
                                         <?php foreach ($companies as $company) {?>
                                            <option value="<?php echo $company->getId(); ?>"><?php echo $company->getName();?></option>
                                        <?php }?>
                                    </select>

                                <div class="input-group-addon" onClick="window.open('<?php echo SITE_ROOT . 'companies/addCompany/'; ?>')">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="contractor_id">
                                        <div class="info"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectDeadline" class="deadline_project projects_deadline_input">
                                <div class="col-md-4 labelProject projects_deadline_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/calendar-day-solid.png" alt="deadline icon"><?php echo _('Deadline:'); ?></div>
                                <div class="form-group input-group col-sm- col-lg-6 projects_deadline_input">
                                    <input id="deadline" data-date-format="dd-mm-yyyy" class="form-control <?php if($hD && isset($errors["deadline"]) && $errors["deadline"]){echo 'has-error';}?>" name="deadline" type="text" placeholder="<?php echo _('dd/mm/yyyy'); ?>" <?php if(isset($oldValues['deadline'])){echo 'value="' . $oldValues['deadline'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectHourlywage" class="hourlyWage_project projects_hourly_wage_input_input">
                                <div class="col-md-4 labelProject projects_hourly_wage_input_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/file-invoice-dollar-solid.png" alt="wage icon"><?php echo _('Hourly wage:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_hourly_wage_input">
                                    <input id="hourly_wage" class="form-control <?php if($hD && isset($errors["hourly_wage"]) && $errors["hourly_wage"]){echo 'has-error';}?>" name="hourly_wage" type="text" placeholder="<?php echo _('Price per hour'); ?>" <?php if(isset($oldValues['hourly_wage'])){ echo 'value="' . $oldValues['hourly_wage'] . '"';}else{ echo 'value="' . DEFAULT_WAGE . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectSOhourly" class="soHourly_project projects_SO_hourly_input">
                                <div class="col-md-4 labelProject projects_budget_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/clock-solid.png" alt="s&o hourly icon"><?php echo _('S&O Hourly:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_SO-hourly_input">
                                    <input id="SO_hourly" class="form-control <?php if($hD && isset($errors["SO_hourly"]) && $errors["SO_hourly"]){echo 'has-error';}?>" name="SO_hourly" type="text" placeholder="<?php echo _('SO hourly Input'); ?>" <?php if(isset($oldValues['SO_hourly'])){ echo 'value="' . $oldValues['SO_hourly'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectSOdescription" class="soDescription_project projects_SO_description_input">
                                <div class="col-md-4 labelProject projects_SO_description_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png" alt="s&o description icon"><?php echo _('S&O Description:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_SO-description-input">
                                    <textarea id="SO_description" class="form-control <?php if($hD && isset($errors["SO_description"]) && $errors["SO_description"]){echo 'has-error';}?>" name="SO_description" type="text" placeholder="<?php echo _('SO description'); ?>"><?php if(isset($oldValues['SO_description'])){ echo $oldValues['SO_description'];}?></textarea>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectCmcDropdown" class="customerMainContact_project projects_person_main_contact_name_input">
                                <div class="col-md-4 labelProject projects_person_main_contact_name_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="customer main contact icon"><?php echo _('Customer Main Contact:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_person_main_contact_name_input">
                                    <select id="customerMC" name="person_main_contact_id" class="selectpicker form-control" data-live-search="true" data-container="body" data-size="8">Contact person
                                        <option value="<?php if(isset($oldValues['person_main_contact_id'])){echo $oldValues['person_main_contact_id'];} ?>">
                                                       <?php if(isset($oldValues['person_main_contact_id'])){echo $pName;} ?></option>
                                        <option data-divider="true"></option>
                                        <option value="-1">No one</option>
                                         <?php foreach ($persons as $person) {?>
                                            <option value="<?php echo $person->getId(); ?>"><?php echo $person->getName();?></option>
                                        <?php }?>
                                    </select>
                                    <div class="input-group-addon" onClick="window.open('<?php echo SITE_ROOT . 'person/add/'; ?>')">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="person_main_contact_id">
                                        <div class="info"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectCrmcDropdown" class="contractorMainContact_project projects_contractor_main_contact_name_input">
                                <div class="col-md-4 labelProject projects_contractor_main_contact_name_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="contractor main contact icon"><?php echo _('Contractor Main Contact:'); ?></div>
                                <div  class="form-group input-group col-sm-12 col-lg-6 projects_contractor_main_contact_name_input">
                                    <input  type="number" class="form-control bedrijfpersoonpickerLazy hidden" name="contractor_main_contact_id" id="contractor_main_contact_id" data-id="contractor_main_contact_id" value="<?php if(isset($oldValues['contractor_main_contact_id'])){echo $oldValues['contractor_main_contact_id'];}else{echo '-1';}?>"/>
                                    <input id="contractorMC" name="contractor_main_contact_name-<?php echo $counterAutofill; ?>" type="text" class="form-control bedrijfpersoonpickerLazy searchOnKeyUp" data-callback="contractor_main_contact_id" data-method="getContactPerson" data-target-id="contractor_main_contact_id" data-control="api" <?php if(isset($oldValues['contractor_main_contact_name-' . $counterAutofill])){echo 'value="' . $oldValues['contractor_main_contact_name-' . $counterAutofill] . '"';}?> autocomplete="new-password"/>
                                    <div class="input-group-addon" onClick="window.open('<?php echo SITE_ROOT . 'person/add/'; ?>')">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="contractor_main_contact_id">
                                        <div class="info"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectInterDropdown" class="intermediate_project projects_Intermediate_name_input">
                                <div class="col-md-4 labelProject projects_Intermediate_name_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="intermediate icon"><?php echo _('Intermediate:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_contractor_name_input">
                                    <select id="intermediate" name="intermediate_id" class="selectpicker form-control" data-live-search="true" data-container="body" data-size="8">Company
                                        <option value="<?php if(isset($oldValues['intermediate_id'])){echo $oldValues['intermediate_id'];} ?>">
                                                       <?php if(isset($oldValues['intermediate_id'])){echo $iName;} ?></option>
                                        <option data-divider="true"></option>
                                        <option value="-1">No one</option>
                                         <?php foreach ($companies as $company) {?>
                                            <option value="<?php echo $company->getId(); ?>"><?php echo $company->getName();?></option>
                                        <?php }?>
                                    </select>
                                <div class="input-group-addon" onClick="window.open('<?php echo SITE_ROOT . 'companies/addCompany/'; ?>')">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                <div class="seng_standard_dropdown seng_dropdown_searchOnKeyUp" data-for="contractor_id">
                                        <div class="info"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectMainResponDropdown" class="responsible_project projects_responsible_name_input">
                                <div class="col-md-4 labelProject projects_responsible_name_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/user-tag-solid.png" alt="main resposiblity icon"><?php echo _('Main resposiblity of:'); ?></div>
                                <div  class="form-group input-group col-sm-12 col-lg-6 projects_responsible_name_input">
                                    <select id="responsible" name="responsible" class="selectpicker form-control" data-live-search="true" data-container="body" data-size="8">Contact person
                                        <option value="<?php if(isset($oldValues['responsible'])){echo $oldValues['responsible'];} ?>">
                                                       <?php if(isset($oldValues['responsible'])){echo $rName;} ?></option>
                                        <option data-divider="true"></option>
                                        <option value="-1">No one</option>
                                        <?php foreach ($persons as $person) {?>
                                            <option value="<?php echo $person->getId(); ?>"><?php echo $person->getName();?></option>
                                        <?php }?>
                                    </select> 
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            

                            <hr class="fieldSeparators" />
                            <div class="clearfix"></div>
                        </div>
                        <div class="<?php if(!$showProjectLinks){echo "hidden";} ?>">
                            <p class="projectinformation">Project links</p>
                            <div id="projectDevLink" class="testpage_project projects_dev_link_input">
                                <div class="col-md-4 labelProject projects_dev_link_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid.png" alt="an icon for the testpage input field"><?php echo _('Test page:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_dev_link_input">
                                    <input id="dev_link" class="form-control <?php if($hD && isset($errors["dev_link"]) && $errors["dev_link"]){echo 'has-error';}?>" name="dev_link" type="text" placeholder="<?php echo _('www.developementlink.com'); ?>" <?php if(isset($oldValues['dev_link'])){echo 'value="' . $oldValues['dev_link'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectAndroidLink" class="androidLink_project projects_android_link_input">
                                <div class="col-md-4 labelProject projects_android_link_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid.png" alt="android link icon"><?php echo _('Android link:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_android_link_input">
                                    <input id="android_link" class="form-control <?php if($hD && isset($errors["android_link"]) && $errors["android_link"]){echo 'has-error';}?>" name="android_link" type="text" placeholder="<?php echo _('www.android.com/link'); ?>" <?php if(isset($oldValues['android_link'])){echo 'value="' . $oldValues['android_link'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectiOSLink" class="iosLink_project projects_ios_link_input">
                                <div class="col-md-4 labelProject projects_ios_link_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid.png" alt="iOS link icon"><?php echo _('iOS link:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_ios_link_input">
                                    <input id="ios_link" class="form-control <?php if($hD && isset($errors["ios_link"]) && $errors["ios_link"]){echo 'has-error';}?>" name="ios_link" type="text" placeholder="<?php echo _('www.apple.com/link'); ?>" <?php if(isset($oldValues['ios_link'])){echo 'value="' . $oldValues['ios_link'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectResposiLink" class="repositoryLink_project projects_repository_input">
                                <div class="col-md-4 labelProject projects_repository_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/globe-europe-solid.png"  alt="respository link icon"><?php echo _('Respository link:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_repository_input">
                                    <input id="repository" class="form-control <?php if($hD && isset($errors["repository"]) && $errors["repository"]){echo 'has-error';}?>" name="repository" type="text" placeholder="<?php echo _('www.bitbucket.com/link'); ?>" <?php if(isset($oldValues['repository'])){echo 'value="' . $oldValues['repository'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectSlug" class="slug_project projects_slug_input">
                                <div class="col-md-4 labelProject projects_slug_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png"  alt="slug icon"><?php echo _('Slug:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_slug_input">
                                    <input id="slug" class="form-control <?php if($hD && isset($errors["slug"]) && $errors["slug"]){echo 'has-error';}?>" name="slug" type="text" placeholder="<?php echo _('n/a'); ?>" <?php if(isset($oldValues['slug'])){echo 'value="' . $oldValues['slug'] . '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div id="projectDescription" class="description_project projects_description_input">
                                <div class="col-md-4 labelProject projects_description_input"><img class="labelIcon" src="<?php echo SITE_ROOT;?>img/icons/pen-solid.png"  alt="description icon"><?php echo _('Description:'); ?></div>
                                <div class="form-group input-group col-sm-12 col-lg-6 projects_description_input">
                                    <textarea id="description" class="form-control tinymceinput <?php if($hD && isset($errors["description"]) && $errors["description"]){echo 'has-error';}?>" name="description"> <?php if(isset($oldValues['description'])){echo $oldValues['description'];}?> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="hidden projectsHiddenNum">
                            <input type="numer" name="randomString" value="<?php echo $counterAutofill; ?>"/>
                        </div>
                    </div>
                <hr class="fieldSeparators" />
                <div class="clearfix"></div>
                </div>
                <div class="button-row">
                    <button name="add" type="submit" class="custom_btn btn btn-primary"><i class="fa fa-floppy-o"></i> <?php if($edit){echo _('SAVE');}else{echo _('CREATE');} ?></button> 
                </div>
            </div>
        </form>
    </div>
</div>