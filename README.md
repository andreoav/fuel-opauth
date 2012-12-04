FuelPHP package for Opauth
=========================

FuelPHP 1.x package for [Opauth](https://github.com/uzyn/opauth).

Opauth is a multi-provider authentication framework.

Requirements
---------
FuelPHP v1.x  
Opauth >= v0.2

How to use
----------
1. Install this package for your FuelPHP application. Go to your FuelPHP application package folder.

   ```bash
   cd your_fuel_app/fuel/packages/
   git clone git://github.com/andreoav/fuel-opauth.git opauth
   ```

2. Copy the opauth configuration file located at PKGPATH/opauth/config/opauth.php to your_fuel_app/fuel/app/config/, change the security salt and tweak as you need. eg.

	```php
	<?php
	'path' => '/auth/login/',
	'callback_url' => '/auth/callback/',
	'Strategy' => array(
		'Facebook' => array(
			'app_id' => 'APP_ID',
			'app_secret' => 'APP_SECRET'
		),
	),
	```

3. Enable fuel-opauth package.
	
	```php
	<?php
	'always_load' => array(
		'packages' => array(
			'opauth',
		),
	),
	```

4. Create a controller called Controller_Auth and an action called login. eg.

	```php
	<?php
	class Controller_Auth extends Controller
	{
		private $_config = null;
	
		public function before()
		{
			if(!isset($this->_config))
			{
				$this->_config = Config::load('opauth', 'opauth');
			}
		}
		
		/**
		 * eg. http://www.exemple.org/auth/login/facebook/ will call the facebook opauth strategy.
		 * Check if $provider is a supported strategy.
		 */
		public function action_login($_provider = null)
		{
			if(array_key_exists(Inflector::humanize($_provider), Arr::get($this->_config, 'Strategy')))
			{
				$_oauth = new Opauth($this->_config, true);
			}
			else
			{
				return Response::forge('Strategy not supported');
			}
		}
		
		// Print the user credentials after the authentication. Use this information as you need. (Log in, registrer, ...)
		public function action_callback()
		{
			$_opauth = new Opauth($this->_config, false);
			
			switch($_opauth->env['callback_transport'])
			{
				case 'session':
					session_start();
					$response = $_SESSION['opauth'];
					unset($_SESSION['opauth']);
				break;            
			}
			
			if (array_key_exists('error', $response))
			{
				echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.'."<br>\n";
			}
			else
			{
				if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid']))
				{
					echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
				}
				elseif (!$_opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason))
				{
					echo '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
				}
				else
				{
					echo '<strong style="color: green;">OK: </strong>Auth response is validated.'."<br>\n";
			
					/**
					 * It's all good. Go ahead with your application-specific authentication logic
					 */
				}
			}
			
			return Response::forge(var_dump($response));
		}
	}
	```

Available strategies
--------------------
A strategy is a set of instructions that interfaces with respective authentication providers and relays it back to Opauth.
This package comes with strategies for Facebook and twitter. To install other strategies copy the files to PKGPATH/opauth/classes/Strategy/ folder.

Provider-specific:

<table>
<tr>
	<th>Strategy</th>
	<th>Maintained by</th>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://facebook.com" alt="Facebook">&nbsp;&nbsp;
		<a href="https://github.com/uzyn/opauth-facebook">Facebook</a></td>
	<td>uzyn</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://google.com" alt="Google">&nbsp;&nbsp;
		<a href="https://github.com/uzyn/opauth-google">Google</a></td>
	<td>uzyn</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://instagram.com" alt="Instagram">&nbsp;&nbsp;
		<a href="https://github.com/muhdazrain/opauth-instagram">Instagram</a></td>
	<td>muhdazrain</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://linkedin.com" alt="LinkedIn">&nbsp;&nbsp;
		<a href="https://github.com/uzyn/opauth-linkedin">LinkedIn</a></td>
	<td>uzyn</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://mixi.co.jp" alt="mixi">&nbsp;&nbsp;
		<a href="https://github.com/ritou/opauth-mixi">mixi</a></td>
	<td>ritou</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://openid.net" alt="OpenID">&nbsp;&nbsp;
		<a href="https://github.com/uzyn/opauth-openid">OpenID</a></td>
	<td>uzyn</td>
</tr>
<tr>
	<td><img src="http://g.etfv.co/http://twitter.com" alt="Twitter">&nbsp;&nbsp;
		<a href="https://github.com/uzyn/opauth-twitter">Twitter</a></td>
	<td>uzyn</td>
</tr>

</table>

Generic strategy: [OAuth](https://github.com/uzyn/opauth-oauth)

See [wiki's list of strategies](https://github.com/uzyn/opauth/wiki/List-of-strategies) for an updated list of Opauth strategies or to make requests.  Also, refer to [strategy contribution guide](https://github.com/uzyn/opauth/wiki/Strategy-contribution-guide) if you would like to contribute a strategy.
