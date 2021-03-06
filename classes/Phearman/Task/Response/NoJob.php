<?php

namespace Phearman\Task\Response;
use Phearman\Phearman;
use Phearman\Task;

/**
 * Implements the NO_JOB worker response packet.
 *
 * This is given in response to a GRAB_JOB request to notify the worker there
 * are no pending jobs that need to run.
 *
 * Arguments:
 * - None.
 *
 * @author Inash Zubair <inash@leptone.com>
 * @package Phearman
 * @subpackage Task\Response
 * @license http://www.opensource.org/licenses/BSD-3-Clause
 */
class NoJob extends Task
{
    public function __construct()
    {
        $this->code = Phearman::CODE_RESPONSE;
        $this->type = Phearman::TYPE_NO_JOB;
    }
}
