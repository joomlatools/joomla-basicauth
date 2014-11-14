# Basic HTTP authentication plugin

This Joomla plugin allows users to login using basic HTTP authentication.

## Installation

Create a `composer.json` file in the root directory of your Joomla installation and simply require the `basicauth-extension` plugin :

```json
	{    
    	"require": {
    		"nooku/basicauth-extension": "*"
    	}
	}
```
	
Install by executing `composer install`. 

## Usage

After installing the plugin, publish it in the Plugin manager. 

Any menu item with access level set to _Registered_ or _Special_ will now prompt the user to login using basic HTTP auth if he/she tries to open this page directly. 

## Requirements

* Composer
* Joomla version 2.5 and up.

## Contributing

Fork the project, create a feature branch, and send us a pull request.

## Authors

See the list of [contributors](https://github.com/nooku/basicauth-extension/contributors).

## License

The `basicauth-extension` plugin is licensed under the GPL v3 license - see the LICENSE file for details.

