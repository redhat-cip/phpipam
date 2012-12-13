<?php

/**
 * SendMail functions
 *
 */

/**
 *	Get all settings / needed for footer
 */
$settings = getAllSettings();

# get active user name */
$mail['sender'] = getActiveUserDetails();

/**
 *	Definition of header and footer
 */
$mail['from']		= "$settings[siteTitle] <ipam@$settings[siteDomain]>";
$mail['headers']	= 'From: ' . $mail['from'] . "\r\n";
$mail['headers']   .= 'Reply-To: '. $settings['siteAdminMail'] . "\r\n";
$mail['headers']   .= "Content-type: text/html; charset=utf8" . "\r\n";
$mail['headers']   .= 'X-Mailer: PHP/' . phpversion();
  
$mail['header'] = "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
	<head></head>
	<body style='margin:0px;padding:0px;background:#f9f9f9;border-collapse:collapse;'>
	<table style='margin-left:10px;margin-top:5px;width:auto;padding:0px;border-collapse:collapse;'>";

$mail['footer'] = "
	<tr>
		<td style='padding:8px;margin:0px;'>
			<table>
			<tr>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'>E-mail</font></td>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'><a href='mailto:$settings[siteAdminMail]' style='color:#08c;'>$settings[siteAdminName]</a></font></td>
			</tr>
			<tr>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'>www</font></td>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'><a href='$settings[siteURL]' style='color:#08c;'>$settings[siteURL]</a></font></td>
			</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>";

$mail['footer2'] = "
	<tr>
		<td style='padding:8px;margin:0px;' colspan='2'>
			<table>
			<tr>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'>E-mail</font></td>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'><a href='mailto:$settings[siteAdminMail]' style='color:#08c;'>$settings[siteAdminName]</a></font></td>
			</tr>
			<tr>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'>www</font></td>
				<td><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'><a href='$settings[siteURL]' style='color:#08c;'>$settings[siteURL]</a></font></td>
			</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>";





/**
 *	Send IP address details mail
 */ 
function sendIPnotifEmail($to, $subject, $content)
{
	# get settings
	global $settings;
	global $mail;
	
	# set additional headers
	$mail['recipients'] = $to;
	$mail['subject']	= $subject;
	
	# reformat \n to breaks
	$content = str_replace("\n", "<br>", $content);
	
	# get active user name */
	$sender = getActiveUserDetails();
	
	# set content
	$mail['content']  = $mail['header'];
	$mail['content'] .= "<tr><td style='padding:5px;margin:0px;color:#333;font-size:16px;text-shadow:1px 1px 1px white;border-bottom:1px solid #eeeeee;'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:16px;'>$subject</font></td></tr>";
	$mail['content'] .= "<tr><td style='padding:5px;padding-left:15px;margin:0px;padding-top:5px;line-height:18px;border-top:1px solid white;border-bottom:1px solid #eeeeee;padding-top:10px;padding-bottom:10px;'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:13px;'>$content</font></td></tr>";
	$mail['content'] .= "<tr><td style='padding:5px;padding-left:15px;margin:0px;font-style:italic;padding-bottom:3px;text-align:right;color:#ccc;text-shadow:1px 1px 1px white;border-top:1px solid white;'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:11px;'>Sent by user ".$mail['sender']['real_name']." at ".date('Y/m/d H:i')."</font></td></tr>";
	$mail['content'] .= $mail['footer'];
	
	
	# send mail and update log
	if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
		# write log
		updateLogTable ("Sending notification mail to $mail[recipients] failed!", $severity = 2);
		return false;
	}
	else {
		# write log
		updateLogTable ("Sending notification mail to $mail[recipients] succeeded!", $severity = 0);
		return true;
	}
}


/**
 *	Send user account details
 */ 
function sendUserAccDetailsEmail($userDetails, $subject)
{
	# get settings
	global $settings;
	global $mail;
	
	# set additional headers
	$mail['recipients'] = $userDetails['email'];
	$mail['subject']	= $subject;
	
	# get active user name */
	$sender = getActiveUserDetails();
	
	# set content
	$mail['content']  = $mail['header'];
	$mail['content'] .= "<tr><td style='padding:5px;margin:0px;color:#333;font-size:16px;text-shadow:1px 1px 1px white;border-bottom:1px solid #eeeeee;' colspan='2'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:16px;'>$subject</font></td></tr>";	
	
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;border-top:1px solid white;padding-top:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Name</font></td>	  	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;padding-top:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $userDetails['real_name'] .'</font></td></tr>' . "\n";
	# we dont need pass for domain account
	if($userDetails['domainUser'] == 0) {
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Username</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $userDetails['username'] 	.'</font></td></tr>' . "\n";
	if(strlen($userDetails['plainpass']) != 0) {
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Password</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $userDetails['plainpass'] .'</font></td></tr>' . "\n";
	}
	}
	else {
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Password</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">* your domain username('. $userDetails['username'] .')</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Password</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">* your domain password</font></td></tr>' . "\n";
	}

	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Email</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;"><a href="mailto:'.$userDetails['email'].'" style="color:#08c;">'.$userDetails['email'].'</a></font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Role</font></td>		<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $userDetails['role'] 		.'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;border-bottom:1px solid #eeeeee;padding-bottom:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; WebApp</font></td>	<td style="padding: 0px;padding-left:15px;margin:0px;line-height:18px;text-align:left;border-bottom:1px solid #eeeeee;padding-bottom:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;"> <a href="'. $settings['siteURL'] .'" style="color:#08c;">'. $settings['siteURL']. '</font></a><td></tr>' . "\n";
	
	$mail['content'] .= "<tr><td style='padding:5px;padding-left:15px;margin:0px;font-style:italic;padding-bottom:3px;text-align:right;color:#ccc;text-shadow:1px 1px 1px white;border-top:1px solid white;' colspan='2'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:11px;'>Sent by user ".$mail['sender']['real_name']." at ".date('Y/m/d H:i')."</font></td></tr>";
	$mail['content'] .= $mail['footer2'];
	
	
	# send mail and update log
	if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
		# write log
		updateLogTable ("Sending notification mail for new account to $userDetails[email] failed!", $severity = 2);
		return false;
	}
	else {
		# write log
		updateLogTable ("Sending notification mail for new account to $userDetails[email] succeeded!", $severity = 0);
		return true;
	}
}


/**
 *	Send IP request mail
 */ 
function sendIPReqEmail($to, $subject, $request)
{
	# get settings
	global $settings;
	global $mail;
	
	# set additional headers
	$mail['recipients'] = $to;
	$mail['subject']	= $subject;
	
	# get active user name */
	$sender = getActiveUserDetails();

	# reformat \n to breaks
	$request['comment'] = str_replace("\n", "<br>", $request['comment']);	
	
	# set content
	$mail['content']  = $mail['header'];
	$mail['content'] .= "<tr><td style='padding:5px;margin:0px;color:#333;font-size:16px;text-shadow:1px 1px 1px white;border-bottom:1px solid #eeeeee;' colspan='2'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:16px;'>$subject</font></td></tr>";
	
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;border-top:1px solid white;padding-top:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Requested section   	</font></td><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;border-top:1px solid white;padding-top:10px;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $subnet .'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Requested IP address	</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. Transform2long($request['ip_addr']) .'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Description		 	</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $request['description'] .'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Hostname			 	</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $request['dns_name'] .'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Owner				</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $request['owner'] .'</font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Requested from		</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;"><a href="mailto:'.$request['requester'].'" style="color:#08c;">'. $request['requester'] .'</a></font></td></tr>' . "\n";
	$mail['content'] .= '<tr><td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">&bull; Comment			 	</font></td>	<td style="padding: 0px;padding-left:10px;margin:0px;line-height:18px;text-align:left;"><font face="Helvetica, Verdana, Arial, sans-serif" style="font-size:13px;">'. $request['comment'] .'</font></td></tr>' . "\n";	
	
	$mail['content'] .= "<tr><td style='padding:5px;padding-left:15px;margin:0px;font-style:italic;padding-bottom:3px;text-align:right;color:#ccc;text-shadow:1px 1px 1px white;border-top:1px solid white;' colspan='2'><font face='Helvetica, Verdana, Arial, sans-serif' style='font-size:11px;'>Sent by user ".$mail['sender']['real_name']." at ".date('Y/m/d H:i')."</font></td></tr>";
	$mail['content'] .= $mail['footer2'];
	
	
	# send mail and update log
	if (!mail($mail['recipients'], $mail['subject'], $mail['content'], $mail['headers'] )) {
		# write log
		$text = 'Sending notification mail to '. $mail['recipients'] . ' failed!';
		updateLogTable ("New IP request mail sending failed", "Sending notification mail to $mail[recipients] failed!", $severity = 2);
		return false;
	}
	else {
		# write log
		updateLogTable ("New IP request mail sent ok", "Sending notification mail to $mail[recipients] succeeded!", $severity = 0);
		return true;
	}
}


 
?>