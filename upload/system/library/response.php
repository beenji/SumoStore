<?php
class Response
{
    private $headers = array();
    private $level = 0;
    private $output;

    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    public function setCompression($level)
    {
        $this->level = $level;
    }

    public function setOutput($output)
    {
        //Sumo\Logger::info('Output set, strlen: ' . strlen($output));
        $test = @json_decode($output, true);
        if (is_array($test) && $test) {
            //$this->addHeader('Content-type: text/json');
        }
        $this->output = $output;
    }

    private function compress($data, $level = 0)
    {
        $this->addHeader('X-Test: blub');
        return $data;
        if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            return $data;
        }

        if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
            $encoding = 'x-gzip';
        } elseif (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            $encoding = 'gzip';
        } else {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        $this->addHeader('Content-Encoding: ' . $encoding);

        return gzencode($data, (int) $level);
    }

    public function output($return = false)
    {
        /*
        $output = $this->output;
        $test = @json_decode($output, true);
        if (is_array($test)) {
            if ($return) {
                return $output;
            }
            echo $output;
            return;
        }
        */
        if ($this->output) {
            if ($this->level) {
                $output = $this->output; //$this->compress($this->output, $this->level);
            } else {
                $output = $this->output;
            }

            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }
            if ($return) {
                return $output;
            }
            echo $output;
        }
        else {
            echo '[empty_output]';
        }
    }
}
