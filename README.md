# webkon-typo3-composer

add to your root package.json
```
    "post-update-cmd": [
        "DirkPersky\\Typo3Composer\\Classes\\Installer::setVersion"
    ],
    "post-install-cmd": [
        "DirkPersky\\Typo3Composer\\Classes\\Installer::setVersion"
    ]
```