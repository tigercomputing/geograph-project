{assign var="page_title" value="Register"}
{include file="_std_begin.tpl"}

<h2>Register</h2>

{dynamic}

{if $registration_ok}

	<p>Thanks for registering - we've sent you an email, simply
	follow the link contained in the email to confirm your 
	registration</p>

        <p><b>Hotmail users please note:</b> Check your "Junk E-Mail" folder as we've found
	Hotmail sometimes treats the confirmation mail as spam.</p>


{elseif $confirmation_status eq "ok"}
	<p>Congratulations - your registration is complete. We 
	hope you'll enjoy contributing!</p>
	
	<p>You should now <a title="view your profile" href="/profile.php">view your profile</a>
	to configure your site preferences.

	</p>

{elseif $confirmation_status eq "expired"}
	<p>Your previous registration has been expired for security reasons.
	Please
	<a title="Register here" href="/register.php">sign up</a> again.</p>

{elseif $confirmation_status eq "alreadycomplete"}
	<p>You have already completed the registration confirmation - please
	<a title="Log in here" href="/login.php">log in</a> using your username and password</p>

{elseif $confirmation_status eq "fail"}
	<p>Sorry, there was a problem confirming your registration.
	Please <a href="contact.php">contact us</a> if the problem persists.</p>
{else}

	<form action="register.php" method="post">
	<input type="hidden" name="CSRF_token" value="{$CSRF_token}" />

	{if $errors.csrf}
	<div class="interestBox" style="background-color:yellow; color:black; border:2px solid orange; padding:5px; font-size:0.9em">
	Your input could not be processed due to <a href="/help/csrf">security reasons</a>. Please verify the below form and try again.
	</div>
	{/if}

	<p>You must register before you can upload photos, but it's quick
	and painless and free. </p>

	<label for="name">Your name</label><br/>
	<input id="name" name="name" value="{$name|escape:'html'}"/>
	<span class="formerror">{$errors.name}</span>

	<br/><br/>

	<label for="email">Your email address</label><br/>
	<input id="email" name="email" value="{$email|escape:'html'}"/>
	<span class="formerror">{$errors.email}</span>

	<br/><br/>

	<label for="password1">Choose a password</label><br/>
	<input size="12" type="password" id="password1" name="password1" value="{$password1|escape:'html'}"/>
	<span class="formerror">{$errors.password1}</span>

	<br/><br/>
	<label for="password2">Confirm password</label><br/>
	<input size="12" type="password" id="password2" name="password2" value="{$password2|escape:'html'}"/>
	<span class="formerror">{$errors.password2}</span>
	<br/><br/>
			<div style="padding:10px; border:2px solid yellow; font-size:0.7em">
			<img src="//{$static_host}/templates/basic/img/icon_alert.gif" alt="Alert" width="30" height="24" align="left" style="margin-right:10px"/>
			Please note that we store the specified data into your user profile. Only the name is publicly visible, the other
			items are only used internally. Additionally, we will send emails to your address, for example for requests about your contributions.
			See our <a href="/help/privacy">privacy statement</a> for details.</div>
			<input type="checkbox" id="confirmdata" name="confirmdata" value="1"{if $confirmdata} checked="checked"{/if} /> <label for="confirmdata">I understand that you are storing and processing my data as described!</label>
			<br/>
			<span class="formerror">{$errors.confirmdata}</span>
	<br/>
	<span class="formerror">{$errors.general}</span>
	<br/>

	<input type="submit" name="register" value="Register"/>
	</form>  

	<p>We won't sell or distribute your
	email address, we hate spam, we really do.</p>

{/if}

{/dynamic}
    
{include file="_std_end.tpl"}
