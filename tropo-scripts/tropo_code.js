// QUICK START GUIDE
//
// 1. Clone this gists and make it private
// 2. Create an incoming integratin in a Spark Room from the Spark Web client : http://web.ciscospark.com
// 3. Replace YOUR_INTEGRATION_SUFFIX by the integration id, example: Y2lzY29zcGFyazovL3VzL1dFQkhPT0svZjE4ZTI0MDctYzg3MS00ZTdmLTgzYzEtM2EyOGI1N2ZZZZZ
// 4. Create your Tropo application pointing to your gist URL, append /raw/tropodevops-sample.js to the gist URL

//
// Cisco Spark Logging Library for Tropo
//

// Factory for the Spark Logging Library, with 2 parameters
//    - the name of the application will prefix all your logs,
//    - the Spark Incoming integration (to  which logs will be posted)
// To create an Incoming Integration
//   - click integrations in the right pane of a Spark Room (Example : I create a dedicated "Tropo Logs" room)
//   - select incoming integration
//   - give your integration a name, it will be displayed in the members lists (Example : I personally named it "from tropo scripting")
//   - copy your integration ID, you'll use it to initialize the SparkLibrary
function SparkLog(appName, incomingIntegrationID) {

    if (!appName) {
        appName = "";
        //log("SPARK_LOG : bad configuration, no application name, exiting...");
        //throw createError("SparkLibrary configuration error: no application name specified");
    }
    this.tropoApp = appName;

    if (!incomingIntegrationID) {
        log("SPARK_LOG : bad configuration, no Spark incoming integration URI, exiting...");
        throw createError("SparkLibrary configuration error: no Spark incoming integration URI specified");
    }
    this.sparkIntegration = incomingIntegrationID;

    log("SPARK_LOG: all set for application:" + this.tropoApp + ", posting to integrationURI: " + this.sparkIntegration);
}

// This function sends the log entry to the registered Spark Room
// Invoke this function from the Tropo token-url with the "sparkIntegration" parameter set to the incoming Webhook ID you'll have prepared
// Returns true if the log entry was acknowledge by Spark (ie, got a 2xx HTTP status code)
SparkLog.prototype.log = function(newLogEntry) {

    // Robustify
    if (!newLogEntry) {
        newLogEntry = "";
    }

    var result;
    try {
        // Open Connection
        var url = "https://api.ciscospark.com/v1/webhooks/incoming/" + this.sparkIntegration;
        var connection = new java.net.URL(url).openConnection();

        // Set timeout to 10s
        connection.setReadTimeout(10000);
        connection.setConnectTimeout(10000);

        // Method == POST
        connection.setRequestMethod("POST");
        connection.setRequestProperty("Content-Type", "application/json");

        // TODO : check if this cannot be removed
        connection.setRequestProperty("Content-Length", newLogEntry.length);
        connection.setUseCaches(false);
        connection.setDoInput(true);
        connection.setDoOutput(true);

        //Send Post Data
        var bodyWriter = new java.io.DataOutputStream(connection.getOutputStream());
        log("SPARK_LOG: posting: " + newLogEntry + " to: " + url);
        var contents = '{ "text": "' + this.tropoApp + ': ' + newLogEntry + '" }'
        bodyWriter.writeBytes(contents);
        bodyWriter.flush();
        bodyWriter.close();

        var result = connection.getResponseCode();
        log("SPARK_LOG: read response code: " + result);

        if (result < 200 || result > 299) {
            log("SPARK_LOG: could not log to Spark, message format not supported");
            return false;
        }
    }
    catch (e) {
        log("SPARK_LOG: could not log to Spark, socket Exception or Server Timeout");
        return false;
    }

    log("SPARK_LOG: log successfully sent to Spark, status code: " + result);
    return true; // success
}


//
// Cisco Spark Client Library for Tropo
//

// Factory for the Spark Library, with 1 parameter
//    - the Spark API token
function SparkClient(spark_token) {

    if (!spark_token) {
        log("SPARK_CLIENT : bad configuration, no API token, exiting...");
        throw createError("SparkClient configuration error: no API token specified");
    }
    this.token = spark_token;

    log("SPARK_CLIENT: all set; ready to invoke spark");
}

// Returns a status code
SparkClient.prototype.createMemberShip = function(roomID, email) {

    // Robustify
    if (!roomID) {
        return 400;
    }
    if (!email) {
        return 400;
    }

    var result;
    try {
        // Open Connection
        var url = "https://api.ciscospark.com/v1/memberships";
        var connection = new java.net.URL(url).openConnection();

        // Set timeout to 10s
        connection.setReadTimeout(10000);
        connection.setConnectTimeout(10000);

        // Method == POST
        connection.setRequestMethod("POST");
        connection.setRequestProperty("Content-Type", "application/json");
        connection.setRequestProperty("Authorization", "Bearer " + this.token);

        // Prepare payload
        var payload = '{ "roomId": "' + roomID + '", "personEmail": "' + email + '", "isModerator": "false" }'

        // [TODO] Check if this cannot be removed
        connection.setRequestProperty("Content-Length", payload.length);
        connection.setUseCaches(false);
        connection.setDoInput(true);
        connection.setDoOutput(true);

        //Send Post Data
        var bodyWriter = new java.io.DataOutputStream(connection.getOutputStream());
        log("SPARK_CLIENT: posting: " + payload + " to: " + url);
        bodyWriter.writeBytes(payload);
        bodyWriter.flush();
        bodyWriter.close();

        result = connection.getResponseCode();
        log("SPARK_CLIENT: read response code: " + result);

    }
    catch (e) {
        log("SPARK_CLIENT: could not log to Spark, socket Exception or Server Timeout");
        return 500;
    }

    if (result < 200 || result > 299) {
        log("SPARK_CLIENT: could not add user with email: " + email + " to room:" + roomID);
    }
    else {
        log("SPARK_CLIENT: user with email: " + email + " added to room:" + roomID);
    }

    return result; // success
}


//
// Library to send outbound API calls
//

// Returns the JSON object at URL or undefined if cannot be accessed
function requestJSONviaGET(requestedURL) {
    try {
        var connection = new java.net.URL(requestedURL).openConnection();
        connection.setDoOutput(false);
        connection.setDoInput(true);
        connection.setInstanceFollowRedirects(false);
        connection.setRequestMethod("GET");
        connection.setRequestProperty("Content-Type", "application/json");
        connection.setRequestProperty("charset", "utf-8");
        connection.connect();

        var responseCode = connection.getResponseCode();
        log("JSON_LIBRARY: read response code: " + responseCode);
        if (responseCode < 200 || responseCode > 299) {
            log("JSON_LIBRARY: request failed");
            debug("request failed");
            return undefined;
        }

        // Read stream and create response from JSON
        var bodyReader = connection.getInputStream();
        // [WORKAROUND] We cannot use a byte[], not supported on Tropo
        // var myContents= new byte[1024*1024];
        // bodyReader.readFully(myContents);
        var contents = new String(org.apache.commons.io.IOUtils.toString(bodyReader));
        
        var parsed = JSON.parse(contents);
        log("JSON_LIBRARY: JSON is " + parsed.toString());

        return parsed;
    }
    catch (e) {
        log("JSON_LIBRARY: could not retreive contents, socket Exception or Server Timeout");
        debug("error throwed on GET");
        return undefined;
    }
}

// Returns the Status Code when GETting the URL
function requestStatusCodeWithGET(requestedURL) {
    try {
        var connection = new java.net.URL(requestedURL).openConnection();
        connection.setDoOutput(false);
        connection.setDoInput(true);
        connection.setInstanceFollowRedirects(false);
        connection.setRequestMethod("GET");
        connection.setRequestProperty("Content-Type", "application/json");
        connection.setRequestProperty("charset", "utf-8");
        connection.connect();

        var responseCode = connection.getResponseCode();
        return responseCode;
    }
    catch (e) {
        log("JSON_LIBRARY: could not retreive contents, socket Exception or Server Timeout");
        return 500;
    }
}


//
// Script logic starts here
//

// Let's create several instances for various log levels
var SparkInfo = new SparkLog("", "Y2lzY29zcGFyazovL3VzL1dFQkhPT0svOGRiY2NhNjUtNjkwZC00NGJlLTkyZjgtNjlmNzNmMjJjMDI4"); //
var SparkDebug = new SparkLog("", "Y2lzY29zcGFyazovL3VzL1dFQkhPT0svOGRiY2NhNjUtNjkwZC00NGJlLTkyZjgtNjlmNzNmMjJjMDI4");

// info level used to get a synthetic sump up of what's happing
function info(logEntry) {
    log("INFO: " + logEntry);
    SparkInfo.log(logEntry);
    // Uncomment if you opt to go for 2 distinct Spark Rooms for DEBUG and INFO log levels
    SparkDebug.log(logEntry);
}

// debug level used to get detail informations
function debug(logEntry) {
    log("DEBUG: " + logEntry);
    SparkDebug.log(logEntry);
}

// returns true or false
function isEmail(email) {
    // extract from http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

// returns an email address if found in the phrase specified
function extractEmail(phrase) {
    if (phrase) {
        var parts = phrase.split(" ");
        for (var i = 0; i < parts.length; i++) {
            if (isEmail(parts[i])) {
                return parts[i];
            }
        }
    }
    return null;
}

function fetchNextActivities() {
    var url = "https://pixelscamp.herokuapp.com/next";
    var response = requestJSONviaGET(url);
    if (response && response instanceof Array) {
        return response;
    }
    return [];
}

// Adds the user with email to the room,
// Returns an HTTP status referenced here: https://developer.ciscospark.com/endpoint-memberships-post.html
function addUserToSupportRoom(email) {
    var client = new SparkClient("YOUR_CISCO_SPARK_API_ACCESS_TOKEN");
    var result = client.createMemberShip("THE_IDENTIFIER_OF_THE_SPARK_ROOM_TO_JOIN", email);
    return result;
}


// Convert time from CET to TZ
// example : convertCET(new Date().getTime())
function convertCET(ref) {
    var offset = 1;
    return new Date(ref + (3600000 * offset));
}

// Returns current time in format HH:MM AM|PM
function timeAtEvent() {
    var meridian = "AM";
    var nowlocally = convertCET(Date.now());
    var hours = nowlocally.getHours();
    if (hours > 12) {
        meridian = "PM";
        hours -= 12;
    }
    return "" + hours + ":" + nowlocally.getMinutes() + " " + meridian;
}

function say_as(value,type){
      ssml_start="<?xml version='1.0'?><speak>";
      ssml_end="</say-as></speak>";
      ssml ="<say-as interpret-as='vxml:"+ type + "'>" + value+"";
      complete_string = ssml_start + ssml + ssml_end;
      log('@@ Say as: ' + complete_string);
      say(complete_string);
}

function post(url, options) {
    if(options.query) {
        url += "?";
        var delimiter = "";
        for(var propName in (options.query||{})) {
            url += (delimiter + (propName + "=" + escape(options.query[propName])));
            delimiter = "&";
        }
    }
    debug("Fetching " + url);
    var code;

    var body = options.body;
    if(body == null) {
        throw {message:"Body is required"};
    }

    try {

        // Open Connection
        connection = new java.net.URL(url).openConnection();

        // Set timeout
        var timeout = options.timeout ? options.timeout : 10000;
        connection.setReadTimeout(timeout);
        connection.setConnectTimeout(timeout);

        // Method == POST
        connection.setRequestMethod("POST");

        // Set Content Type
        var contentType = options.contentType != null ? options.contentType : "text/plain";
        connection.setRequestProperty("Content-Type", contentType);

        // Set Content Length
        connection.setRequestProperty("Content-Length", body.length);

        // Silly Java Stuff
        connection.setUseCaches (false);
        connection.setDoInput(true);
        connection.setDoOutput(true);

        //Send Post Data
        bodyWriter = new java.io.DataOutputStream(connection.getOutputStream());
        bodyWriter.writeBytes(body);
        bodyWriter.flush ();
        bodyWriter.close ();

        code = connection.getResponseCode();
    }
    catch(e) {
        throw {message:("Socket Exception or Server Timeout: " + e), code:0};
    }
    if(code < 200 || code > 299) {
        throw {message:("Received non-2XX response: " + code), code:code};
    }
    is = null;
    try {
        is = connection.getInputStream();
        return new String(org.apache.commons.io.IOUtils.toString(is));
    }
    catch(e) {
        throw {message:("Failed to read server response"), code:0};
    }
    finally {
        try {if(is != null)is.close();} catch (err){}
    }

}

// --------------- Starts here -------------------

function initiateGame() {
    var url = "https://pixelstrike-gnsps.rhcloud.com/api/player?callerid="+currentCall.callerID;
    debug("url: "+url);
    var response = requestJSONviaGET(url);
    debug(response);
    if (response) {
        return response;
    }
    return [];
}

// post 
function assign_character(){
    var choice;
    var pin;
    var message = "";
    say("Hello and welcome to PixelStrike!");
    
    ask("Would you like to be a terrorist or a cop?", {
        choices: "terrorist, cop, police",
        timeout: 10.0,
        attempts: 10,
        bargein: false,
        onBadChoice: function(event) {
            say("Sorry, Didn't understand?");
        },
        onChoice: function(event) {
            if(event.value == "terrorist"){
                say("So you one of the bad guys eh? Well, no one is judging you, your objective will be to eliminate as many pixels Campers as you can, go and plant a bomb when you are ready, to do it just call me again on the spot.");
                choice = 1;
                message = "Terrorist";
            }else{
                say("So you one of the good guys eh? Well, no one is judging you, your objective will be to defuse as many filthy terrorist's bombs as you can to save as many Pixel Campers as you can. We'll be able to gather some info on the terrorists so just call me when you want to defuse some bombs.");
                choice = 0;
                message = "Cop";
            }
        }
    });
    
    wait(500);
    
    say("Oh, I almost forgot, do you have the pin number we gave you, need to make sure it's you!");
    ask("Please insert your 4 digit pin now", {
        choices:"[4 DIGITS]",
        interdigitTimeout: 5,
        timeout: 20.0,
        mode: 'dtmf',
        attempts: 10,
        bargein: true,
        onBadChoice: function(event) {
            say("Try again!");
        },
        onChoice: function(event) {
            //post event.value
            // params: 4 digit pin and callerID and Terrorist?
            
            var url = "https://pixelstrike-gnsps.rhcloud.com/api/player/activate?callerid="+currentCall.callerID+"&code="+event.value+"&t_ct="+choice;
            debug("url: "+url);
            var response = requestJSONviaGET(url);
            debug(response);
            
//            post("https://pixelstrike-gnsps.rhcloud.com/api/player/activate", {body : "Hello, Central!", contentType: "application/json", query: {
//                "callerid": currentCall.callerID,
//                "code": event.value,
//                "t_ct": choice
//            }});
            
            debug(currentCall.callerID+" has just joined forces with the "+message+"s! with code "+event.value);
            
            say("Good, I'll be going now, call me again when you are ready.");
            hangup();
            
            call(currentCall.callerID, {network:"SMS"});
            say("Welcome to the "+message+"'s team!");
        }
    });
}

function plant_bomb(bombs_availble, killed, action){
    if(bombs_availble > 0){
        var plural = "";
        if(bombs_availble > 1) plural = "s";
        say("Nice to see you are still up and running, We have "+bombs_availble+" bomb"+plural+" ready to plant!");
        ask("Would you like to plant 1 now? Press 1 for yes or 0 for no.", {
            choices: "[1 DIGITS]",
            interdigitTimeout: 5,
            timeout: 20.0,
            mode: 'dtmf',
            attempts: 10,
            bargein: true,
            onBadChoice: function(event) {
                say("Please insert your answer now.");
            },
            onChoice: function(event) {
                if(event.value == "0"){
                    say("If you don't want to plant bombs you should have been a cop. Don't compromise this line!");
                    hangup();
                }else{
                    if(event.value == "1"){
                        // do stuff -> post planted bomb
                        // do bomb has been planted sound
                        // tell score
                        
                        var url = "https://pixelstrike-gnsps.rhcloud.com/api/bomb?callerid="+currentCall.callerID;
                        debug("url: "+url);
                        var response = requestJSONviaGET(url);
                        debug(response);
                        
//                        post("https://pixelstrike-gnsps.rhcloud.com/api/bomb", {body : "Hello, Central!", contentType: 'application/json', query: {
//                            "callerid": currentCall.callerID
//                        }});
                        
                        say("http://pixelstrike-gnsps.rhcloud.com/audio/bomb_planted.mp3");
                        hangup();
                        // todo: send sms report
                    }
                }
            }
        });
    }else{
        say("All bombs planted! Call again later, we still need to make more");
        hangup();
    }
    
    call(currentCall.callerID, {network:"SMS"});
    say("Lifes taken: "+killed+" \nBomb explosions: "+action);
}

function get_info_on_bomb(bombs, saved, action){
    // get  -> get distance to bombs
    // tell # of bombsand  distance of 2 nearest bombs
    // if distance < 5m ask for defuse
    // if answer is yes post defuse
    
    say("Nice to see you are still fighting the good fight!");
    wait(100);
    
    if(bombs.length == 0){
        say("Seems like the terrorists are keeping it low key. We have no intel on bombs right now. Try later.");
        hangup();
    }else{
    
        if((+bombs[0].distance) < 5){
            say("It seems you are right on top of a bomb, Would you like to defuse it now?");
            ask("Please press 0 for no or 1 for yes.", {
                choices: "[1 DIGITS]",
                interdigitTimeout: 5,
                timeout: 20.0,
                mode: 'dtmf',
                attempts: 10,
                bargein: true,
                onBadChoice: function(event) {
                    say("Please insert your answer now.");
                },
                onChoice: function(event) {
                    if(event.value == "0"){
                        say("If you don't want to defuse bombs you should have been a terrorist. You are a disgrace to your country!");
                        hangup();
                    }else{
                        if(event.value == "1"){


                            var url = "https://pixelstrike-gnsps.rhcloud.com/api/bomb/defuse?callerid="+currentCall.callerID+"&bomb_id="+bombs[0].id;
                            debug("url: "+url);
                            var response = requestJSONviaGET(url);
    //                        post("https://pixelstrike-gnsps.rhcloud.com/api/bomb/defuse", {body : "Hello, Central!", contentType: 'application/json', query: {
    //                            "bomb_id": bombs_availble[0].id,
    //                            "callerid": currentCall.callerID
    //                        }});

                            say("https://pixelstrike-gnsps.rhcloud.com/audio/bomb_defused.mp3");
                            hangup();
                            // todo: send sms with report
                        }
                    }
                }
            });
        }else{
            say("The closest bomb is "+(Math.round(+bombs[0].distance))+" meters away");
            hangup();
        }
    }
    
    call(currentCall.callerID, {network:"SMS"});
    say("Lives saved: "+saved+"\nBombs defused:"+action);
}

result = initiateGame();

if(result.error == 2){
    assign_character();
}else{
    if(result.error == 1){
        say("It appears that something went wrong, please try calling later! Goodbye");
        hangup();
    }else{
        // do stuff
        switch(result.data.player.t_ct){
            // Police
            case "0":
                get_info_on_bomb(result.data.bombs, result.data.player.lives_actioned, result.data.player.bombs_actioned);   //todo
                break;
            // Terrorist
            case "1": 
                plant_bomb(result.data.bombs.available, result.data.player.lives_actioned, result.data.player.bombs_actioned);  //todo
                break;
            // not registered yet
            default: 
                //assign_character();
                say("the rsrs line seems to rsrs breaking up rsrs contact rsrs can.");
                hangup();
                break;
        }
    }
}
//assign_character();

//debug(currentCall.callerID + " just called!");

// Get - says if person is assigned
// POST - params: 4 digit pin and callerID and Terrorist/Cop
// POST - plant bomb on location
// GET - get distance to bombs
// POST - difuse bomb #X
// GET - PIN code
