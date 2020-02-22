// jquery & attach to window
const $ = require("jquery");
window.$ = $;

// bootstrap & plugins
require("bootstrap");
require("bootstrap-select");

require( 'datatables.net');
require( 'datatables.net-bs4');

// include fontawesome
import {dom} from '@fortawesome/fontawesome-svg-core'

dom.watch();

//register basic usability handles
$(document).ready(function () {
    //prevent double submit; give user instant feedback
    $("form").on("submit", function () {
        const $form = $(this);
        const $buttons = $(".btn", $form);
        if (!$buttons.hasClass("no-disable")) {
            $buttons.addClass("disabled");
        }
    });

    //initialize multiselect
    $('select[multiple]').selectpicker();

    //force reload on user browser button navigation
    $(window).on('popstate', function () {
        location.reload(true);
    });

    //enable ajax form submission
    $('form.ajax-form').on("submit", function (event) {
        event.preventDefault();
        const $form = $(this);
        const url = $form.attr("action");

        $.ajax({
            type: "POST",
            url: url,
            data: $form.serialize(), // serializes the form's elements.
            success: function (data) {
                const $buttons = $(".btn", $form);
                $buttons.removeClass("disabled");
            }
        });
    });

    $('table.sortable').DataTable({
        paging: false
    });
});
