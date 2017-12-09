define([], function () {
    return {
        socket: 'wss://pokermain.royal-wars.com/socket/',
        animation_enable: true,
        testusers: [],
        title: 'GlobalAmuse poker',
        img_path: '../img/',
        img_version: '2',
        facebook: {
            url: appOptions.app_url,
            init: {
                appId: appOptions.app_id,
                version: 'v2.2',
                xfbml: true,
            },
            permission: ['public_profile', 'user_friends', 'publish_actions']
        },
        vkontakte: {
            init: {
                apiId: ((typeof (VK_ID) != 'undefined') ? VK_ID : ''),
            },
            price_rate: 10,
            test_mode: false
        },
        auth_type: appOptions.auth,
        api_version: '1.0',
        timeout: 3600,
        langDefault: 'en',
        langs: [
            'en', 'ru', 'fr', 'es', 'it', 'de'],
        routers: {
            default: {
                regexp: '^\/([^\/?]*)(?:\/([^\/?]*))?',
                constraints: {
                    controller: 1,
                    action: 2
                },
                default: {
                    controller: 'index',
                    action: 'index'
                }
            }
        },
        images: {
            'bg': 'index/index-bg.jpg',
            'loader': 'index/index-loader.png',
            'index-title': 'index/index-title.png',
            'startbtn': 'index/index-startbtn.png',
            'bg-home': 'bg-home.jpg',
            'table': 'home/table-min.png',
            'chair': 'home/chair.png',
            'cards': 'cards/cards-min.png',
            'chips': 'cards/chips/chips-min.png',

            'sl-machine': 'slot-machine/slot-machine-bg.png',

            'el11': 'slot-machine/bar.png',
            'el10': 'slot-machine/seven.png',
            'el9': 'slot-machine/diamant.png',
            'el8': 'slot-machine/horseshoe.png',

            'el7': 'slot-machine/heart.png',
            'el6': 'slot-machine/clubs.png',
            'el5': 'slot-machine/diamonds.png',
            'el4': 'slot-machine/spades.png',

            'el3': 'slot-machine/lemon.png',
            'el2': 'slot-machine/watermelon.png',
            'el1': 'slot-machine/cherry.png',
            'el0': 'slot-machine/bell.png',

            'chip-s': 'cards/chip-s.png',
            'dealer': 'cards/dealer_button-min.png'
        },
        files: [],
        cards: {
            'card-10-clubs': {x: 1, y: 1},
            'card-10-diamonds': {x: 369, y: 1},
            'card-10-hearts': {x: 737, y: 1},
            'card-10-spades': {x: 1105, y: 1},
            'card-2-clubs': {x: 1473, y: 1},
            'card-2-diamonds': {x: 1841, y: 1},
            'card-2-hearts': {x: 2209, y: 1},
            'card-2-spades': {x: 2577, y: 1},
            'card-3-clubs': {x: 1, y: 523},
            'card-3-diamonds': {x: 369, y: 523},
            'card-3-hearts': {x: 737, y: 523},
            'card-3-spades': {x: 1105, y: 523},
            'card-4-clubs': {x: 1473, y: 523},
            'card-4-diamonds': {x: 1841, y: 523},
            'card-4-hearts': {x: 2209, y: 523},
            'card-4-spades': {x: 2577, y: 523},
            'card-5-clubs': {x: 1, y: 1045},
            'card-5-diamonds': {x: 369, y: 1045},
            'card-5-hearts': {x: 737, y: 1045},
            'card-5-spades': {x: 1105, y: 1045},
            'card-6-clubs': {x: 1473, y: 1045},
            'card-6-diamonds': {x: 1841, y: 1045},
            'card-6-hearts': {x: 2209, y: 1045},
            'card-6-spades': {x: 2577, y: 1045},
            'card-7-clubs': {x: 1, y: 1567},
            'card-7-diamonds': {x: 369, y: 1567},
            'card-7-hearts': {x: 737, y: 1567},
            'card-7-spades': {x: 1105, y: 1567},
            'card-8-clubs': {x: 1473, y: 1567},
            'card-8-diamonds': {x: 1841, y: 1567},
            'card-8-hearts': {x: 2209, y: 1567},
            'card-8-spades': {x: 2577, y: 1567},
            'card-9-clubs': {x: 1, y: 2089},
            'card-9-diamonds': {x: 369, y: 2089},
            'card-9-hearts': {x: 737, y: 2089},
            'card-9-spades': {x: 1105, y: 2089},
            'card-ace-clubs': {x: 1473, y: 2089},
            'card-ace-diamonds': {x: 1841, y: 2089},
            'card-ace-hearts': {x: 2209, y: 2089},
            'card-ace-spades': {x: 2577, y: 2089},
            'card-jack-clubs': {x: 1, y: 2611},
            'card-jack-diamonds': {x: 369, y: 2611},
            'card-jack-hearts': {x: 737, y: 2611},
            'card-jack-spades': {x: 1105, y: 2611},
            'card-king-clubs': {x: 1473, y: 2611},
            'card-king-diamonds': {x: 1841, y: 2611},
            'card-king-hearts': {x: 2209, y: 2611},
            'card-king-spades': {x: 2577, y: 2611},
            'card-queen-clubs': {x: 2945, y: 1},
            'card-queen-diamonds': {x: 2945, y: 523},
            'card-queen-hearts': {x: 2945, y: 1045},
            'card-queen-spades': {x: 2945, y: 1567},
            'back': {x: 2945, y: 2089}
        }
    };
});