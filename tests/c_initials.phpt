<?php

namespace Dakujem;

require_once 'test_suite_bootstrap.php';

final class InitialsTest extends ShortsBaseTest
{

    function testInitials()
    {
        $this->assertInitials('FB', 'Foo Bar');
        $this->assertInitials('HVR', 'Hugo Ventil Rypák');
        $this->assertInitials('PEEG', 'Pablo Emilio Escobar Gaviria');

        $this->assertInitials('F.B.', 'Foo Bar', '.');
        $this->assertInitials('H. V. R.', 'Hugo Ventil Rypák', '.', ' ');
        $this->assertInitials('P^^-E^^-E^^-G^^', 'Pablo Emilio Escobar Gaviria', '^^', '-');
    }

}


(new InitialsTest())->run();

