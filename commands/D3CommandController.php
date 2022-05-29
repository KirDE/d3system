<?php

namespace d3system\commands;

use Yii;
use yii\console\Controller;
use yii\db\Connection;

/**
 *
 * @property Connection $connection
 */
class D3CommandController extends Controller
{

    /**
     * @var bool
     */
    public $debug = false;
    
    public $saveLog;
    public $logContent;
    
    protected $startedTime;
    
    public function beforeAction($action)
    {
        $this->out('');
        $this->out('==================');
        $this->out('Route: ' . $this->route);
        $argv = $_SERVER['argv'];
        unset($argv[0],$argv[1]);
        if($argv) {
            $this->out('Arguments:');
            foreach ($argv as $argValue) {
                $this->out(' ' . $argValue);
            }
        }
        $this->out('Action: ' . $action->actionMethod);
        $this->out('Class: ' . get_class($action->controller));
        $this->out('------------------');
        $this->startedTime = date('Y-m-d H:i:s');
        $this->out('Started: ' . $this->startedTime);
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result)
    {
        $this->out('Started: ' . $this->startedTime);
        $this->out('Finished: ' . date('Y-m-d H:i:s'));
        $this->out('==================');
        $this->out('');
        return parent::afterAction($action, $result);
        
        if ($this->saveLog) {
            $this->writeLog();
        }

    }
    
    /**
     *
     */
    protected function writeLog():void
    {
        file_put_contents($this->saveLog, $this->logContent, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return Yii::$app->getDb();
    }

    /**
     * output to terminal line
     * @param string $string output string
     * @param int $settings
     */
    public function out(string $string, int $settings = 0): void
    {
        $this->stdout($string . PHP_EOL, $settings);
        
        if ($this->saveLog) {
            $this->logContent .= $string . PHP_EOL;
        }
    }

    /**
     * output messages list
     * @param array $list
     * @param int $settings
     */
    public function outList(array $list, int $settings = 0): void
    {
        foreach($list as $string){
            $this->stdout($string . PHP_EOL, $settings);
        }
    }

    public function debug(string $string, int $settings = 0): void
    {
        if($this->debug){
            $this->out('DEBUG: ' . $string, $settings);
        }
    }
}

