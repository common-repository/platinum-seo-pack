jQuery(document).ready(function($) {
    $("#psp-preview-box .hidden").removeClass('hidden');
    $("#psp-preview-box").tabs();
    $('[data-toggle="tooltip"]').tooltip();
    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
});