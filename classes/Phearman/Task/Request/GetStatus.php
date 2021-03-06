<?php

namespace Phearman\Task\Request;
use Phearman\Phearman;
use Phearman\Task;

/**
 * Implements the GET_STATUS packet.
 *
 * A client issues this to get status information for a submitted job.
 *
 * Arguments:
 * - Job handle that was given in JOB_CREATED packet.
 *
 * @author Inash Zubair <inash@leptone.com>
 * @package Phearman
 * @subpackage Task\Request
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 */
class GetStatus extends Task
{
    protected $jobHandle;

    public function __construct($jobHandle)
    {
        $this->code = Phearman::CODE_REQUEST;
        $this->type = Phearman::TYPE_GET_STATUS;
        $this->jobHandle = $jobHandle;
    }

    protected function getDataPart()
    {
        return $this->jobHandle;
    }
}
