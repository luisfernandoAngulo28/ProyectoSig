<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProjectTest extends TestCase {
    
    public function testDb() {
        $this->seeInDatabase('users', ['email' => 'edumejia30@gmail.com']);
    }

}