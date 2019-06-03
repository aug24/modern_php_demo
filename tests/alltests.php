<?php
require_once('vendor/simpletest/simpletest/autorun.php');

class AllTests extends TestSuite
{

    private $directory = "tests";

    public function allTests()
    {
        $tests = array_diff(scandir($this->directory), array('..', '.', 'alltests.php'));
        foreach ($tests as $file) {
            $this->addFile($this->directory . "/" . $file);
        }
    }
}
