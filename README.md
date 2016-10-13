# PixelStrike (Cisco Hackathon @ PixelsCamp '16)

A game created @ PixelsCamp '16 for the Cisco Hackathon that implements [Tropo](https://www.tropo.com/), [Meraki CMX APIs](https://meraki.cisco.com/solutions/cmx) and [Cisco Spark APIs](https://developer.ciscospark.com/)

Please refer to [my Medium article](https://medium.com/@GNSPS/pixels-camp-cisco-hackathon-and-why-we-won-it-17030a093f9d) for further information about the competition.

1. [Description](https://github.com/GNSPS/pixelstrike-cisco-hackathon#1-description)
2. [Pitfalls Encountered](https://github.com/GNSPS/pixelstrike-cisco-hackathon#2-pitfalls-encountered)
3. [Setup](https://github.com/GNSPS/pixelstrike-cisco-hackathon#3-setup)

## 1. Description

This repo is divided in two folders: `php-backend` and `tropo-scripts` with their names being pretty self-explanatory.

`php-backend` is a [CodeIgniter 3.1](http://www.codeigniter.com)-powered API (with an awesome library from Phil Sturgeon) and should run smoothly in any cloud PaaS out there. We stuck with [Openshift from RedHat](https://openshift.redhat.com/) because I personally like it better than any other.

`tropo-scripts` has one single Javascript file made by @lostrapt that we chose to host on Tropo's hosting service as it was easier and faster to do.

`pixelstrike-schema.sql` is a MySQL schema dump file which you can run to automatically create a database identical to the one used.

### Disclaimer

Due to the nature of a hackathon (do as much as possible as fast as possible!) the codebase has no tests, code may be hacky at times and things in general may not be solid. So please bear with me and excuse something that may shock you! :D

## 2. Pitfalls Encountered

During development of Tropo's interactions we decided to, instead of using Tropo's WebAPI and putting all the stress of computing JSON responses on our free Openshift gear, make Tropo query our PHP API for specific game statuses. We got stuck there for a few hours because Tropo's JS scripting engine seems very picky about the HTTP response it gets to process.

And that is the reason why you'll see I actually bypassed the framework's REST response method and `echo`ed all the objects `json_encoded`.

Also you might note the API isn't really REST. That's because Tropo's JS script engine gets even weirder with POST requests and as we were really short on time we decided to stick only to GET requests.

**TL;DR**
- Try a simple JSON echo in whatever language you're using if Tropo isn't getting data from your API
- If you're struggling with POST, just stick to GET requests as they seem to be simpler to deal with

## 3. Setup

I'll list here all the steps taken to get this up and running.

### Openshift

Creating an account at Openshift should be fairly easy. Just register, create a free PHP gear, clone the repo and you're good to go.

If you paste the whole folder given into the freshly cloned repo, when you `git push` Openshift just takes care of the whole deployment and the site should be up and running in seconds.

### Tropo

Once again, just register create a new app and choose to create a new blank JS file when Tropo asks you to. You can then paste the contents of the file included in `tropo-scripts` to make it work in a second.

- Make sure you don't put your application in production mode because that's irreversible
- You should also contact support or ask some Cisco personnel to activate your account for Tropo to be able to send SMS's

### Meraki API

Please configure the Meraki AP to present your root folder (ours was: [https://pixelstrike-gnsps.rhcloud.com](https://pixelstrike-gnsps.rhcloud.com)) as the splash page and activate Meraki's location polling to poll your `/api/meraki`
 endpoint (in our case: https://pixelstrike-gnsps.rhcloud.com/api/meraki [POST])
 
### Spark API

This one has no secrets. It should be pretty straightforward, just change the relevant JS script function to post the log messages to a different named Spark room.
