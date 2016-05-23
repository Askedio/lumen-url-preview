# Lumen URL Preview
A microservice to use for generating URL previews. Simply pass a url request and get a json result of it's paramaters.

Requests are cached, I am using Redis for this - so it's included.

# Installation
Clone the package then install.
~~~
composer install
~~~
Configure the .env file.

# Usage
Just `?q=url` is all it takes.
~~~
http://localhost?q=http://google.com
~~~
Results
~~~~
{  
   "url":"http:\/\/google.com",
   "title":"Google",
   "contentType":"text\/html",
   "description":"Search the world's information, including webpages, images, videos and more. Google has many special features to help you find exactly what you're looking for.",
   "image":"http:\/\/google.com\/images\/branding\/googleg\/1x\/googleg_standard_color_128dp.png",
   "images":[  
      "http:\/\/google.com\/images\/branding\/googlelogo\/1x\/googlelogo_white_background_color_272x92dp.png"
   ]
}
~~~

# Credits
This package uses [kasp3r/link-preview](https://github.com/kasp3r/link-preview) that does all the heavy lifting, kudos.
