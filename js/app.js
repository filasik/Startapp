
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
    });



    // Následující blok zachyti odeslani pozadavku na ostraneni uzivatele
    $(document).on('click', '.deleteUserBtn', function () {
        // Na tlacitku uzivatele si sejmeme hodnotu url z hodnoty atributu 'data-url'
        var url = $(this).attr('data-url');

        // Nactu si radek tabulky daneho uzivatele
        var row = $(this).closest('tr');

        // Chceme mazat uzivatele - polozime jeste overujici dotaz
        if (confirm('Chcete opravdu smazat uzivatele??') !== true) {
            // Zabranim vykonani akce
            return false;
        }

        // Ajaxové volání akce odstraneni uzivatele
        // Pouzivame k tomu jednoduchou fci get() knihovny jQuery
        // Ta zajistí Ajaxový požavek i zpracování získané odpovedit
        // Více např. zde: https://www.w3schools.com/jquery/ajax_get.asp (B)
        $.get({
            'url': url
        }).done(function (data) {
            // Tato callback funkce se vola po dokonceni ajax pozadavku
            // Protoze nechceme prekreslit celou stránku -
            // - jednoduše jen opět pomocí skvélé knihovny jQuery skryjeme řádek odstraněného uzivatele
            console.log(data);
            var response = JSON.parse(data);
            if (response.status === 'success') {
                row.addClass('success');
                row.hide(1000);
            } else {
                // Doslo k chybe radek zůstava a je podbarven cervene
                row.addClass('danger');
            }
        });
    });


});
