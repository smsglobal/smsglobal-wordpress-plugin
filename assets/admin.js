(function ($) {
    "use strict";

    $(function () {
        var $number = $("#id_number");

        $("#id_to").change(function () {
            var isNumber = "number" === $(this).val();

            $number.toggle(isNumber);

            if (isNumber) {
                $("#id_number").focus();
            }
        })
            .trigger("change");
    });
}(window.jQuery));
