# Silverstripe Optimised Images
Automatically optimise images that are uploaded to the SilverStripe CMS

----
## Installation
Installation can be done either by composer or by manually downloading a release.

### Via Composer
`$ composer require joshcronin/silverstripe-optimisedimages`

### Manually
 1.  Download the module from [the releases page](https://github.com/joshcronin/silverstripe-optimisedimages/releases).
 2.  Extract the files.
 3.  Make sure the folder after being extracted is named 'silverstripe-optimisedimages'.
 4.  Place this directory in your sites root directory. This is the one with framework and cms in it.
 5.  Run `/dev/build` on your site.

----
## Usage
The plugin extends to the `onAfterUpload` method of the `Image` object.  Whenever an `Image` object is uploaded it will optimise the image using the options defined in `app.yaml`.

There are three keys in the config that are used regardless of the optimisation provider.  These are `Use`, `SaveOriginal` and `OriginalDir`.

#### Use
Use accepts two values `Kraken` or `TinyPNG`.  The value is used to decide which service to use to optimise the images.

#### SaveOriginal
SaveOrginal accepts a boolean, if true it will keep the original image - unoptimised - at the directory specified in the `OriginalDir` key.

#### OriginalDir
OriginalDir accepts a string representing the path to the directory that the original images should be saved to.  This is only used if `SaveOriginal` is set to `true`.