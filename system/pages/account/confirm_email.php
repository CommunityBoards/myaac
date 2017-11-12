<?php
/**
 * Account confirm mail
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

$title = 'Confirm Email';

$hash = isset($_GET['v']) ? $_GET['v'] : '';
if(empty($hash)) {
	warning('Please enter email hash code.<br/>If you copied the link, please try again with full link.');
	return;
}

if(!$res->rowCount()) {
	note("Your email couldn't be verified. Please contact staff to do it manually.");
}
else
{
	success('You have now verified your e-mail, this will increase the security of your account. Thank you for doing this.');
}
?>