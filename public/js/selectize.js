if ($('.selectize-tags').length) {
    console.log("Loading selectize");
    require.ensure([], function(require) {
        var $ = require('jquery');
        require('selectize');
        $('.selectize-tags').each(function () {
            var uniqid = 'tags-' + Math.random().toString(36).substr(2, 7);
            if (typeof($(this).attr('data-id')) != 'undefined') {
                return;
            }
            $(this).attr('data-id', uniqid);
            var controlled = $(this).attr('data-controlled');
            var multiple = $(this).attr('data-multiple');
            if (controlled == 1) {
                $(this).selectize({
                    maxItems: 100
                });
            } else {
                if (multiple == 1) {
                    $(this).selectize({
                        delimiter: ',',
                        persist: false,
                        createOnBlur: true,
                        create: function(input) {
                            return {
                                value: input,
                                text: input
                            }
                        },
                        maxItems: 100
                    });
                } else {
                    $(this).selectize({
                        createOnBlur: true,
                        create: function(input) {
                            return {
                                value: input,
                                text: input
                            }
                        },
                        maxItems: 1
                    });
                }
            }
        });
    });
} else {
    console.log("No selectize detected - skipping selectize");
}