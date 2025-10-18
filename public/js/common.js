var common = {

    setLocalStorage: function (key, value) {
        if (typeof Storage !== 'undefined') {
            localStorage.setItem(key, value);
        }
    },
    getLocalStorage: function (key, default_key) {
        if (typeof Storage !== 'undefined') {
            return localStorage.getItem(key) ?? default_key;
        }
        else {
            return default_key
        }
    },
    lightNightSwitch: function (trigger = false) {
        var body = $('body');
        var html = $('html');
        body.attr('data-bs-theme', common.getLocalStorage('theme', app_global.theme));
        var current_theme = common.getLocalStorage('theme', app_global.theme);
        if (trigger) {
            if (current_theme == 'dark') {
                current_theme = 'light';
            }
            else {
                current_theme = 'dark';
            }
        }
        body.attr('data-bs-theme', current_theme == 'dark' ? 'dark' : 'light');
        html.attr('data-bs-theme', current_theme == 'dark' ? 'dark' : 'light');
        html.addClass(current_theme == 'dark' ? 'dark-theme' : 'light-theme');
        html.removeClass(current_theme == 'dark' ? 'light-theme' : 'dark-theme');
        common.setLocalStorage('theme', current_theme == 'dark' ? 'dark' : 'light');
    }
};


$(document).ready(function () {

    $('.selectpicker').selectpicker();
    common.lightNightSwitch();
    $(".app-js-switch-theme").on('click', function () {
        common.lightNightSwitch(true)
    })

    if ($('#js-change-password').length) {
        $('#js-change-password').on('click', function () {
            if ($('#js-change-password').is(':checked')) {
                $('input[name="password"]').prop('disabled', false);
            } else {
                $('input[name="password"]').prop('disabled', true);
            }
        });
    }

    $('.app-js-toggle-password').on('click', function () {
        element = $(this);
        input_field = element.closest('div').find('input')
        if (input_field.attr('type') == 'text') {
            input_field.attr('type', 'password');
        }
        else {
            input_field.attr('type', 'text');
        }
    })

    $('form').submit(function (e) {
        var button = $(this).find('button[type="submit"]');
        button.prop('disabled', true);
        button.find('.spinner-border').removeClass('d-none');
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        if (el.getAttribute('title') || el.getAttribute('data-bs-title')) {
            new bootstrap.Tooltip(el);
        }
    });
});
