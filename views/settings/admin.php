<?php
    $errors = array();
    if(isset($data['errors'])){
        $errors = $data['errors'];
    }
?>
<div class="row">
<div class="bsSettings bs-callout bs-callout-info col-lg-8">
    <h4><?php echo _('Admin settings'); ?></h4>
    <p><?php echo _('Here you can set all important settings, from emailing to logging. DON\'T FORGET TO SAVE!'); ?></p>
</div>
<div class="bsSettings bs-callout bs-callout-success col-lg-8">
    <h4><?php echo _('Change the visible inputs.'); ?></h4>
    <p><?php echo _('You can change which inputs you can see.'); ?></p>
    <div class="visibility-button-holder-admin">
        <a href="<?php echo SITE_ROOT . 'companies' . '/' . 'setVisibility' . '/'; ?>" type='button' class='btn btn-primary visibility-button-admin'><?php echo _('Set visibility company'); ?></a>
        <a href="<?php echo SITE_ROOT . 'person' . '/' . 'setVisibility' . '/'; ?>" type='button' class='btn btn-primary visibility-button-admin'> <?php echo _('Set visibility person'); ?></a>
        <a href="<?php echo SITE_ROOT . 'projects' . '/' . 'setVisibility' . '/'; ?>" type='button' class='btn btn-primary visibility-button-admin'><?php echo _('Set visibility projects'); ?></a>
    </div>
</div>
    </div>
<div class="col-sm-12">
    <div class="panel panel-info">
        <div class="panel-body">
            <div class="row">
                <div class="general">
                    <h4 class="customerinformation">General</h4>
                </div>
                <form id="userSettingsForm" action="<?php echo SITE_ROOT , 'settings/admin/'; ?>" enctype="multipart/form-data" method="POST">
                    <br>
                    <div>
                        <label class="control-label col-form-label col-sm-12 col-lg-3" for="title_website"><?php echo _('Title of the website:'); ?></label>
                        <div class="form-group input-group col-sm-12 col-lg-4">
                            <input id="title_website" class="form-control <?php if(isset($errors["title_website"]) && $errors["title_website"]){echo 'has-error';}?>" name="title_website" type="text" required <?php if(SITE_TITLE){echo 'value="' , SITE_TITLE , '"';}?>>
                        </div>
                    </div>
                    <div class="checkboxSettings">
                        <label>
                            <input id="force_https" name="force_https" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_HTTPS){echo 'checked';} ?>>
                            <span onclick="$('#force_https').bootstrapToggle('toggle')"><?php echo _('Force all to https(Recommended)');?><i class="fa fa-exclamation-triangle" aria-hidden="true" data-toggle="tooltip" title="" data-original-title="<?php echo _('If https is not enabled on this site, this will not work.!'); ?>"></i></span>
                        </label>
                    </div>
                    <div class="checkboxSettings">
                        <label>
                            <input class="has-extra" id="send_mail" name="send_mail" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(SEND_MAIL){echo 'checked';} ?>>
                            <span onclick="$('#send_mail').bootstrapToggle('toggle')"><?php echo _('Set mails.');?></span>
                        </label>
                    </div>
                    <div class='extra-values-box <?php if(SEND_MAIL){echo "show-box";} ?>' data-for='send_mail'>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="from_name"><?php echo _('Emails sent under the name:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="from_name" class="form-control willberequired <?php if(isset($errors["from_name"]) && $errors["from_name"]){echo 'has-error';}?>" name="from_name" type="text" placeholder="<?php echo _('Van Stein en Groentjes B.V.'); ?>" <?php if(SEND_MAIL){echo "required";} ?> <?php if(EMAIL_FROM_NAME){echo 'value="' , EMAIL_FROM_NAME , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="email_real"><?php echo _('Emails really from:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="email_real" class="form-control willberequired <?php if(isset($errors["email_real"]) && $errors["email_real"]){echo 'has-error';}?>" name="email_real" type="email" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(SEND_MAIL){echo "required";} ?> <?php if(EMAIL_FROM_REAL){echo 'value="' , EMAIL_FROM_REAL , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="from_noreply"><?php echo _('Email address for noreply emails:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="from_noreply" class="form-control willberequired <?php if(isset($errors["from_noreply"]) && $errors["from_noreply"]){echo 'has-error';}?>" name="from_noreply" type="email" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(SEND_MAIL){echo "required";} ?> <?php if(EMAIL_FROM_NOREPLY){echo 'value="' , EMAIL_FROM_NOREPLY , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="email_reply"><?php echo _('Set reply to to:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="email_reply" class="form-control willberequired <?php if(isset($errors["email_reply"]) && $errors["email_reply"]){echo 'has-error';}?>" name="email_reply" type="email" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(SEND_MAIL){echo "required";} ?> <?php if(EMAIL_REPLY_TO){echo 'value="' , EMAIL_REPLY_TO , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="emails_dangers"><?php echo _('Send important warnings to:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="emails_dangers" class="form-control willberequired <?php if(isset($errors["emails_dangers"]) && $errors["emails_dangers"]){echo 'has-error';}?>" name="emails_dangers" type="email" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(SEND_MAIL){echo "required";} ?> <?php if(EMAIL_DANGERS_TO){echo 'value="' , EMAIL_DANGERS_TO , '"';}?>>
                            </div>
                        </div>
                    </div>


                    <div class="checkboxSettings">
                        <label>
                            <input class="has-extra" id="use_smtp" name="use_smtp" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_SMPT_EMAIL){echo 'checked';} ?>>
                            <span onclick="$('#use_smtp').bootstrapToggle('toggle')"><?php echo _('add mail account for sending.');?></span>
                        </label>
                    </div>
                    <div class='extra-values-box <?php if(USE_SMPT_EMAIL){echo "show-box";} ?>' data-for='use_smtp'>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="smtp_host"><?php echo _('SMTP host:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="smtp_host" class="form-control willberequired <?php if(isset($errors["smtp_host"]) && $errors["smtp_host"]){echo 'has-error';}?>" name="smtp_host" type="text" placeholder="<?php echo _('website.domain'); ?>" <?php if(USE_SMPT_EMAIL){echo "required";} ?> <?php if(SMTP_HOST){echo 'value="' , SMTP_HOST , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <div class="checkboxSettings">
                                <div onclick="$('#smtp_auth').bootstrapToggle('toggle')" class="smtpAuthText control-label col-form-label col-sm-4 col-lg-3"><label><?php echo _('use SMTP authenticate');?></label></div>
                                <label class="smtpAuth">
                                    <input id="smtp_auth" name="smtp_auth" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(SMTP_AUTH){echo 'checked';} ?>>
                                </label>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-4 col-lg-3" for="smtp_username"><?php echo _('SMTP username:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="smtp_username" class="form-control willberequired <?php if(isset($errors["smtp_username"]) && $errors["smtp_username"]){echo 'has-error';}?>" name="smtp_username" type="text" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(USE_SMPT_EMAIL){echo "required";} ?> <?php if(SMTP_USERNAME){echo 'value="' , SMTP_USERNAME , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="smtp_password"><?php echo _('SMTP password:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="smtp_password" class="form-control willberequired <?php if(isset($errors["smtp_password"]) && $errors["smtp_password"]){echo 'has-error';}?>" name="smtp_password" type="password" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(USE_SMPT_EMAIL){echo "required";} ?> <?php if(SMTP_PASSWORD){echo 'value="' , SMTP_PASSWORD , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="smtp_security"><?php echo _('SMTP encryption:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <select id="smtp_security" name="smtp_security" class= "form-control">
                                    <option value="false" <?php if(SMTP_SECURE === false){echo 'selected';}?>><?php echo _('None'); ?></option>
                                    <option value="tls" <?php if(SMTP_SECURE === 'tls'){echo 'selected';}?>><?php echo _('tls'); ?></option>
                                    <option value="ssl" <?php if(SMTP_SECURE === 'ssl'){echo 'selected';}?>><?php echo _('ssl'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="SMTP_PORT"><?php echo _('Use port:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="smtp_port" class="form-control willberequired <?php if(isset($errors["smtp_port"]) && $errors["smtp_port"]){echo 'has-error';}?>" name="smtp_port" type="text" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(USE_SMPT_EMAIL){echo "required";} ?> <?php if(SMTP_PORT){echo 'value="' , SMTP_PORT , '"';}?>>
                            </div>
                        </div>
                    </div>
                    <!--
                    <div class="checkbox">
                        <label>
                            <input id="activate_account" name="activate_account" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(ACTIVATE_ACCOUNTS){echo 'checked';} ?>>
                            <span onclick="$('#activate_account').bootstrapToggle('toggle')"><?php echo _('Activate accounts before able to use it.');?></span>
                        </label>
                    </div>
                    -->
                    <div class="checkboxSettings">
                        <label>
                            <input class="has-extra" id="use_log" name="use_log" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_LOG){echo 'checked';} ?>>
                            <span onclick="$('#use_log').bootstrapToggle('toggle')"><?php echo _('Log events.');?></span>
                        </label>
                    </div>
                    <div class='extra-values-box extra-large <?php if(USE_LOG){echo "show-box";} ?>' data-for='use_log'>
                        <div class="checkboxSettings">
                        <div onclick="$('#use_log_ip').bootstrapToggle('toggle')" class="smtpAuthText control-label col-form-label col-sm-4 col-lg-3"><label><?php echo _('Log ip.');?><i class="fa fa-info-circle warning" aria-hidden="true" data-toggle="tooltip" title="" data-original-title="<?php echo _('This function is usefull, but not always allowed. Check the applicable laws in your region.'); ?>"></i></label></div>
                            <label class="logIp">
                                <input id="use_log_ip" name="use_log_ip" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_IP){echo 'checked';} ?>>
                            </label>
                        </div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="log_website_name"><?php echo _('Name website in log:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="log_website_name" class="form-control willberequired <?php if(isset($errors["log_website_name"]) && $errors["log_website_name"]){echo 'has-error';}?>" name="log_website_name" type="text" placeholder="<?php echo _('Name website for in log'); ?>" <?php if(USE_LOG){echo "required";} ?> <?php if(WEBSITE_NAME){echo 'value="' , WEBSITE_NAME , '"';}?>>
                            </div>
                        </div>
                        <div>
                            <label class="control-label col-form-label col-sm-12 col-lg-3" for="log_level"><?php echo _('Save to log:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <select id="log_level" name="log_level" class="form-control">
                                    <option value="1" <?php if(DB_LOG_LEVEL === 1){echo 'selected';}?>><?php echo _('Everything'); ?></option>
                                    <option value="2" <?php if(DB_LOG_LEVEL === 2){echo 'selected';}?>><?php echo _('Warning and errors'); ?></option>
                                    <option value="3" <?php if(DB_LOG_LEVEL === 3){echo 'selected';}?>><?php echo _('Only errors'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="checkboxSettings">
                            <div onclick="$('#use_log_ext').bootstrapToggle('toggle')" class="smtpAuthText control-label col-form-label col-sm-4 col-lg-3"><label><?php echo _('Use external log.');?></label></div>
                            <label class="userExternalLog">
                                <input class="has-extra" id="use_log_ext" name="use_log_ext" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_EXTERNAL_LOG){echo 'checked';} ?>>
                            </label>
                        </div>
                        <div class='extra-values-box helo <?php if(USE_EXTERNAL_LOG){echo "show-box";} ?>' data-for='use_log_ext'>
                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_host"><?php echo _('Host:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_host" class="form-control willberequired <?php if(isset($errors["db_log_host"]) && $errors["db_log_host"]){echo 'has-error';}?>" name="db_log_host" type="text" placeholder="<?php echo _('website@hosting.domain'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_HOST){echo 'value="' , DB_LOG_HOST , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_port"><?php echo _('Port:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_port" class="form-control willberequired <?php if(isset($errors["db_log_port"]) && $errors["db_log_port"]){echo 'has-error';}?>" name="db_log_port" type="text" placeholder="<?php echo _('3306'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_PORT){echo 'value="' , DB_LOG_PORT , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_name"><?php echo _('Database name:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_name" class="form-control willberequired <?php if(isset($errors["db_log_name"]) && $errors["db_log_name"]){echo 'has-error';}?>" name="db_log_name" type="text" placeholder="<?php echo _('name'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_NAME){echo 'value="' , DB_LOG_NAME , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_user"><?php echo _('Username:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_user" class="form-control willberequired <?php if(isset($errors["db_log_user"]) && $errors["db_log_user"]){echo 'has-error';}?>" name="db_log_user" type="text" placeholder="<?php echo _('username'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_USER){echo 'value="' , DB_LOG_USER , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_password"><?php echo _('Password:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_password" class="form-control willberequired <?php if(isset($errors["db_log_password"]) && $errors["db_log_password"]){echo 'has-error';}?>" name="db_log_password" type="password" placeholder="<?php echo _('password'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_PASSWORD){echo 'value="' , DB_LOG_PASSWORD , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div>
                                <label class="control-label col-form-label col-sm-12 col-lg-3" for="db_log_table_name"><?php echo _('Table name:'); ?></label>
                                <div class="form-group input-group col-sm-12 col-lg-9">
                                    <input id="db_log_table_name" class="form-control willberequired <?php if(isset($errors["db_log_table_name"]) && $errors["db_log_table_name"]){echo 'has-error';}?>" name="db_log_table_name" type="text" placeholder="<?php echo _('log'); ?>" <?php if(USE_EXTERNAL_LOG){echo "required";} ?> <?php if(DB_LOG_TABLE_NAME){echo 'value="' , DB_LOG_TABLE_NAME , '"';}?>>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                        </div>
                    </div>

                    <div class="checkboxSettings">
                        <label>
                            <input id="use_cookie" name="use_cookie" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_COOKIE){echo 'checked';} ?>>
                            <span onclick="$('#use_cookie').bootstrapToggle('toggle')"><?php echo _('use cookies.');?><i class="fa fa-info-circle info" aria-hidden="true" data-toggle="tooltip" title="" data-original-title="<?php echo _('The cookie is used as a backup for the login system. We store the username and sessioncode, but it might be'); ?>"></i></span>
                        </label>
                    </div>

                    <div class="checkboxSettings">
                        <label>
                            <input class="has-extra" id="use_secure_login" name="use_secure_login" type="checkbox" data-width="75px" data-height="36px" data-toggle="toggle" <?php if(USE_SECUREIMAGE_LOGIN){echo 'checked';} ?>>
                            <span onclick="$('#use_secure_login').bootstrapToggle('toggle')"><?php echo _('Use captcha for login.');?></span>
                        </label>
                    </div>
                    <div class='extra-values-box <?php if(USE_SECUREIMAGE_LOGIN){echo "show-box";} ?>' data-for='use_secure_login'>
                        <div>
                            <label class="control-label col-form-label col-sm-4 col-lg-3" for="login_captcha_secret"><?php echo _('Captcha secret code:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="login_captcha_secret" class="form-control willberequired <?php if(isset($errors["login_captcha_secret"]) && $errors["login_captcha_secret"]){echo 'has-error';}?>" name="login_captcha_secret" type="text" placeholder="<?php echo _('captcha secret'); ?>" <?php if(USE_SECUREIMAGE_LOGIN){echo "required";} ?> <?php if(CAPTCHA_SECRET){echo 'value="' , CAPTCHA_SECRET , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div>
                            <label class="control-label col-form-label col-sm-4 col-lg-3" for="login_captcha_public"><?php echo _('Captcha public code:'); ?></label>
                            <div class="form-group input-group col-sm-12 col-lg-9">
                                <input id="login_captcha_public" class="form-control willberequired <?php if(isset($errors["login_captcha_public"]) && $errors["login_captcha_public"]){echo 'has-error';}?>" name="login_captcha_public" type="text" placeholder="<?php echo _('captcha public'); ?>" <?php if(USE_SECUREIMAGE_LOGIN){echo "required";} ?> <?php if(CAPTCHA_PUBLIC){echo 'value="' , CAPTCHA_PUBLIC , '"';}?>>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="refreshCaptchaToken" onclick="javascript:changePublicKey();">
                            <span> <?php echo _('Click here after changing the captcha public key. After that, please verify you are not a robot with the captcha box.'); ?></span>
                        </div>
                        <div id="recaptcha-holder-holder">
                            <div id="g-recaptcha-target" data-sitekey="<?php echo CAPTCHA_PUBLIC; ?>"></div>
                        </div>
                        <script src='https://www.google.com/recaptcha/api.js'></script>
                    </div>
                    <br>
                    <div>
                        <label class="control-label col-form-label col-sm-4 col-lg-3" for="default_wage"><?php echo _('Default hourly wage:'); ?></label>
                        <div class="form-group input-group col-sm-12 col-lg-4">
                            <input id="default-wage" class="form-control <?php if(isset($errors["default_wage"]) && $errors["default_wage"]){echo 'has-error';}?>" name="default_wage" type="text" <?php if(DEFAULT_WAGE){echo 'value="' , DEFAULT_WAGE , '"';}?>>
                        </div>
                    </div>
                    <hr class="fieldSeparators" />
                    <div class="button-row overflow-auto">
                        <button type="submit" class="custom_btn btn btn-primary pull-left" name="saveButton" id="saveButton" value="view">
                            <?php echo _('Save settings'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

