<?php
namespace MadeTech;

trait HttpSimulator
{
    /** @var string */
    protected static $domain = "localhost:47281";

    /** @var resource */
    private $simulator;

    protected function setUp()
    {
        parent::setUp();
        $this->startSimulator();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->endSimulator();
    }

    private function startSimulator()
    {
        $path = $this->getResponsesPath();
        `rm -Rf $path`;
        $path = $this->getRequestsPath();
        `rm -Rf $path`;
        $script = $this->getSimulatorPath() . 'http-simulator.php';
        $domain = self::$domain;
        $phpBinary = PHP_BINARY;
        $simulatorCommand = "$phpBinary -S {$domain} {$script}";
        $process = proc_open(
            $simulatorCommand,
            [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["file", "/tmp/error-output.txt", "a"],
            ],
            $pipes
        );
        $this->simulator = $process;
    }

    private function endSimulator()
    {
        proc_terminate($this->simulator);
    }

    /**
     * @return string
     */
    public function getSimulatorPath()
    {
        return __DIR__ . '/../../simulator/';
    }

    /**
     * @return string
     */
    public function getResponsesPath()
    {
        return $this->getSimulatorPath() . 'responses/';
    }

    /**
     * @param $fileData
     * @param $fileName
     */
    public function writeResponse($fileData, $fileName)
    {
        @mkdir($this->getResponsesPath(), 0777, true);
        file_put_contents($this->getResponsesPath() . $fileName, $fileData);
    }

    /**
     * @return string
     */
    private function getRequestsPath()
    {
        return $this->getSimulatorPath() . 'requests/';
    }
}
