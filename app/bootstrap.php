<?php

function configure() {
    option('app_dir', file_path(dirname(option('root_dir')), 'app'));
    option('lib_dir', file_path(option('app_dir'), 'lib'));
    option('views_dir', file_path(option('app_dir'), 'views'));
    option('session', "app_session");
    option('debug', false);
    setlocale(LC_TIME, "ro_RO");
}


/**
 * Start the logic
 */
dispatch('/', 'index');
dispatch('/creare', 'creare');
dispatch_post('/creare', 'cont_nou');
dispatch('/contact', 'contact');
dispatch_post('/contact', 'trimite');
run();

?>