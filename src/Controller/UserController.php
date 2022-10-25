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


            //password regEx : Minimum eight characters, at least one upper case English letter, one lower case English letter, one number and one special character ("^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$""/^\S*(?=.*[a-z])(?=.*A-A)(?=.*\d)(?=\S*[\W])[a-zA-Z\d]{8,}\S*$/")
            $passwordRegex =  "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";

            // test for empty email field.
            if (!isset($newUser['email']) || empty($newUser['email'])) {
                $errors['emptyEmail'] = 'Please enter an email';

                // test for valid email address
            } elseif (filter_var($newUser['email'], FILTER_VALIDATE_EMAIL)) {

                //good to send email
                $newUser['email'] = ['email'];
            } else {
                $errors['badEmailPattern'] = 'Please enter a valid email address';
            }

            // test for empty password field
            if (!isset($newUser['password']) || empty($newUser['password'])) {
                $errors['emptyPassword'] = 'Please enter an password';

                // test for matching regEx password
            } elseif (preg_match($passwordRegex, $newUser['password'])) {

                // good to hash and send password (using bcrypt blowfish algorithm encryption)
                $passwordToHash = $newUser['password'];
                $hashedPassword = password_hash($passwordToHash, CRYPT_BLOWFISH);
                $newUser['password'] = $hashedPassword;
            } else {
                $errors['badPasswordPattern'] = 'Please enter a valid password : Minimum eight characters, at least one upper case English letter, one lower case English letter, one number and one special character';
            }

            // test for empty pseudo field
            if (!isset($newUser['pseudo']) || empty($newUser['pseudo'])) {
                $errors['emptyPseudo'] = 'Please enter a pseudo';

                //good to send pseudo
            } else {
                $newUser['pseudo'] = $newUser['pseudo'];
            }

            // test for empty firstname field
            if (!isset($newUser['firstname']) || empty($newUser['firstname'])) {
                $errors['emptyFirstname'] = 'Please enter a firstname';

                //good to send firstname
            } else {
                $newUser['firstname'] = $newUser['firstname'];
            }

            // test for empty lastname field
            if (!isset($newUser['lastname']) || empty($newUser['lastname'])) {
                $errors['emptyLastname'] = 'Please enter a lastname';

                //good to send lastname
            } else {
                $newUser['lastname'] = $newUser['lastname'];
            }

            if (count($errors) > 0) {
                return $this->twig->render('User/register.html.twig', ['errors' => $errors]);

                // if all good, send a createUser request
            } else {
                $insertUser = new UserManager();
                $insertUser->createUser($newUser);

                // user created, redirect to login method
                $this->login();
            }
        }
        return $this->twig->render('User/register.html.twig');
    }
}
