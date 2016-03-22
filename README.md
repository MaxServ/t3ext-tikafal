# TYPO3 extension: tikafal
Extract metadata from files using Apache Tika

## Requirements

You will need a java executable if you want to use the Tika jar file.

## Installation

Clone it
```bash
git clone https://github.com/MaxServ/t3ext-tikafal.git tikafal
```

Or install it using composer:
```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/MaxServ/t3ext-tikafal.git"
        }
    ],
    "require": {
        "maxserv/tikafal": "*"
    }
}
```

## Configuration

1. Configure the extension in the extension manager. Specify the absolute path to the tika jar file. There is a fairly recent one in the Resources/Public/Java folder.
2. Tweak the fieldmapping in TypoScript under the key: `module.tx_tikafal.settings.fieldmap`.
