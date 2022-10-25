<?php

namespace App\Controller;

use App\Model\UserManager;

class UserController extends AbstractController
{
    public function login(): string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credentials = array_map('trim', $_POST);
            //      @todo make some controls on email and password fields and if errors, send them to the view
            if (isset($credentials['email']) && !empty($credentials['email'])) {
                if (isset($credentials['password']) && !empty($credentials['password'])) {
                    $userManager = new UserManager();
                    $user = $userManager->selectOneByEmail($credentials['email']);
                    var_dump($user);
                    if ($user && password_verify($credentials['password'], $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        header('Location: /');
                        exit();
                    } else {
                        return $errors['wrongPasword'] = 'Wrong password';
                    }
                } else {
                    return $errors['noPasword'] = 'No password enttered';
                }
            } else {
                return $errors['noEmail'] = 'No email entered';
            }
        }
        return $this->twig->render('User/login.html.twig');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        session_destroy();
        header('Location: /');
    }

    public function register(): string
    {
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newUser = array_map('trim', $_POST);
            var_dump($newUser);
            if (!isset($newUser['email']) || empty($newUser['email'])) {
                return $errors['emptyEmail'] = 'Please enter an email';
            }
            if (!isset($newUser['password']) || empty($newUser['password'])) {
                return $errors['emptyEmail'] = 'Please enter an email';
            } else {
                $passwordToHash = $newUser['password'];
                $hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);
            }
            if (!isset($newUser['pseudo']) || empty($newUser['pseudo'])) {
                return $errors['emptyPseudo'] = 'Please enter a pseudo';
            }
            if (!isset($newUser['firstname']) || empty($newUser['firstname'])) {
                return $errors['emptyFirstname'] = 'Please enter a firstname';
            }
            if (!isset($newUser['lastname']) || empty($newUser['lastname'])) {
                return $errors['emptyLastname'] = 'Please enter a lastname';
            }
        }
        return $this->twig->render('User/register.html.twig');
    }
}
