# Magento2-Autocomplete
Autocomplete search with as-you-type product or category suggestions

## Installation

:warning: _Always backup your store before installing._

* Copy "UgoRaffaele" folder into <your Magento install dir>/app/code
* Open a terminal and move to Magento root directory
* Run these commands in your terminal

```shell
# You must be in Magento root directory
composer require ugoraffaele/module-autocomplete
php bin/magento cache:clean
php bin/magento module:enable UgoRaffaele_Autocomplete
php bin/magento setup:upgrade
```

* If you are logged to Magento backend, logout from Magento backend and login again

## License

[GNU General Public License v3.0](LICENSE.txt)