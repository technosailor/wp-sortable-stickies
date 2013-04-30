# Plugin Name
Contributors: technosailor

Requires at least: 3.5.1

Tested up to: 3.6-beta1

License: GPLv2

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds drag and drop Sticky Post sorting to the Settings > Reading Page. WordPress likes to store Stickies in order they were stickied. I don't like that.

# Description

Adds a new admin settings section to the Settings > Reading table. Drag and drop Sticky Posts in the order you want them considered by WordPress. (Note: You still have to set posts as sticky on the post edit screen for sticky posts to show up)

## Frequently Asked Questions

# How do I order sticky posts?

Just drag and drop the posts in the order you want them considered.

# I changed the order, but the order isn't changed on my site. What's wrong?

This is a case of the loop not using `orderby` => `post__in`. You vavet o manually modify your query to do this. Future versions will handle that for you.

# Do you like beer? Can I buy you one?

Derp. I'm a beer snob. Of course you can.

## Screenshots

![Screenshot](/screenshot-1.png "Screenshot")
Simply drag the posts into the order you want them.

## Changelog

# 1.0
* Initial launch.
