<?hh
/*
 *  Copyright (c) 2014, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the BSD-style license found in the
 *  LICENSE file in the root directory of this source tree. An additional grant
 *  of patent rights can be found in the PATENTS file in the same directory.
 *
 */

require_once('NoEngineStats.php');
require_once('PerfOptions.php');
require_once('PerfSettings.php');
require_once('PHPEngine.php');

final class PHP5Daemon extends PHPEngine {
  use NoEngineStats;

  private PerfTarget $target;

  public function __construct(
    private PerfOptions $options,
  ) {
    $this->target = $options->getTarget();
    parent::__construct((string) $options->php5);
  }

  public function start(): void {
    parent::startWorker(
      $this->options->daemonOutputFileName('php5'),
      $this->options->delayProcessLaunch,
      $this->options->traceSubProcess,
    );
  }

  protected function getArguments(): Vector<string> {
    return Vector {
      '-b', '127.0.0.1:'.PerfSettings::FastCGIPort(),
      '-c', __DIR__,
    };
  }

  protected function getEnvironmentVariables(): Map<string, string> {
    return Map {
      'PHP_FCGI_CHILDREN' => '60',
      'PHP_FCGI_MAX_REQUESTS' => '0',
    };
  }
}
