<?php
if(!defined('OSTSTAFFINC') || !$staff || !$thisstaff) die('Access Denied');

$info=$staff->getInfo();
$info['signature'] = Format::viewableImages($info['signature']);
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
$info['id']=$staff->getId();
?>
<form action="profile.php" method="post" id="save" autocomplete="off">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="update">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>My Account Profile</h2>
 <table class="table table-condensed" width="100%" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4>Account Information</h4>
                <em>Contact information.</em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
                Username:
            </td>
            <td><b><?php echo $staff->getUserName(); ?></b></td>
        </tr>

        <tr>
            <td width="180" class="required">
                First Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="34" name="firstname" value="<?php echo $info['firstname']; ?>">
                <?php if($errors['firstname']) echo '<span class="alert alert-danger">' .$errors['firstname']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Last Name:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="34" name="lastname" value="<?php echo $info['lastname']; ?>">
                <?php if($errors['lastname']) echo '<span class="alert alert-danger">' .$errors['lastname']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                Email Address:
            </td>
            <td class="form-group form-inline has-error">
                <input class="form-control" type="text" size="34" name="email" value="<?php echo $info['email']; ?>">
                <?php if($errors['email']) echo '<span class="alert alert-danger">' .$errors['email']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Phone Number:
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" size="22" name="phone" value="<?php echo $info['phone']; ?>">
                <?php if($errors['phone']) echo '<span class="alert alert-danger">' .$errors['phone']. '</span>'; ?>
                <label>Extension</label> <input class="form-control" type="text" size="5" name="phone_ext" value="<?php echo $info['phone_ext']; ?>">
                <?php if($errors['phone_ext']) echo '<span class="alert alert-danger">' .$errors['phone_ext']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Mobile Number:
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="text" size="22" name="mobile" value="<?php echo $info['mobile']; ?>">
                <?php if($errors['mobile']) echo '<span class="alert alert-danger">' .$errors['mobile']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Preferences</strong>: Profile preferences and settings.</em>
            </th>
        </tr>
        <tr>
            <td width="180" class="required">
                Time Zone:
            </td>
            <td class="form-group form-inline has-error">
                <select class="form-control" name="timezone_id" id="timezone_id">
                    <option value="0">&mdash; Select Time Zone &mdash;</option>
                    <?php
                    $sql='SELECT id, offset,timezone FROM '.TIMEZONE_TABLE.' ORDER BY id';
                    if(($res=db_query($sql)) && db_num_rows($res)){
                        while(list($id,$offset, $tz)=db_fetch_row($res)){
                            $sel=($info['timezone_id']==$id)?'selected="selected"':'';
                            echo sprintf('<option value="%d" %s>GMT %s - %s</option>',$id,$sel,$offset,$tz);
                        }
                    }
                    ?>
                </select>
                <?php if($errors['timezone_id']) echo '<span class="alert alert-danger">' . $errors['timezone_id']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Preferred Language:
            </td>
            <td class="form-group form-inline">
        <?php
        $langs = Internationalization::availableLanguages(); ?>
                <select class="form-control" name="lang">
                    <option value="">&mdash; Use Browser Preference &mdash;</option>
<?php foreach($langs as $l) {
    $selected = ($info['lang'] == $l['code']) ? 'selected="selected"' : ''; ?>
                    <option value="<?php echo $l['code']; ?>" <?php echo $selected;
                        ?>><?php echo $l['desc']; ?></option>
<?php } ?>
                </select>
                <?php if($errors['lang']) echo '<span class="alert alert-danger">'  .$errors['lang']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
               Daylight Saving:
            </td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="daylight_saving" value="1" <?php echo $info['daylight_saving']?'checked="checked"':''; ?>>
                <label>Observe daylight saving</label>
                <p class="help-block">Current Time: <?php echo Format::date($cfg->getDateTimeFormat(),Misc::gmtime(),$info['tz_offset'],$info['daylight_saving']); ?></p>
            </td>
        </tr>
        <tr>
            <td width="180">Maximum Page size:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="max_page_size">
                    <option value="0">&mdash; system default &mdash;</option>
                    <?php
                    $pagelimit=$info['max_page_size']?$info['max_page_size']:$cfg->getPageSize();
                    for ($i = 5; $i <= 50; $i += 5) {
                        $sel=($pagelimit==$i)?'selected="selected"':'';
                         echo sprintf('<option value="%d" %s>show %s records</option>',$i,$sel,$i);
                    } ?>
                </select><label> per page.</label>
            </td>
        </tr>
        <tr>
            <td width="180">Auto Refresh Rate:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="auto_refresh_rate">
                  <option value="0">&mdash; disable &mdash;</option>
                  <?php
                  $y=1;
                   for($i=1; $i <=30; $i+=$y) {
                     $sel=($info['auto_refresh_rate']==$i)?'selected="selected"':'';
                     echo sprintf('<option value="%d" %s>Every %s %s</option>',$i,$sel,$i,($i>1?'mins':'min'));
                     if($i>9)
                        $y=2;
                   } ?>
                </select>
                <p class="help-block">Tickets page refresh rate in minutes.</p>
            </td>
        </tr>
        <tr>
            <td width="180">Default Signature:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="default_signature_type">
                  <option value="none" selected="selected">&mdash; None &mdash;</option>
                  <?php
                  $options=array('mine'=>'My Signature','dept'=>'Dept. Signature (if set)');
                  foreach($options as $k=>$v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $k,($info['default_signature_type']==$k)?'selected="selected"':'',$v);
                  }
                  ?>
                </select>
                <p class="help-block">(You can change selection on ticket page)</p>
                <?php if($errors['default_signature_type']) echo '<span class="alert alert-danger">' .$errors['default_signature_type']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">Default Paper Size:</td>
            <td class="form-group form-inline">
                <select class="form-control" name="default_paper_size">
                  <option value="none" selected="selected">&mdash; None &mdash;</option>
                  <?php
                  $options=array('Letter', 'Legal', 'A4', 'A3');
                  foreach($options as $v) {
                      echo sprintf('<option value="%s" %s>%s</option>',
                                $v,($info['default_paper_size']==$v)?'selected="selected"':'',$v);
                  }
                  ?>
                </select>
                <p class="help-block">Paper size used when printing tickets to PDF</p>
                <?php if($errors['default_paper_size']) echo '<span class="alert alert-danger">' . $errors['default_paper_size']. '</span>'; ?>
            </td>
        </tr>
        <?php
        //Show an option to show assigned tickets to admins & managers.
        if($staff->isAdmin() || $staff->isManager()){ ?>
        <tr>
            <td>Show Assigned Tickets:</td>
            <td class="form-group form-inline">
                <input class="form-control checkbox" type="checkbox" name="show_assigned_tickets" <?php echo $info['show_assigned_tickets']?'checked="checked"':''; ?>>
                <label>Show assigned tickets on open queue.</label>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th colspan="2">
                <em><strong>Password</strong>: To reset your password, provide your current password and a new password below.&nbsp;<span class="error">&nbsp;<?php echo $errors['passwd']; ?></span></em>
            </th>
        </tr>
        <?php if (!isset($_SESSION['_staff']['reset-token'])) { ?>
        <tr>
            <td width="180">
                Current Password:
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="password" size="18" name="cpasswd" value="<?php echo $info['cpasswd']; ?>">
                <?php if($errors['cpasswd']) echo '<span class="alert alert-danger">' .$errors['cpasswd']. '</span>'; ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td width="180">
                New Password:
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="password" size="18" name="passwd1" value="<?php echo $info['passwd1']; ?>">
                <?php if($errors['passwd1']) echo '<span class="alert alert-danger">' .$errors['passwd1']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <td width="180">
                Confirm New Password:
            </td>
            <td class="form-group form-inline">
                <input class="form-control" type="password" size="18" name="passwd2" value="<?php echo $info['passwd2']; ?>">
                <?php if($errors['passwd2']) echo '<span class="alert alert-danger">' .$errors['passwd2']. '</span>'; ?>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong>Signature</strong>: Optional signature used on outgoing emails.</em>
                <?php if($errors['signature']) echo '<span class="alert alert-danger">' .$errors['signature']. '</span>'; ?>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea class="richtext" name="signature" cols="21"
                    rows="5" style="width: 60%;"><?php echo $info['signature']; ?></textarea>
                <br><em>Signature is made available as a choice, on ticket reply.</em>
            </td>
        </tr>
    </tbody>
</table>
<p class="centered">
    <input class="btn btn-success" type="submit" name="submit" value="Save Changes">
    <input class="btn btn-warning" type="reset"  name="reset"  value="Reset Changes">
    <input class="btn btn-danger" type="button" name="cancel" value="Cancel Changes" onclick='window.location.href="index.php"'>
</p>
</form>
