<?php
/**
 * This file is part of the TestingBundle project.
 *
 * @project    TestingBundle
 * @author     Cosmin Voicu <cosmin.voicu@oconotech.com>
 * @copyright  2016 - ocono Tech GmbH
 * @license    http://www.ocono-tech.com proprietary
 * @link       http://www.ocono-tech.com
 * @date       08/02/16
 */

namespace Cosma\Bundle\TestingBundle\TestCase\Traits;

trait RetryTrait
{


    /*
     * {@inheritdoc}
     *
     */
    public function runBare()
    {
        for ($i = 0; $i <= $this->getNumberOfRetries(); $i++) {
            try {
                if ($i > 0) {
                    //purple on yellow background colour
                    echo "\033[35m\033[43mR\033[0m";
                }
                parent::runBare();
                parent::runTest();


                return;
            } catch (\Exception $exception) {
            }
        }
        if ($exception) {
            throw $exception;
        }
    }

    /**
     * @return int
     */
    private function getNumberOfRetries()
    {
        $annotations = $this->getAnnotations();

        if (isset($annotations['method']['retry'])) {
            if (
                isset($annotations['method']['retry'][0]) &&
                is_numeric($annotations['method']['retry'][0])

            ) {
                return $annotations['method']['retry'][0];
            }

            return 1;
        }

        if (isset($annotations['class']['retry'])) {
            if (
                isset($annotations['class']['retry'][0]) &&
                is_numeric($annotations['class']['retry'][0])

            ) {
                return $annotations['class']['retry'][0];
            }

            return 1;
        }

        return 0;
    }
    
}