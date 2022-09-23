<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Flag;

class Locker extends Flag
{
    /**
     * @var string
     */
    protected $_flagCode = 'tigtax-integrating-lock';

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function isLocked(): bool
    {
        $this->loadSelf();
        return (bool) ($this->getFlagData()['locked'] ?? false);
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getTime(): string
    {
        $this->loadSelf();
        return (string) ($this->getFlagData()['time'] ?? '');
    }

    /**
     * @throws \Exception
     */
    public function lock()
    {
        $time = time();

        $this->setFlagData([
            'time' => $time,
            'locked' => 1
        ])->save();
    }

    /**
     * @throws \Exception
     */
    public function unlock()
    {
        $this->setFlagData([
            'time' => $this->getTime(),
            'locked' => 0
        ])->save();
    }
}
