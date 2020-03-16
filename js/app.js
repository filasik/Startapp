
// Zde si otevřu použití knihovny jquery
$(function () {

    // Následující blok zachytí odeslání profilového (registračního) formuláře
    $(document).on('submit', '#profilForm', function (e) {

        // Do lokálních proměnných načtu elementy text. poli hesel
        var pass = $('#profilPass');
        var passConfirm = $('#profilPassConfirm');

        // Paklize bylo vyplněno (registrace povinně, úprava profilu volitelně)
        // A zaroven hodnoty hesla se neshodují
        if (pass && pass.val() !== passConfirm.val()) {
            alert('Pozor - hesla se neshodují');

            $('#passwordSection').css('border', '1px solid red');
            pass.val(''); // vynulovani poli formulare
            passConfirm.val('');

            // Zamezím odeslání formuláře
            return false;
        }

        // Povolím odeslání formuláře
        return true;
    })


});
