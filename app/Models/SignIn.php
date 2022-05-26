<?php namespace Valle\Models;

use Valle\Factorys\RepositoryFactory;

class SignIn
{
    /**
     * @var RepositoryFactory
     */
    protected $repository;

    public function __construct(RepositoryFactory $repository)
    {
        $this->repository = $repository;
    }

    public function existsUser(string $email)
    {
        $userRepo = $this->repository->get('users');

        $user = $userRepo->where("email=?", [$email]);
        
        if ($user) {
            return (object)$user[0] ?? false;
        }

        return false;
    }

    public function doSignIn(string $email, string $password)
    {
        $user = $this->existsUser($email);

        if ($user && password_verify($password, $user->password)) {
            unset($user->password);
            $_SESSION['auth'] = 'On';
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->name;
            $_SESSION['user_email'] = $user->email;
            
            return $user;
        }

        return false;
    }
}