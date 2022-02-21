<?php
namespace Rootways\Canadapost\Model\Method;

use Psr\Log\LoggerInterface;

class Logger
{
    const DEBUG_KEYS_MASK = '****';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Logs payment related information used for debug
     *
     * @param array $data
     * @param array|null $maskKeys
     * @param bool|null $forceDebug
     * @return void
     */
    public function debug(array $data, array $maskKeys = null, $forceDebug = null)
    {
        $maskKeys = $this->getDebugReplaceFields($maskKeys);
        //$debugOn = $forceDebug !== null ? $forceDebug : (bool)$this->customHelper->getConfig('payment/rootways_canadapost_general/debug');
        $debugOn = true;
        if ($debugOn === true) {
            $data = $this->filterDebugData(
                $data,
                $maskKeys
            );
            $this->logger->debug(var_export($data, true));
        }
    }

    /**
     * Returns configured keys to be replaced with mask
     *
     * @return array
     */
    private function getDebugReplaceFields($maskKeys)
    {
        $globalReplaceFiled = array('customer-number', 'contract-id', 'image');
        $maskKeys = $maskKeys !== null ? $maskKeys : array();
        $replaceFiled = array_merge($globalReplaceFiled, $maskKeys);

        return $replaceFiled;
    }

    /**
     * Recursive filter data by private conventions
     *
     * @param array $debugData
     * @param array $debugReplacePrivateDataKeys
     * @return array
     */
    protected function filterDebugData(array $debugData, array $debugReplacePrivateDataKeys)
    {
        $debugReplacePrivateDataKeys = array_map('strtolower', $debugReplacePrivateDataKeys);

        foreach (array_keys($debugData) as $key) {
            if (in_array(strtolower($key), $debugReplacePrivateDataKeys)) {
                $debugData[$key] = self::DEBUG_KEYS_MASK;
            } elseif (is_array($debugData[$key])) {
                $debugData[$key] = $this->filterDebugData($debugData[$key], $debugReplacePrivateDataKeys);
            }
        }
        return $debugData;
    }
}
