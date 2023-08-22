<?php

namespace App\DataFixtures\Provider;

class AppProvider
{
    private $user = [
        "admin" => [
            "name" => "Admin",
            "email" => "admin@oclock.io",
            "password" => "admin",
            "roles" => ["ROLE_ADMIN"]
            
        ],

        "user" => [
            "name" => "User",
            "email" => "user@oclock.io",
            "password" => "user",
            "roles" => [],
            
        ]
    ];

    private $game = [
        "game1" => [
            "name" => "Game 1"
        ],

        "game2" => [
            "name" => "Game 2"
        ],

        "game3" => [
            "name" => "Game 3"
        ],

    ];

     /**
     * Get a a user, available roles : admin, user
     * @param string the role of the user
     * @return array a role
     */
    public function user($role = null)
    {
        if ($role) {
            return $this->user[$role];
        }
        return $this->user[array_rand($this->user)];
    }

    /**
     * Get a game
     * @return array a role
     */
    public function game()
    {
        
        return $this->game[array_rand($this->game)];
    }


}