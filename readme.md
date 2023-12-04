# Gallery Plugin
A WordPress plugin to add images fom Unsplash to your WordPress media library

## Usage
It adds a settings page under Media called Unsplash settings

### Add Api
![Add API](https://github.com/WestCoastDigital/JM-Unsplash/blob/main/assets/img/api.png?raw=true)

1. [Login](https://unsplash.com/developers) to Unsplash and register a free api key
1. Create an app for your site and generate keys
1. Copy and paste the public key into the API Settings Field
1. Here you can also change how many images per page

### Add Images
![Add Images](https://github.com/WestCoastDigital/JM-Unsplash/blob/main/assets/img/imagesearch.png?raw=true)

1. Click on the Image Search tab
1. Type in a keyword to search for
1. Click on each image you want to add
1. Click on load more to go to next page of images

![Load More](https://github.com/WestCoastDigital/JM-Unsplash/blob/main/assets/img/loadmore.png?raw=true)

### Delete Images
![Delete Images](https://github.com/WestCoastDigital/JM-Unsplash/blob/main/assets/img/delete.png?raw=true)

1. Click on the Delete Images Tab
1. Type 42 into the input field, this is added to help prevent accidental deletion
1. Click delete

## FAQs

#### Can I select images and then search for more before adding images?
No. If you want to do another keyword search add selected images before continuing as it will reset the page

#### Can I delete images one by one?
Yes! Same way you delete any image from the WordPress media library.

#### Does using the delete images function delete images I upload outside of this plugin?
No. When you add a image to the library it records the ID generated into a custom database field. Using the delete function only deletes the images in that database record.

#### Do I need to generate an API key for every site?
Whilst I recommend it, it is not necessary. However free keys are limited (50 requests per hour at time of writing) which is why I recommend it.

#### Is this theme or plugin dependant?
No. I built this to be a stand alone plugin. It does not rely on any plugins or frameworks to function as everything is created and coded by myself, including the gallery field. It only relies on the Unsplash API

#### Can I use this without an API Key?
No, the key is free to get however.

### Why did you create this when there is a plugin from Unsplash already?
I found the plugin was giving me issues with scrolling and loading more images when doing a search as well as I did not like how I had to use it as part of adding Featured Image. I wanted to be able to bulk add images at the start of the project. As a web developer I find myself using unsplash to generate placeholders to show the site to client, not as content to stay when going live, so I also wanted a way to bulk delete all the placeholder images used.