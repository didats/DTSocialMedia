# DTSocialMedia

DTSocialMedia is a PHP class to get the user's timeline from various social media. Currently it support Facebook, Twitter, Pinterest, Youtube, Instagram, Flickr, and RSS Feed.

## Intention

Our client asking for a mobile app which showing their timeline on various social media. So, instead of making the UI on each social media (which is tiring), we come up with showing a WebView instead. To do that, I will need to grab the timelines through PHP. 

## Contact
[Blog site](http://didats.net)

[@didats](https://twitter.com/didats)

## Some Information

For Flickr, you will need to provide the user ID, which you can get it from here: [http://idgettr.com/](http://idgettr.com/).

For Instagram, you will need to provide the user ID as well. You could get the user id from here: [http://jelled.com/instagram/lookup-user-id](http://jelled.com/instagram/lookup-user-id)


## Usage

```php
<?php
	require "DTSocialMedia.php";
	
	// create an object
	$social = new DTSocialMedia();
	
	// get the timelines
	// the $social_name values are:
	// rss, facebook, twitter, pinterest, flickr, youtube, instagram
	print_r($social->get_data($social_name, $user_or_url));
?>
```
## Result Data

```text
Array ( [title] => Another Salmiya [date] => 1363181499 [link] => http://www.flickr.com/photos/didats/8553714195/ [image] => http://farm9.staticflickr.com/8530/8553714195_2dc9af2e1c_b.jpg ) 
```

## License

This code is distributed under the terms and conditions of the MIT license.

Copyright (c) 2013 Didats Triadi

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.