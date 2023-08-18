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

}