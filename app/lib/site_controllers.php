<?php
/**
 * Main site controllers
 */

function before()
{
    layout('default_layout.php');
    set('site_title', Config::get_key('site_title'));
    set('site_simple_block', Config::get_key('site_simple_block'));
    set('site_notice_block', Config::get_key('site_notice_block'));
    set('site_footer', Config::get_key('site_footer'));
}

function not_found($errno, $errstr, $errfile=null, $errline=null)
{
    layout('default_layout.php');
    $html = '<h2 class="title">Eroare 404</h2>';
    set('site_title', Config::get_key('site_title'));
    set('page_title', 'Error 404');
    set('site_footer', Config::get_key('site_footer'));
    return html($html);
}
 
function server_error($errno, $errstr, $errfile=null, $errline=null)
{
    layout('default_layout.php');
    $html = '<h2 class="title">Eroare 500</h2>';
    set('site_title', Config::get_key('site_title'));
    set('page_title', 'Error 500 (SERVER_ERROR)');
    set('site_footer', Config::get_key('site_footer'));
    return html($html);
}

/**
 * Dispatch helpers
 */

function index() {
    set('route', ' ');
    set('page_title', 'Prima pagină');
    return html('main.php');
}

function creare() {
    set('route', '/creare');
    set('page_title', 'Creare cont');
    layout('forms_layout.php');
    set('recaptcha', recaptcha_get_html(Config::get_key('recaptcha_pubkey')));
    if(flash_now('fail'))
	set('s', $_SESSION['s']);
    
    //$cont = new Cont();
    //set('rez',$cont->alias_info('Stas.Suscov'));
	
    return html('signup.php');
}

function cont_nou() {
    set('recaptcha', recaptcha_get_html(Config::get_key('recaptcha_pubkey')));
    $s = $_POST['s'];
    option('session', true);
    $_SESSION['s'] = $s;
    
    $s['cnp'] = filter_var($s['cnp'], FILTER_SANITIZE_NUMBER_INT);
    $s['cont'] = filter_var($s['cont'], FILTER_SANITIZE_STRING);
    $s['parola'] = filter_var($s['parola'], FILTER_SANITIZE_STRING);
    $s['alias'] = filter_var($s['alias'], FILTER_SANITIZE_STRING);
    
    $resp = recaptcha_check_answer (Config::get_key('recaptcha_privkey'),
				    $_SERVER["REMOTE_ADDR"],
				    $_POST["recaptcha_challenge_field"],
				    $_POST["recaptcha_response_field"]);
    
    if (!$resp->is_valid) {
	flash('fail', 'Contul nu a fost creat. Verificați testul anti-robot.');
    }
    else if(!empty($s['cnp']) && !empty($s['cont']) && !empty($s['parola'])) {
	$cont = new Cont();
	$u = $cont->valid_user($s['cont'], $s['parola'], $s['cnp'], $s['alias']);
	if($u)
	    if($cont->create_user($u))
		flash('ok', 'Contul a fost creat. Mulțumim.');
	    else
		flash('fail', 'Contul nu a fost creat. A intervenit o eroare.');
	else
	    flash('fail', 'Contul nu a fost creat. Utilizatorul sau alias-ul există deja sau avem o problemă cu SINU.');
    }
    else {
	flash('fail', 'Contul nu a fost creat. Verificați câmpurile obligatorii.');
    }
    
    redirect_to('creare');
}

function contact() {
    set('route', '/contact');
    set('page_title', 'Contact');
    layout('forms_layout.php');
    set('recaptcha', recaptcha_get_html(Config::get_key('recaptcha_pubkey')));
    if(flash_now('fail'))
	set('c', $_SESSION['c']);
    return html('contact.php');
}

function trimite() {
    set('recaptcha', recaptcha_get_html(Config::get_key('recaptcha_pubkey')));
    $c = $_POST['c'];
    option('session', true);
    $_SESSION['c'] = $c;
    
    $c['email'] = filter_var($c['email'], FILTER_SANITIZE_EMAIL);
    $c['nume'] = filter_var($c['nume'], FILTER_SANITIZE_STRING);
    $c['cont'] = filter_var($c['cont'], FILTER_SANITIZE_STRING);
    $c['mesaj'] = filter_var($c['mesaj'], FILTER_SANITIZE_STRING);
    
    $resp = recaptcha_check_answer (Config::get_key('recaptcha_privkey'),
				    $_SERVER["REMOTE_ADDR"],
				    $_POST["recaptcha_challenge_field"],
				    $_POST["recaptcha_response_field"]);
    
    if (!$resp->is_valid) {
	flash('fail', 'Mesajul nu a fost trimis. Verificați testul anti-robot.');
    }
    else if(!empty($c['nume']) && !empty($c['email']) && !empty($c['mesaj'])) {
	    $to = Config::get_key('site_email');
	    $subject = Config::get_key('site_title'). " / Contact";
	    $headers = array();
	    $headers['MIME-Version'] = "1.0";
	    $headers['Content-type'] = "text/plain; charset=UTF-8";
	    $headers['From'] = $c['email'];
	    $headers['To'] = $to;
	    $headers['Return-Path'] = $to;
	    $headers['X-Mailer'] = 'PHP/' . phpversion();
	    $headers['X-Limonade'] = LIM_SESSION_NAME . ' / '.LIMONADE;
	    $body = $c['nume'] ." cu contul SINU: `". $c['cont'] ."` a scris:\n---\n". $c['mesaj'] ."\n---\n";
	    $headers_lines = array();
	    foreach($headers as $k=>$v) $headers_lines[] = $k.": ".$v;
	    $sent = @mail($to, $subject, $body, implode("\r\n",$headers_lines));
	    
	    if($sent)
		flash('ok', 'Mesajul a fost trimis. Mulțumim.');
	    else
		flash('fail', 'Mesajul nu a fost trimis. A intervenit o eroare.');
    }
    else {
	flash('fail', 'Mesajul nu a fost trimis. Verificați câmpurile obligatorii.');
    }
    redirect_to('/contact');
}

/**
 * Some helpers
 */
function flash_h($flash) {
    if($flash) {
	echo '<div class="flash">';
	if($flash['fail'])
	    echo '<div class="message error">'.$flash['fail'].'</div>';
	else
	    echo '<div class="message notice">'.$flash['ok'].'</div>';
	echo '</div>';
    }
}