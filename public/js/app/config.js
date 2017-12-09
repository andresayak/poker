define(['jquery', 'config/local', 'config/global'
], function ($, local, global) {
    return $.extend(global, local);
});
