# webkon-typo3-composer

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