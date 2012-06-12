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

2. Copy the opauth configuration file located at PKGPATH/opauth/config/opauth.php to your_fuel_app/fuel/app/config/ and tweak as you need. eg.

	```php
	<?php	
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

4. Create a controller called Controller_Auth and actions for strategies that you want your application support. eg.

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
			 * http://www.exemple.org/auth/facebook/
			 */
			public function action_facebook()
			{
				$_oauth = new Opauth($this->_config, true);
			}
			
			// Print the user credentials after the authentication
			public function action_callback()
			{
				$_oauth = new Opauth($this->_config, false);
				
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