<?php
/**
 * Change characters name
 *
 * @package   MyAAC
 * @author    Gesior <jerzyskalski@wp.pl>
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2017 MyAAC
 * @link      http://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');

echo '<script type="text/javascript" src="tools/check_name.js"></script>';

$player_id = isset($_POST['player_id']) ? (int)$_POST['player_id'] : NULL;
$name = isset($_POST['name']) ? stripslashes(ucwords(strtolower($_POST['name']))) : NULL;
if((!$config['account_change_character_name']))
	echo 'Changing character name for premium points is disabled on this server.';
else
{
	$points = $account_logged->getCustomField('premium_points');
	if(isset($_POST['changenamesave']) && $_POST['changenamesave'] == 1) {
		if($points < $config['account_change_character_name_points'])
			$errors[] = 'You need ' . $config['account_change_character_name_points'] . ' premium points to change name. You have <b>'.$points.'<b> premium points.';
		
		if(empty($errors) && empty($name))
			$errors[] = 'Please enter a new name for your character!';
		else if(strlen($name) > 25)
			$errors[] = 'Name is too long. Max. lenght <b>25</b> letters.';
		else if(strlen($name) < 3)
			$errors[] = 'Name is too short. Min. lenght <b>3</b> letters.';
		else {
			$exist = new OTS_Player();
			$exist->find($name);
			if($exist->isLoaded()) {
				$errors[] = 'Character with this name already exist.';
			}
		}
		
		if(empty($errors))
		{
			if(!admin() && !Validator::newCharacterName($name))
				$errors[] = Validator::getLastError();
		}
		
		if(empty($errors)) {
			$player = new OTS_Player();
			$player->load($player_id);
			if($player->isLoaded()) {
				$player_account = $player->getAccount();
				if($account_logged->getId() == $player_account->getId()) {
					if($player->isOnline()) {
						$errors[] = 'This character is online.';
					}
					
					if(empty($errors)) {
						$show_form = false;
						$old_name = $player->getName();
						$player->setName($name);
						$player->save();
						$account_logged->setCustomField("premium_points", $points - $config['account_change_character_name_points']);
						$account_logged->logAction('Changed name from <b>' . $old_name . '</b> to <b>' . $player->getName() . '</b>.');
						echo $twig->render('success.html.twig', array(
							'title' => 'Character Name Changed',
							'description' => 'The character <b>'.$old_name.'</b> name has been changed to <b>' . $player->getName() . '</b>.'
						));
					}
				}
				else {
					$errors[] = 'Character <b>' . $player_name . '</b> is not on your account.';
				}
			}
			else {
				$errors[] = "Character with this name doesn't exist.";
			}
		}
	}
	
	if($show_form) {
		if(!empty($errors)) {
			echo $twig->render('error_box.html.twig', array('errors' => $errors));
		}
		
		echo $twig->render('account.change_name.html.twig', array(
			'points' => $points,
			'errors' => $errors
			//'account_players' => $account_logged->getPlayersList()
		));
	}
}

?>