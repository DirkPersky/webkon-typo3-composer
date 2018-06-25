# webkon-typo3-composer
run Composer commands over WebAPI from `https://webmanagement.gutenberghaus.de`.

## Scripts
add to your root package.json
```
    "scripts": {
        "post-update-cmd": [
            "DirkPersky\\Typo3Composer\\Classes\\Installer::setVersion",
            "DirkPersky\\Typo3Composer\\Classes\\ChefSymlink::setSymlink"
        ],
        "post-install-cmd": [
            "DirkPersky\\Typo3Composer\\Classes\\Installer::setVersion",
           "DirkPersky\\Typo3Composer\\Classes\\ChefSymlink::setSymlink"
        ]
    },
    "chef": "LINK_TO_YOUR_CHEF_INSTALLATION"
```

### setVersion
This handler will create a `composer.php` in your Public web dir, or i your root Path for other Systems.

If this is done, our Webmanagement Tool can call this API and call all relevant composer commands.
Only with this Module, you can `update` or `requie`Modules from out Tool

### setSymlink
This handler will replace all local Folders that we can handle with a Chef System, to performe global core Updates.

