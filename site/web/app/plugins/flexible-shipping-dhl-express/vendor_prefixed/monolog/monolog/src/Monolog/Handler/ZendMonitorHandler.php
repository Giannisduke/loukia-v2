<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace DhlVendor\Monolog\Handler;

use DhlVendor\Monolog\Formatter\NormalizerFormatter;
use DhlVendor\Monolog\Logger;
/**
 * Handler sending logs to Zend Monitor
 *
 * @author  Christian Bergau <cbergau86@gmail.com>
 * @author  Jason Davis <happydude@jasondavis.net>
 */
class ZendMonitorHandler extends \DhlVendor\Monolog\Handler\AbstractProcessingHandler
{
    /**
     * Monolog level / ZendMonitor Custom Event priority map
     *
     * @var array
     */
    protected $levelMap = array();
    /**
     * Construct
     *
     * @param  int                       $level
     * @param  bool                      $bubble
     * @throws MissingExtensionException
     */
    public function __construct($level = \DhlVendor\Monolog\Logger::DEBUG, $bubble = \true)
    {
        if (!\function_exists('DhlVendor\\zend_monitor_custom_event')) {
            throw new \DhlVendor\Monolog\Handler\MissingExtensionException('You must have Zend Server installed with Zend Monitor enabled in order to use this handler');
        }
        //zend monitor constants are not defined if zend monitor is not enabled.
        $this->levelMap = array(\DhlVendor\Monolog\Logger::DEBUG => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_INFO, \DhlVendor\Monolog\Logger::INFO => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_INFO, \DhlVendor\Monolog\Logger::NOTICE => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_INFO, \DhlVendor\Monolog\Logger::WARNING => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_WARNING, \DhlVendor\Monolog\Logger::ERROR => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR, \DhlVendor\Monolog\Logger::CRITICAL => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR, \DhlVendor\Monolog\Logger::ALERT => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR, \DhlVendor\Monolog\Logger::EMERGENCY => \DhlVendor\ZEND_MONITOR_EVENT_SEVERITY_ERROR);
        parent::__construct($level, $bubble);
    }
    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $this->writeZendMonitorCustomEvent(\DhlVendor\Monolog\Logger::getLevelName($record['level']), $record['message'], $record['formatted'], $this->levelMap[$record['level']]);
    }
    /**
     * Write to Zend Monitor Events
     * @param string $type Text displayed in "Class Name (custom)" field
     * @param string $message Text displayed in "Error String"
     * @param mixed $formatted Displayed in Custom Variables tab
     * @param int $severity Set the event severity level (-1,0,1)
     */
    protected function writeZendMonitorCustomEvent($type, $message, $formatted, $severity)
    {
        zend_monitor_custom_event($type, $message, $formatted, $severity);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultFormatter()
    {
        return new \DhlVendor\Monolog\Formatter\NormalizerFormatter();
    }
    /**
     * Get the level map
     *
     * @return array
     */
    public function getLevelMap()
    {
        return $this->levelMap;
    }
}
