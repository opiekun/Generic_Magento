<?php

declare(strict_types=1);

namespace Ecommerce121\TigTax\Model;

class RangeManager
{
    /**
     * Input: 19,4,3,16,7,15,2,18,1
     * Output: [
     *     '1-4',
     *     '7-7',
     *     '15-19',
     * ]
     *
     * @param int[] $numbers
     * @return array
     */
    public function getRanges(array $numbers): array
    {
        sort($numbers);
        if ($this->isSequenceWithoutGaps($numbers)) {
            return [reset($numbers) . '-' . end($numbers)];
        }

        $rangePool = [];
        $firstInRange = false;
        foreach ($numbers as $key => $number) {
            if (!$firstInRange) {
                $firstInRange = $number;
            }

            if ($this->isItSequenceEnd((int) $number, (int) ($numbers[$key + 1] ?? 0))) {
                $rangePool[] = $firstInRange . '-' . $number;
                $firstInRange = false;
            }
        }

        return $rangePool;
    }

    /**
     * @param array $numbers
     * @return bool
     */
    private function isSequenceWithoutGaps(array $numbers): bool
    {
        $min = (int) reset($numbers);
        $max = (int) end($numbers);
        return ($max === $min) || (($max - $min) === 1);
    }

    /**
     * @param int $currentNumber
     * @param int $nextNumber
     * @return bool
     */
    private function isItSequenceEnd(int $currentNumber, int $nextNumber): bool
    {
        return (!$nextNumber) || (($currentNumber + 1) !== $nextNumber);
    }
}
