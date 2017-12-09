define([
    'jquery.mb'
], function(mbAudio) {
    function Helper(){
        var path = window.location.origin;
        $.mbAudio.sounds = {
            poker: {
                id: "poker",
                ogg: path+"/sound/poker.ogg",
                mp3: path+"/sound/poker.mp3",
                sprite:{
                    createBank     : {id: "createBank", start: 0, end:  7, loop: false},
                    updateBank     : {id: "updateBank", start: 7, end:  2.02, loop: false},
                    track3     : {id: "track3", start: 2.02, end:  3.02, loop: false},
                    getCards2     : {id: "getCards2", start: 3.05, end:  3.61, loop: false},
                    getCards3     : {id: "getCards3", start: 3.05, end:  4.17, loop: false},
                    getCards4     : {id: "getCards4", start: 3.05, end:  4.73, loop: false},
                    getCards5     : {id: "getCards5", start: 3.05, end:  5.29, loop: false},
                    getCards6     : {id: "getCards6", start: 3.05, end:  5.85, loop: false},
                    getCards7     : {id: "getCards7", start: 3.05, end:  6.41, loop: false},
                    getCards8     : {id: "getCards8", start: 3.05, end:  6.97, loop: false},
                    getCards9     : {id: "getCards9", start: 3.05, end:  7.01, loop: false},
                    getCards10    : {id: "getCards10", start: 3.05, end:  7.01, loop: false},
                    track5     : {id: "track5", start: 7.01, end:  18.01, loop: false},
                    callfirst     : {id: "callfirst", start: 18.01, end:  18.06, loop: false},
                    track7     : {id: "track7", start: 18.06, end:  20.01, loop: false},
                    call1     : {id: "call1", start: 20.01, end:  20.3, loop: false},
                    call2     : {id: "call2", start: 20.3, end:  20.6, loop: false},
                    call3     : {id: "call3", start: 20.6, end:  21.07, loop: false},
                    track9     : {id: "track9", start: 21.07, end:  25.09, loop: false},
                }
            },
            notification: {
                id: "notification",
                ogg: path+"/sound/notification.ogg",
                mp3: path+"/sound/notification.mp3",
                //example of sprite
                sprite:{
                    message     : {id: "intro", start: 0, end:  1, loop: false},
                }
            },
            backgroundSprite: {
                id: "backgroundSprite",
                ogg: path+"/sound/bgSprite.ogg",
                mp3: path+"/sound/bgSprite.mp3",
                //example of sprite
                sprite:{
                    intro     : {id: "intro", start: 0, end:  343, loop: true},
                }
            },

            effectSprite: {
                id: "effectSprite",
                ogg: path+"/sound/effectsSprite.ogg",
                mp3: path+"/sound/effectsSprite.mp3",
                sprite:{
                    streak: {id: "streak", start: 0, end: 1.3, loop: false},
                }
            },
            effectSprite2: {
                id: "effectSprite2",
                ogg: path+"/sound/effectsSprite2.ogg",
                mp3: path+"/sound/effectsSprite2.mp3",
                sprite:{
                    streak: {id: "streak", start: 0, end: 1.3, loop: false},
                }
            },
            regionmap: {
                id: "regionmap",
                ogg: path+"/sound/regionSoundscape.ogg",
                mp3: path+"/sound/regionSoundscape.mp3",
                sprite:{
                    streak: {id: "streak", start: 0, end: 120, loop: true},
                }
            },
            citymap: {
                id: "citymap",
                ogg: path+"/sound/villageSoundscape.ogg",
                mp3: path+"/sound/villageSoundscape.mp3",
                sprite:{
                    streak: {id: "streak", start: 0, end: 125, loop: true},
                }
            }
        };
    };
    Helper.prototype.play = function(name, sprite) {
        $.mbAudio.play(name, sprite);
    };
    Helper.prototype.stop = function(name){
        $.mbAudio.pause(name);
    };
    Helper.prototype.isMuteAll = function(){
        return localStorage.getItem("audioOff") === undefined 
            || (localStorage.getItem("audioOff")=='false') 
            || (localStorage.getItem("audioOff")===false);
    };
    Helper.prototype.muteAll = function(){
        $.mbAudio.muteAllSounds();
        localStorage.setItem("audioOff", true);
    };
    Helper.prototype.unmuteAll = function(){
        $.mbAudio.unMuteAllSounds();
        localStorage.setItem("audioOff", false);
    };
    Helper.prototype.isMute = function(name){
        return localStorage.getItem("audioOff-"+name) === undefined
            || localStorage.getItem("audioOff-"+name) == 'false'
            || localStorage.getItem("audioOff-"+name) === false;
    };
    Helper.prototype.mute = function(name){
        $.mbAudio.mute(name);
        localStorage.setItem("audioOff-"+name, true);
    };
    Helper.prototype.unmute = function(name){
        $.mbAudio.unMute(name);
        localStorage.setItem("audioOff-"+name, false);
    };
    return new Helper();
});