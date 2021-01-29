<?php

/**
 * WooCommerce Logger: WooCommerceLogger class.
 *
 * @package WPDesk\WooCommerceShipping
 */
namespace DhlVendor\WPDesk\WooCommerceShipping;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
/**
 * Wants to show all logs using wc_add_notice
 */
class DisplayNoticeLogger implements \Psr\Log\LoggerInterface
{
    const WC_NOTICE = 'notice';
    const WC_ERROR = 'error';
    use LoggerTrait;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $service_name;
    /**
     * DisplayLogs constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger Logger.
     * @param string $service_name .
     */
    public function __construct(\Psr\Log\LoggerInterface $logger, $service_name)
    {
        $this->logger = $logger;
        $this->service_name = $service_name;
    }
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level   Level.
     * @param string $message Message.
     * @param array  $context context.
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
        if (\in_array($level, [\Psr\Log\LogLevel::DEBUG, \Psr\Log\LogLevel::INFO], \true)) {
            $this->show($message, $context, self::WC_NOTICE);
        } else {
            $this->show($message, $context, self::WC_ERROR);
        }
    }
    /**
     * Show notices
     *
     * @param string $message Message.
     * @param array  $context context.
     * @param string $type    Type.
     *
     * @return void
     */
    private function show($message, array $context, $type)
    {
        $message = \sprintf('%1$s: %2$s', $this->service_name, $message);
        $dump = '';
        foreach ($context as $label => $value) {
            if (!\is_string($value)) {
                $value = \print_r($value, \true);
            }
            \ob_start();
            include __DIR__ . '/view/display-notice-context-single-value.php';
            $dump .= \ob_get_clean();
        }
        if (!\wc_has_notice($message . $dump, $type)) {
            \wc_add_notice($message . $dump, $type);
        }
    }
}
