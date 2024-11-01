<?php

namespace Netjog\Utils;

/**
 * Class Alert
 * @package Netjog\Utils
 *
 * This alert class is responsible for storing and releasing alerts
 */
class Alert
{
    private static array $_runtimeAlerts = [];

    /**
     * Obtain the alerts
     * @return array
     */
    public static function obtain(): array
    {
        return self::$_runtimeAlerts;
    }

    /**
     * Store a notice in the session
     * @param string $message
     * @param string $type
     */
    public static function store(string $message, string $type = 'success'): void
    {
        // we're going to use options to store the alert for the next request
        if (self::ensureSession()){
            $_SESSION['netjog_virtualjog_alerts'][] = [
                'message' => $message,
                'type' => $type,
            ];

            // session close
            session_write_close();
        }else{
            // we're going to use options to store the alert
            $alerts = get_option('netjog_virtualjog_alerts', []);
            $alerts[] = [
                'message' => $message,
                'type' => $type,
            ];
            update_option('netjog_virtualjog_alerts', $alerts);
        }
    }

    /**
     * Release the alerts from the session or options
     */
    public static function release(): void
    {
        $alerts = [];

        if (self::ensureSession()){
            if (isset($_SESSION['netjog_virtualjog_alerts'])){
                $alerts = sanitize_text_field($_SESSION['netjog_virtualjog_alerts']);
                unset($_SESSION['netjog_virtualjog_alerts']);
            }

            // session close
            session_write_close();
        }

        $optionAlerts = get_option('netjog_virtualjog_alerts', []);
        $alerts = array_merge($alerts, $optionAlerts);

        self::$_runtimeAlerts = $alerts;
    }

    public static function ensureSession(): bool
    {
        // check if headers were already sent
        if (headers_sent()){
            return false;
        }

        // optimistic behavior
        $sessionStarted = true;

        // check if the session is started
        if (session_status() == PHP_SESSION_NONE) {
            $sessionStarted = session_start();
        }

        return $sessionStarted;
    }
}