<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository
{
    private $users;

    public function __construct(User $user)
    {
        $this->users = $user;
    }

    public function getUserByEmail($email)
    {
        return $this->users->where('email', $email)->first();
    }

    public function createGoogleUser($request)
    {
        $data = [];
        $data['firstname'] = $request['given_name'];
        $data['lastname'] = $request['family_name'];
        $data['image_url'] = $request['picture'];
        $data['email'] = $request['email'];
        $data['google_id'] = $request['id'];
        $user = $this->users->create($data);
        return $user;
    }
}
