<?php

	require 'vendor/autoload.php';
	use Mailgun\Mailgun;

	ob_start();
?>
<style>
</style>
<?php
	$header_styles = ob_get_clean();

	include "header.php"; 

	$formsubmitted = !empty($_GET["formsubmitted"]);

	$q1 = $mysqli->real_escape_string($_POST["q1"]);
	if ($q1) {
		$name = $mysqli->real_escape_string($_POST["name"]);
		$email = $mysqli->real_escape_string($_POST["email"]);
		$amount = $mysqli->real_escape_string($_POST["amount"]);
		$amount_code = "CZK";
		$error_amount = !is_numeric($amount);
		$error_name = empty($name);
		$error_email = empty($email); 


		if (!$error_email && !$error_name && !$error_amount) {

/*
create table rregistration ( 
	hash varchar (100), 
	email varchar (100), 
	name varchar (100), 
	q1 varchar (100), q2 varchar (100), q3 varchar (100), q4 varchar (100), 
	note varchar (10000),
	amount varchar (100), 
	amount_code varchar(100),
	payment_success varchar(100));
*/

			$q2 = $mysqli->real_escape_string($_POST["q2"]);
			$q3 = $mysqli->real_escape_string($_POST["q3"]);
			$q4 = $mysqli->real_escape_string($_POST["q4"]);
			$note = $mysqli->real_escape_string($_POST["note"]);
			$amount = $mysqli->real_escape_string($_POST["amount"]);

			$hash = bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
			$sql = "insert into rregistration values ('$hash', '$email', '$name', '$q1', '$q2', '$q3', '$q4', '$note',  '$amount', '$amount_code', 'no')";
			$mysqli->query($sql);


			$translation = array("%amount%" => $amount, "%method%" => $q4, "%name%" => $name, "%email%" => $email);

			if ($q4 == "paypal") {
				$mail_body = $results_array["form_mail_paypal"]; }
			else if ($q4 == "wire") {
				$mail_body = $results_array["form_mail_wire"]; }
			else if ($q4 == "cash") {
				$mail_body = $results_array["form_mail_wire"]; }
			else { die; }
			$mail_body = strtr($mail_body, $translation);
			// insert into db
		
			// send mail
			$client = new \Http\Adapter\Guzzle6\Client();
			$mgClient = new \Mailgun\Mailgun('key-6585e0828401fa0c238949215f8ce647', $client);
			$domain = "dzogchen.cz";

			// Make the call to the client.
/*
			$result = $mgClient->sendMessage($domain, array(
			    'from'    => 'Yellow <yellow@dzogchen.cz>',
			    'to'      =>  "$name <$email>",
			    'subject' => 'Registrace Retreat',
			    'text'    => "$mail_body"));
*/

			if ($q4 == "paypal") {
				$hash_reg = $_GET['formsubmitted'];
				$newURL = sprintf('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=yellow%40dzogchen%2ecz&lc=CZ&item_name=Mezin%c3%a1rodn%c3%ad%20komunita%20dzog%c4%8dhenu%20Kunkyabling%2c%20z%2es%2e&amount=1000%2e00&currency_code=CZK&no_note=1&no_shipping=1&rm=1&return=https%3a%2f%2fwww%2edzogchen%2ecz%2fregistration%2fcs%2f%3fpaypal%3dhash&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted');
				 }
			else {	
				$newURL = strtok($_SERVER["REQUEST_URI"],'?') . "?formsubmitted=$hash"; }
			header ('Location: ' . $newURL);
			exit();
		} }

	$page = 'registration';
	echo ob_get_clean(); ?>

	<main class="min100 registration">
		<section class="red">
			<div class="center">
				<h4><?php i("form_register_h1"); ?></h4>
				<hr />
				<?php if (!$formsubmitted) { ?>
				<form method="POST">
					<div>
						<label class="top" for="name"><?php i("form_name"); ?></label>
						<input value="XXX" type="text" name="name" id="name" />
						<div class="error">
							<?php if ($error_name) { i("form_name_error"); } ?>
						</div>
					</div>
					<div>
						<label class="top" for="email"><?php i("form_email"); ?></label>
						<input value="YYY" type="text" name="email" id="email" />
						<div class="error">
							<?php if ($error_email) { i("form_email_error"); } ?>
						</div>
					</div>
					<div>
						<h6><?php i("form_chair"); ?></h6>
						<p><?php i("form_chair_p"); ?></p>
						<ul>
							<li><input id="q11" type="radio" name="q1" value="no" checked /><label for="q11"><?php i("form_no"); ?></label></li>
							<li><input id="q12" type="radio" name="q1" value="yes" /><label for="q12"><?php i("form_yes"); ?></label></li>
						</ul>
					</div>
					<div>
						<h6><?php i("form_babysitting"); ?></h6>
						<p><?php i("form_babysitting_p"); ?></p>
						<ul>
							<li><input id="q21" type="radio" name="q2" value="no" checked /><label for="q21"><?php i("form_no"); ?></label></li>
							<li><input id="q22" type="radio" name="q2" value="yes" /><label for="q22"><?php i("form_yes"); ?></label></li>
						</ul>
					</div>
					<div>
						<h6><?php i("form_meal"); ?></h6>
						<p><?php i("form_meal_p"); ?></p>
						<ul>
							<li><input id="q31" type="radio" name="q3" value="none" checked /><label for="q31"><?php i("form_meal_option_1"); ?></label></li>
							<li><input id="q32" type="radio" name="q3" value="saturday" /><label for="q32"><?php i("form_meal_option_2"); ?></label></li>
							<li><input id="q33" type="radio" name="q3" value="sunday" /><label for="q33"><?php i("form_meal_option_3"); ?></label></li>
							<li><input id="q34" type="radio" name="q3" value="both" /><label for="q34"><?php i("form_meal_option_4"); ?></label></li>
						</ul>
					</div>
					<div>
						<h6><?php i("form_amount"); ?></h6>
						<p><?php i("form_amount_p"); ?></p>
						<div class="pre_amount">
							<label for="amount"><?php i("form_pre_amount"); ?></label><input value="1080" id="amount" type="text" name="amount" />
							<div class="error"><span style="display: none;" class="n_error">Částka musí být číslo</span></div>
						</div>
						<h6><?php i("form_payment"); ?></h6>
						<p><?php i("form_payment_p"); ?></p>
						<ul class="q_payment">
							<li><input checked id="q42" type="radio" name="q4" value="paypal" /><label for="q42"><?php i("form_paypal"); ?></label> </li>
							<li><input id="q41" type="radio" name="q4" value="wire" /><label for="q41"><?php i("form_wire"); ?> </label></li>
							<li><input id="q43" type="radio" name="q4" value="cash" /><label for="q43"><?php i("form_cash"); ?></label> </li>
						</ul>
					</div>
					<div>
						<input type="submit" name="submit" value="<?php i("form_register"); ?>" />
					</div>
				</form>
				<?php } else { 
				?>
					<p><?php i("form_register_success"); ?></p>
					<p></p>
				<?php } ?>
			</div>
		</section>
	</main>

<?php ob_start(); ?>
	<script type="text/javascript">
		$(".pre_amount input").on('input propertychange paste',function (e) {
			var v = $(this).val();
			if (isNaN(v)) {
				$(".n_error").show();
				$('input[type="submit"]').attr("disabled", "disabled"); }
			else {	
				$(".n_error").hide();
				$('input[type="submit"]').removeAttr("disabled");
				var n = parseInt(v);
				if (n == 0) {
					$(".q_payment input").attr("disabled", "disabled"); }
				else {
					$(".q_payment input").removeAttr("disabled"); } } });
	</script>
<?php
	$script = ob_get_clean();
	include "footer.php"; ?>