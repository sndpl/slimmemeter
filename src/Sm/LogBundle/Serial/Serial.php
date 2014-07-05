<?php
namespace Sm\LogBundle\Serial;

/**
 * Serial port control class
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARRANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * @author Rémy Sanchez <remy.sanchez@hyperthese.net>
 * @author Rizwan Kassim <rizwank@geekymedia.com>
 * @thanks Aurélien Derouineau for finding how to open serial ports with windows
 * @thanks Alec Avedisyan for help and testing with reading
 * @thanks Jim Wright for OSX cleanup/fixes.
 * @copyright under GPL 2 licence
 */
class Serial
{

    const SERIAL_DEVICE_NOT_SET = 0;
    const SERIAL_DEVICE_SET = 1;
    const SERIAL_DEVICE_OPENED = 2;

    /**
     * @var string
     */
    protected $device = null;

    /**
     * @var string
     */
    protected $winDevice = null;

    /**
     * @var resource
     */
    protected $deviceHandle = null;

    /**
     * @var int
     */
    protected $deviceState = self::SERIAL_DEVICE_NOT_SET;

    /**
     * @var string
     */
    protected $buffer = "";

    /**
     * @var string
     */
    protected $os = "";

    /**
     * This var says if buffer should be flushed by sendMessage (true) or
     * manually (false)
     *
     * @var bool
     */
    public $autoFlush = true;

    /**
     * Constructor. Perform some checks about the OS and setserial
     *
     * @return Serial
     */
    public function __construct()
    {
        setlocale(LC_ALL, "en_US");

        $sysName = php_uname();

        if (substr($sysName, 0, 5) === "Linux") {
            $this->os = "linux";

            if ($this->exec("stty --version") === 0) {
                register_shutdown_function(array($this, "deviceClose"));
            } else {
                trigger_error(
                    "No stty availible, unable to run.",
                    E_USER_ERROR
                );
            }
        } elseif (substr($sysName, 0, 6) === "Darwin") {
            $this->os = "osx";
            register_shutdown_function(array($this, "deviceClose"));
        } elseif (substr($sysName, 0, 7) === "Windows") {
            $this->os = "windows";
            register_shutdown_function(array($this, "deviceClose"));
        } else {
            trigger_error("Host OS is neither osx, linux nor windows, unable " .
                "to run.", E_USER_ERROR);
            exit();
        }
    }

    //
    // OPEN/CLOSE DEVICE SECTION -- {START}
    //

    /**
     * Device set function : used to set the device name/address.
     * -> linux : use the device address, like /dev/ttyS0
     * -> osx : use the device address, like /dev/tty.serial
     * -> windows : use the COMxx device name, like COM1 (can also be used
     *     with linux)
     *
     * @param  string $device the name of the device to be used
     * @return bool
     */
    public function deviceSet($device)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_OPENED) {
            if ($this->os === "linux") {
                if (preg_match("@^USB(\\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyUSB" . ($matches[1] - 1);
                } elseif (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
                    $device = "/dev/ttyS" . ($matches[1] - 1);
                }

                if ($this->exec("stty -F " . $device) === 0) {
                    $this->device = $device;
                    $this->deviceState = self::SERIAL_DEVICE_SET;

                    return true;
                }
            } elseif ($this->os === "osx") {
                echo "SLIM - $device";
                if ($this->exec("stty -f " . $device) === 0) {
                    $this->device = $device;
                    $this->deviceState = self::SERIAL_DEVICE_SET;

                    return true;
                }
            } elseif ($this->os === "windows") {
                if (preg_match("@^COM(\\d+):?$@i", $device, $matches)
                    and $this->exec(
                        exec("mode " . $device . " xon=on BAUD=9600")
                    ) === 0
                ) {
                    $this->winDevice = "COM" . $matches[1];
                    $this->device = "\\.com" . $matches[1];
                    $this->deviceState = self::SERIAL_DEVICE_SET;

                    return true;
                }
            }

            trigger_error("Specified serial port (" . $device . ") is not valid", E_USER_WARNING);

            return false;
        } else {
            trigger_error("You must close your device before to set an other " .
                "one", E_USER_WARNING);

            return false;
        }
    }

    /**
     * Opens the device for reading and/or writing.
     *
     * @param  string $mode Opening mode : same parameter as fopen()
     * @return bool
     */
    public function deviceOpen($mode = "r+b")
    {
        if ($this->deviceState === self::SERIAL_DEVICE_OPENED) {
            trigger_error("The device is already opened", E_USER_NOTICE);

            return true;
        }

        if ($this->deviceState === self::SERIAL_DEVICE_NOT_SET) {
            trigger_error(
                "The device must be set before to be open",
                E_USER_WARNING
            );

            return false;
        }

        if (!preg_match("@^[raw]\\+?b?$@", $mode)) {
            trigger_error(
                "Invalid opening mode : " . $mode . ". Use fopen() modes.",
                E_USER_WARNING
            );

            return false;
        }

        $this->deviceHandle = @fopen($this->device, $mode);

        if ($this->deviceHandle !== false) {
            stream_set_blocking($this->deviceHandle, 0);
            // Serial-over-USB (few have a genuine serial port anymore) such as the FTDI driver (used in Arduinos, and a bunch of other boards)
            // uses 64 byte packets to transport over USB ... therefore reading chunk sizes that aren't divisble by 64 may result in delay
            stream_set_chunk_size($this->deviceHandle, 64);
            $this->deviceState = self::SERIAL_DEVICE_OPENED;

            return true;
        }

        $this->deviceHandle = null;
        trigger_error("Unable to open the device", E_USER_WARNING);

        return false;
    }

    /**
     * Closes the device
     *
     * @return bool
     */
    public function deviceClose()
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_OPENED) {
            return true;
        }

        if (fclose($this->deviceHandle)) {
            $this->deviceHandle = null;
            $this->deviceState = self::SERIAL_DEVICE_SET;

            return true;
        }

        trigger_error("Unable to close the device", E_USER_ERROR);

        return false;
    }

    //
    // OPEN/CLOSE DEVICE SECTION -- {STOP}
    //

    //
    // CONFIGURE SECTION -- {START}
    //

    /**
     * Configure the Baud Rate
     * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
     * 57600 and 115200.
     *
     * @param  int $rate the rate to set the port in
     * @return bool
     */
    public function confBaudRate($rate)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the baud rate : the device is " .
                "either not set or opened", E_USER_WARNING);

            return false;
        }

        $validBauds = array(
            110 => 11,
            150 => 15,
            300 => 30,
            600 => 60,
            1200 => 12,
            2400 => 24,
            4800 => 48,
            9600 => 96,
            19200 => 19,
            38400 => 38400,
            57600 => 57600,
            115200 => 115200
        );

        if (isset($validBauds[$rate])) {
            if ($this->os === "linux") {
                $ret = $this->exec(
                    "stty -F " . $this->device . " " . (int)$rate,
                    $out
                );
            } elseif ($this->os === "osx") {
                $ret = $this->exec(
                    "stty -f " . $this->device . " " . (int)$rate,
                    $out
                );
            } elseif ($this->os === "windows") {
                $ret = $this->exec(
                    "mode " . $this->winDevice . " BAUD=" . $validBauds[$rate],
                    $out
                );
            } else {
                return false;
            }

            if ($ret !== 0) {
                trigger_error(
                    "Unable to set baud rate: " . $out[1],
                    E_USER_WARNING
                );

                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Configure parity.
     * Modes : odd, even, none
     *
     * @param  string $parity one of the modes
     * @return bool
     */
    public function confParity($parity)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_SET) {
            trigger_error(
                "Unable to set parity : the device is either not set or opened",
                E_USER_WARNING
            );

            return false;
        }

        $args = array(
            "none" => "-parenb",
            "odd" => "parenb parodd",
            "even" => "parenb -parodd",
        );

        if (!isset($args[$parity])) {
            trigger_error("Parity mode not supported", E_USER_WARNING);

            return false;
        }

        if ($this->os === "linux") {
            $ret = $this->exec(
                "stty -F " . $this->device . " " . $args[$parity],
                $out
            );
        } elseif ($this->os === "osx") {
            $ret = $this->exec(
                "stty -f " . $this->device . " " . $args[$parity],
                $out
            );
        } else {
            $ret = $this->exec(
                "mode " . $this->winDevice . " PARITY=" . $parity{0},
                $out
            );
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error("Unable to set parity : " . $out[1], E_USER_WARNING);

        return false;
    }

    /**
     * Sets the length of a character.
     *
     * @param  int $int length of a character (5 <= length <= 8)
     * @return bool
     */
    public function confCharacterLength($int)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_SET) {
            trigger_error("Unable to set length of a character : the device " .
                "is either not set or opened", E_USER_WARNING);

            return false;
        }

        $int = (int)$int;
        if ($int < 5) {
            $int = 5;
        } elseif ($int > 8) {
            $int = 8;
        }

        if ($this->os === "linux") {
            $ret = $this->exec(
                "stty -F " . $this->device . " cs" . $int,
                $out
            );
        } elseif ($this->os === "osx") {
            $ret = $this->exec(
                "stty -f " . $this->device . " cs" . $int,
                $out
            );
        } else {
            $ret = $this->exec(
                "mode " . $this->winDevice . " DATA=" . $int,
                $out
            );
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error(
            "Unable to set character length : " . $out[1],
            E_USER_WARNING
        );

        return false;
    }

    /**
     * Sets the length of stop bits.
     *
     * @param  float $length the length of a stop bit. It must be either 1,
     *                       1.5 or 2. 1.5 is not supported under linux and on
     *                       some computers.
     * @return bool
     */
    public function confStopBits($length)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_SET) {
            trigger_error("Unable to set the length of a stop bit : the " .
                "device is either not set or opened", E_USER_WARNING);

            return false;
        }

        if ($length != 1
            and $length != 2
            and $length != 1.5
            and !($length == 1.5 and $this->os === "linux")
        ) {
            trigger_error(
                "Specified stop bit length is invalid",
                E_USER_WARNING
            );

            return false;
        }

        if ($this->os === "linux") {
            $ret = $this->exec(
                "stty -F " . $this->device . " " .
                (($length == 1) ? "-" : "") . "cstopb",
                $out
            );
        } elseif ($this->os === "osx") {
            $ret = $this->exec(
                "stty -f " . $this->device . " " .
                (($length == 1) ? "-" : "") . "cstopb",
                $out
            );
        } else {
            $ret = $this->exec(
                "mode " . $this->winDevice . " STOP=" . $length,
                $out
            );
        }

        if ($ret === 0) {
            return true;
        }

        trigger_error(
            "Unable to set stop bit length : " . $out[1],
            E_USER_WARNING
        );

        return false;
    }

    /**
     * Configures the flow control
     *
     * @param  string $mode Set the flow control mode. Availible modes :
     *                      -> "none" : no flow control
     *                      -> "rts/cts" : use RTS/CTS handshaking
     *                      -> "xon/xoff" : use XON/XOFF protocol
     * @return bool
     */
    public function confFlowControl($mode)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_SET) {
            trigger_error("Unable to set flow control mode : the device is " .
                "either not set or opened", E_USER_WARNING);

            return false;
        }

        $linuxModes = array(
            "none" => "clocal -crtscts -ixon -ixoff",
            "rts/cts" => "-clocal crtscts -ixon -ixoff",
            "xon/xoff" => "-clocal -crtscts ixon ixoff"
        );
        $windowsModes = array(
            "none" => "xon=off octs=off rts=on",
            "rts/cts" => "xon=off octs=on rts=hs",
            "xon/xoff" => "xon=on octs=off rts=on",
        );

        if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
            trigger_error("Invalid flow control mode specified", E_USER_ERROR);

            return false;
        }

        if ($this->os === "linux") {
            $ret = $this->exec(
                "stty -F " . $this->device . " " . $linuxModes[$mode],
                $out
            );
        } elseif ($this->os === "osx") {
            $ret = $this->exec(
                "stty -f " . $this->device . " " . $linuxModes[$mode],
                $out
            );
        } else {
            $ret = $this->exec(
                "mode " . $this->winDevice . " " . $windowsModes[$mode],
                $out
            );
        }

        if ($ret === 0) {
            return true;
        } else {
            trigger_error(
                "Unable to set flow control : " . $out[1],
                E_USER_ERROR
            );

            return false;
        }
    }

    /**
     * Sets a setserial parameter (cf man setserial)
     * NO MORE USEFUL !
     *    -> No longer supported
     *    -> Only use it if you need it
     *
     * @param  string $param parameter name
     * @param  string $arg parameter value
     * @return bool
     */
    public function setSetserialFlag($param, $arg = "")
    {
        if (!$this->checkOpened()) {
            return false;
        }

        $return = exec(
            "setserial " . $this->device . " " . $param . " " . $arg . " 2>&1"
        );

        if ($return{0} === "I") {
            trigger_error("setserial: Invalid flag", E_USER_WARNING);

            return false;
        } elseif ($return{0} === "/") {
            trigger_error("setserial: Error with device file", E_USER_WARNING);

            return false;
        } else {
            return true;
        }
    }

    //
    // CONFIGURE SECTION -- {STOP}
    //

    //
    // I/O SECTION -- {START}
    //

    /**
     * Sends a string to the device
     *
     * @param string $str string to be sent to the device
     * @param float $waitForReply time to wait for the reply (in seconds)
     */
    public function sendMessage($str, $waitForReply = 0.1)
    {
        $this->buffer .= $str;

        if ($this->autoFlush === true) {
            $this->serialflush();
        }

        usleep((int)($waitForReply * 1000000));
    }

    /**
     * Reads the port until no new datas are availible, then return the content.
     *
     * @param int $count Number of characters to be read (will stop before
     *                   if less characters are in the buffer)
     * @return string
     */
    public function readPort($count = 0)
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be opened to read it", E_USER_WARNING);

            return false;
        }

        if ($this->os === "linux" || $this->os === "osx") {
            // Behavior in OSX isn't to wait for new data to recover, but just
            // grabs what's there!
            // Doesn't always work perfectly for me in OSX
            $content = "";
            $i = 0;

            if ($count !== 0) {
                do {
                    if ($i > $count) {
                        $content .= fread($this->deviceHandle, ($count - $i));
                    } else {
                        $content .= fread($this->deviceHandle, 128);
                    }
                } while (($i += 128) === strlen($content));
            } else {
                do {
                    $content .= fread($this->deviceHandle, 128);
                } while (($i += 128) === strlen($content));
            }

            return $content;
        } elseif ($this->os === "windows") {
            // Windows port reading procedures still buggy
            $content = "";
            $i = 0;

            if ($count !== 0) {
                do {
                    if ($i > $count) {
                        $content .= fread($this->deviceHandle, ($count - $i));
                    } else {
                        $content .= fread($this->deviceHandle, 128);
                    }
                } while (($i += 128) === strlen($content));
            } else {
                do {
                    $content .= fread($this->deviceHandle, 128);
                } while (($i += 128) === strlen($content));
            }

            return $content;
        }

        return false;
    }

    /**
     * Reads a line from the serial port
     *
     * @param int $max_length optional maximum number of bytes to read ... will read up to this size, or until end of line or until end of stream
     * @param String $delimiter optional deliminator/end of line char(s)
     *
     * @return bool|string
     */
    public function readPortLine($max_length=10000, $delimiter="\n")
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be opened to read it", E_USER_WARNING);
            return false;
        }

        return stream_get_line($this->deviceHandle, $max_length, $delimiter);
    }

    /**
     * Flushes the output buffer
     * Renamed from flush for osx compat. issues
     *
     * @return bool
     */
    public function serialflush()
    {
        if (!$this->checkOpened()) {
            return false;
        }

        if (fwrite($this->deviceHandle, $this->buffer) !== false) {
            $this->buffer = "";

            return true;
        } else {
            $this->buffer = "";
            trigger_error("Error while sending message", E_USER_WARNING);

            return false;
        }
    }

    //
    // I/O SECTION -- {STOP}
    //

    //
    // INTERNAL TOOLKIT -- {START}
    //

    protected function checkOpened()
    {
        if ($this->deviceState !== self::SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be opened", E_USER_WARNING);

            return false;
        }

        return true;
    }

    protected function checkClosed()
    {
        if ($this->deviceState === self::SERIAL_DEVICE_OPENED) {
            trigger_error("Device must be closed", E_USER_WARNING);

            return false;
        }

        return true;
    }

    protected function exec($cmd, &$out = null)
    {
        $desc = array(
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );

        $proc = proc_open($cmd, $desc, $pipes);

        $ret = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $retVal = proc_close($proc);

        if (func_num_args() == 2) {
            $out = array($ret, $err);
        }
        return $retVal;
    }

    //
    // INTERNAL TOOLKIT -- {STOP}
    //
}
