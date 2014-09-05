<?php
ob_start('output_buffer');

function error_handler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            return Sumo\Logger::warning($errstr . ' in ' . $errfile . ' on line ' . $errline);
            break;
        case E_WARNING:
        case E_USER_WARNING:
            return Sumo\Logger::warning($errstr . ' in ' . $errfile . ' on line ' . $errline);
            break;
        case E_ERROR:
        case E_USER_ERROR:
            return Sumo\Logger::error($errstr . ' in ' . $errfile . ' on line ' . $errline);
            break;
        default:
            return Sumo\Logger::warning($errstr . '  in ' . $errfile . ' on line ' . $errline);
            break;
    }
}

function exception_handler($exception)
{
    Sumo\Logger::add(
        'Exception: ' . $exception->getMessage(),
        Sumo\Logger::LOG_ERROR,
        array(
            array(
                'called'    => 'Exception(' . $exception->getMessage() . ')',
                'file'      => $exception->getFile() . ':' . $exception->getLine(),
                'class'     => get_class($exception)
            )
        )
    );
    $page = '<html><head><title>Fatal exception occured</title></head><body></body></html>';
    return development_debug($page, true);
}

function error_handler_last_resort()
{
    $last = error_get_last();
    echo print_r($last,true);
    if (!empty($last['type'])) {
        Sumo\Logger::error(print_r($last,true));
        $page = '<html><head><title>Fatal error occurred</title></head><body></body></html>';

        return development_debug($page, true);
        #exit;
    }
}

function development_debug($content, $fatal = false)
{
    $extra  = ''; $debug_text = '';
    $errors = Sumo\Logger::get('total');
    $total  = '';
    foreach ($errors as $level) {
        $total .= count($level) . '/';
    }
    $total = rtrim($total, '/');

    if (!$fatal) {
        $content = str_replace('id="debug"></span>', 'id="debug" style="cursor:pointer;" data-toggle="modal" data-target="#footerdebug">' . Sumo\Logger::get('runtime') . 's, ' . $total . ' logs</span>', $content);
    }
    else {
        $extra .= '<button data-toggle="modal" data-target="#footerdebug" type="button" class="btn btn-danger btn-flat" id="error" style="margin-left: 50%;"><i class="fa fa-times-circle"></i> Error</button>';
    }

    if (defined('DEVELOPMENT') || $fatal) {

        $extra .= '<div id="footerdebug" class="modal fade colored-header ' . ($fatal ? 'info' : 'info') . '" tabindex="-1" role="dialog">';
        $extra .= '<div class="modal-dialog" style="width: 80%"><div class="modal-content">';
        $extra .= '<div class="modal-header">';
        if (!$fatal) {
            $extra .= '<h4 class="text-center">Developer mode<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></h4>';
        }
        else {
            $extra .= '<h4 class="text-center">Heerle, we\'ve got a problem..</h4>';
        }
        $extra .= '</div><div class="modal-body" style="height: 600px; overflow: auto;">';
        if ($fatal) {
            $extra .= '<p class="text-center">The system has generated a fatal error. Good thing is: you\'re getting a full trace!</p>';
        }
        $extra .= '<p>Current runtime: ' . Sumo\Logger::get('runtime') . '</p>';
        $extra .= '<p>Memory usage: ' . Sumo\Logger::get('memory') . '</p>';
        $extra .= '<p>';
        if (empty($content)) {
            $extra .= 'Empty output. ';
        }
        if ($total) {
            $i = 0;
            $collapsed = false;
            $extra .= $total . ' log lines available.</p><div id="accordion" class="panel-group accordion accordion-semi">';

            $sorted = array();
            if (isset($errors['error'])) {
                $sorted['error'] = $errors['error'];
            }
            if (isset($errors['warning'])) {
                $sorted['warning'] = $errors['warning'];
            }
            if (isset($errors['info'])) {
                $sorted['info'] = $errors['info'];
            }

            foreach ($errors as $type => $logs) {
                $extra .= '<div class="panel panel-default">';
                    if ($type == 'info') {
                        $typeclass = 'success';
                    }
                    else if ($type == 'error') {
                        $typeclass = 'danger';
                    }
                    else {
                        $typeclass = $type;
                    }

                    $extra .= '<div class="panel-heading ' . $typeclass . '">';
                        $extra .= '<h4 class="panel-title"><a href="#log-' . $type . '" data-parent="#accordion" data-toggle="collapse">';
                            $extra .= '<i class="fa fa-angle-right"></i>';

                            if (class_exists('sumo\language')) {
                                $extra .= Sumo\Language::getVar('SUMO_LOG_' . strtoupper($type));
                            }
                            else {
                                $extra .= ucfirst($type);
                            }
                        $extra .= '</a></h4>';
                    $extra .= '</div>';
                    $extra .= '<div id="log-' . $type . '" class="panel-collapse collapse ' . ((!$collapsed) ? ' in' : '') . '">';
                    $extra .= '<div class="panel-body">';

                    foreach ($logs as $log) {
                        $i++;
                        $time = explode('.', $log['runtime']);
                        $extra .= '<span>';
                            $extra .= '[' . $time[0] . '.' . str_pad($time[1], 8, '0') . '] ';
                            $debug_text .= '[' . $time[0] . '.' . str_pad($time[1], 8, '0') . '] ';
                            $extra .= '[' . $log['memory'] . '] ';
                            $debug_text .= '[' . $log['memory'] . '] ';
                            $extra .= ' <a href="#" onclick="$(\'.message-' . $i . '\').toggle(); return false;">';
                                $extra .= htmlentities($log['message']);
                                $debug_text .= htmlentities($log['message']);
                            $extra .= ' </a>';
                        $extra .= '</span><br />';
                        $extra .= '<div style="display:none;" class="message-' . $i . '"><pre>';
                            foreach ($log['backtrace'] as $nr => $trace) {
                                $extra .= 'trace ' . $nr . PHP_EOL;
                                $debug_text .= 'trace ' . $nr . PHP_EOL;
                                foreach ($trace as $key => $value) {
                                    $extra .= "\t" . $key . ":\t" . htmlentities($value) . PHP_EOL;
                                    $debug_text .= $key . ":\t" . htmlentities($value) . PHP_EOL;
                                }
                                $extra .= PHP_EOL;
                                $debug_text .= PHP_EOL;
                            }
                        $extra .= '</pre></div>';
                        $extra .= '<br />';
                        $last = htmlentities($log['message']);
                    }
                $extra .= '</div></div></div>';
                $collapsed = true;
            }
            $extra .= '</div>';

        }
        else {
            $extra .= 'No log available.</p>';
        }

        if (defined('DIR_LOGS') && defined('DEVELOPMENT')) {
            $file = DIR_LOGS . 'log_';
            if ($fatal) {
                $file .= 'fatal_';
            }
            $file .= date('Y-m-d-H-i-s') . '.txt';
            $fp = fopen($file, 'w');
            if ($fp) {
                fwrite($fp, $debug_text);
                fclose($fp);
            }
            else {
                unset($file);
            }
        }

        $extra .= '</div><div class="modal-footer">';
            $extra .= '<button class="btn btn-default btn-flat md-close" data-dismiss="modal" type="button">Close</button>';
            $extra .= '<a class="btn btn-info btn-flat" href="mailto:klantenservice@sumostore.net?title=SumoStore ' . VERSION . ' / Error&body=There was ' . ($fatal ? 'a fatal' : 'an') .  ' error. ' . (isset($file) ? 'The error log can be found in ' . $file . ', via FTP. Please add this file in the email.' : 'Log file could not be created, probably some write issues?') . '.' . PHP_EOL . PHP_EOL . ' The last error message: ' . $last . PHP_EOL . PHP_EOL . ' Please add some details about this error, for example which page, what were you doing etc.">Mail</a>';
        $extra .= '</div></div></div></div>';

        if (!$fatal) {
            $extra .= '<script type="text/javascript">$(function(){$("#footerdebug").hide();})</script>';
        }
    }
    if ($fatal) {
        $head  = '<link href="//fonts.googleapis.com/css?family=Open+Sans:600,300,400" rel="stylesheet" type="text/css">';
        $head .= '<link rel="stylesheet" type="text/css" href="/admin/view/css/bootstrap/bootstrap.css">';
        $head .= '<link rel="stylesheet" type="text/css" href="/admin/view/css/style.css">';
        $head .= '<link rel="stylesheet" type="text/css" href="/admin/view/fonts/awesome.css">';
        $head .= '<style>.accordion.accordion-semi .panel-heading a.collapsed { color: #FFF; }</style>';
        $head .= '<script type="text/javascript" src="/admin/view/js/jquery/jquery.js"></script>';
        $head .= '<script src="/admin/view/js/bootstrap/bootstrap.js"></script>';
        $extra .= '<script type="text/javascript">$(function(){$("#error").trigger("click");});</script>';
        $content = str_replace('</head>', $head . '</head>', $content);
        exit('<!-- ' . date('Y-m-d H:i:s') . '-->' . str_replace('</body>', $extra . '</body>', $content));
    }

    return str_replace('</body>', $extra . '</body>', $content);
}

function output_buffer($content)
{
    if (defined('DEVELOPMENT')) {
        $content = development_debug($content);
    }
    return $content;
}

error_reporting(-1);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 1);
register_shutdown_function('error_handler_last_resort');
set_error_handler('error_handler');
set_exception_handler('exception_handler');
