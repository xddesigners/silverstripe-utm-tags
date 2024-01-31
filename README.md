# UTM Tag detection for SilverStripe 4 and 5

This module provides an method to handle UTM tags and store them in models like form submissions.

## Installation

Install the module trough composer `composer require xddesigners/silverstripe-utm-tags`

Add the `XD\UTMTags\Extensions\StoresUTMTags` extension to the model you want to store UTM tags in.

Call the method `$myModel->setUTMTagsFromSession();` before calling `write()` to store the tags. 
You could call this in a form submit method.

### Maintainers

- [XD designers](https://www.xd.nl/)
